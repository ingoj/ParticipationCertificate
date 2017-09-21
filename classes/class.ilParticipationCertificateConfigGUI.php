<?php
include_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');
include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePDFGenerator.php';
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePlugin.php');
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificate.php');
include_once './Services/Form/classes/class.ilPropertyFormGUI.php';

/**
 * Class ilParticipationCertificateConfigGUI
 *
 * @author       Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_Calls ilParticipationCertificateConfigGUI: ilParticipationCertificatePDFGenerator
 */
class ilParticipationCertificateConfigGUI extends ilPluginConfigGUI {

	const CMD_CONFIGURE = 'configure';
	const CMD_SAVE = 'save';
	const CMD_CANCEL = 'cancel';
	/**
	 * @var ilParticipationCertificate
	 */
	protected $object;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilToolbarGUI
	 */
	protected $ilToolbar;
	/**
	 * @var ilGroupParticipants
	 */
	protected $learnGroupParticipants;
	/**
	 * @var ilObjCourse
	 */
	protected $courseobject;
	/**
	 * @var \ilDB
	 */
	protected $db;
	/**
	 * @var mixed
	 */
	public $dropValues;
	/**
	 * @var
	 */
	public $surname;
	/**
	 * @var
	 */
	public $lastname;
	/**
	 * @var
	 */
	public $gender;


	/**
	 * ilParticipationCertificateConfigGUI constructor.
	 *
	 */
	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilToolbar, $ilDB;

		$this->object = ilParticipationCertificate::where([ "group_id" => 0 ])->first();
		if (!$this->object) {
			$this->object = new ilParticipationCertificate();
		}
		$this->tpl = $tpl;
		$this->db = $ilDB;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->ilToolbar = $ilToolbar;
		$this->dropValues = $this->getDropdownValues();
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->surname = $this->object->getSurname();
		$this->lastname = $this->object->getLastname();
		$this->gender = $this->object->getGender();

		$this->courseobject = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
	}


	function performCommand($cmd) {
		switch ($cmd) {
			default:
			case 'configure':
			case 'save':
			case 'cancel':
				$this->$cmd();
				break;
		}
	}


	/**
	 * Configure
	 *
	 * @param
	 *
	 * @return
	 */
	public function configure() {

		$this->tpl->getStandardTemplate();

		$form = $this->initForm();
		$this->fillForm($form);
		$this->tpl->setContent($form->getHTML());
	}


	public function initForm() {
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle('Konfiguration Teilnahmebescheinigung');
		$form->setDescription('Folgende Platzhalter sind verfügbar: <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		');

		$title = new ilTextInputGUI('Titel', 'title');
		$form->addItem($title);

		$introduction = new ilTextAreaInputGUI('Beschreibung', 'desc');
		$introduction->setRows(10);
		$form->addItem($introduction);

		$description = new ilTextAreaInputGUI('Erläuterung zur Bescheinigung:', 'explanation');
		$description->setRows(10);
		$form->addItem($description);

		$description2 = new ilTextAreaInputGUI('Erläuterung zur Bescheinigung zweiter Teil (fett gedruckt)', 'explanationTwo');
		$form->addItem($description2);

		$name_teacher = new ilTextInputGUI('Name Aussteller Dokument', 'nameteacher');
		$form->addItem($name_teacher);

		$function_teacher = new ilTextInputGUI('Funktion Aussteller Dokument', 'functionteacher');
		$form->addItem($function_teacher);

		$checkbox_yes = new ilCheckboxInputGUI('Print eMentoring', 'checkementoring');
		$form->addItem($checkbox_yes);

		$uploadfield = new ilFileInputGUI('Laden Sie Ihren PDF Header hoch', 'headerpic');
		$uploadfield->setSuffixes(array( 'png' ));
		$form->addItem($uploadfield);

		$data = $this->dropValues[1];
		$data1 = $this->dropValues[2];
		$data2 = $this->dropValues[3];
		$data3 = $this->dropValues[4];

		$optionss = [
			$data['field_id'] => $data['field_name'],
			$data1['field_id'] => $data1['field_name'],
			$data2['field_id'] => $data2['field_name'],
			$data3['field_id'] => $data3['field_name'],
		];

		$dropdownone = new ilSelectInputGUI('Benutzerdefiniertes Feld Vornamen', 'surname');
		$dropdownone->setOptions($optionss);
		$form->addItem($dropdownone);

		$dropdowntwo = new ilSelectInputGUI('Benutzerdefiniertes Feld Nachnamen', 'lastname');
		$dropdowntwo->setOptions($optionss);
		$form->addItem($dropdowntwo);

		$dropdownthree = new ilSelectInputGUI('Wähle das Feld für das Geschlecht', 'gender');
		$dropdownthree->setOptions($optionss);
		$form->addItem($dropdownthree);

		$form->addCommandButton(ilParticipationCertificateConfigGUI::CMD_SAVE, 'Speichern');

		return $form;
	}


	public function getDropdownValues() {

		$sql = "SELECT * FROM udf_definition";

		$results = $this->db->query($sql);

		while ($row = $this->db->fetchAssoc($results)) {
			$data[$row['field_id']] = $row;
		}

		return $data;
	}


	/**
	 * @return boolean
	 */
	public function fill() {

		$form = $this->initForm();
		$form->setValuesByPost();

		if (!$form->checkInput()) {
			return false;
		}

		$file_data = $form->getInput('headerpic');
		$this->object->storePicture($file_data);
		$this->object->setGroupId(0);
		$this->object->setTitle($form->getInput('title'));
		$this->object->setDescription($form->getInput('desc'));
		$this->object->setTeacherFunction($form->getInput('functionteacher'));
		$this->object->setTeacherName($form->getInput('nameteacher'));
		$this->object->setCheckeMentoring($form->getInput('checkementoring'));
		$this->object->setExplanation($form->getInput('explanation'));
		$this->object->setExplanationTwo($form->getInput('explanationTwo'));

		$this->object->setSurName($form->getInput('surname'));
		$this->object->setLastName($form->getInput('lastname'));
		$this->object->setGender($form->getInput('gender'));

		return true;
	}


	public function fillForm(&$form) {

		$array = array(
			'title' => $this->object->getTitle(),
			'desc' => $this->object->getDescription(),
			'functionteacher' => $this->object->getTeacherFunction(),
			'nameteacher' => $this->object->getTeacherName(),
			'explanation' => $this->object->getExplanation(),
			'explanationTwo' => $this->object->getExplanationTwo(),
			'checkementoring' => $this->object->isCheckeMentoring(),
			'surname' => $this->object->getSurName(),
			'lastname' => $this->object->getLastName(),
			'gender' => $this->object->getGender()
		);

		$form->setValuesbyArray($array);
	}


	/**
	 * @return bool
	 */
	public function save() {
		$form = $this->initForm();

		if (!$this->fill()) {
			return false;
		}

		$this->object->save();
		$this->ctrl->redirect($this, 'configure');


		return true;
	}
}