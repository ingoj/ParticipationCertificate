<?php
include_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');
include_once ("./Services/Component/classes/class.ilPluginConfigGUI.php");
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePDFGenerator.php';
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePlugin.php');
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificate.php');
include_once './Services/Form/classes/class.ilPropertyFormGUI.php';


/**
 * Class ilParticipationCertificateConfigGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
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
	 * ilParticipationCertificateConfigGUI constructor.
	 *
	 */
	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilToolbar;

		$this->object = ilParticipationCertificate::where(["group_id" => 0])->first();
		if(!$this->object)
		$this->object = new ilParticipationCertificate();
		//$this->object = new ilParticipationCertificate();
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->ilToolbar = $ilToolbar;
		$this->pl = ilParticipationCertificatePlugin::getInstance();

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
	 * @param
	 * @return
	 */
	public function configure() {

		$this->tpl->getStandardTemplate();

		$form = $this->initForm();
		$this->fillForm($form);
		$this->tpl->setContent($form->getHTML());
	}


	public function initForm(){
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle('Konfiguration Teilnahmebescheinigung');
		$form->setDescription('Folgende Platzhalter sind verfügbar: <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		' );

		$title = new ilTextInputGUI('Titel','title');
		$form->addItem($title);

		$introduction = new ilTextAreaInputGUI('Beschreibung','desc');
		$introduction->setRows(10);
		$form->addItem($introduction);

		$description = new ilTextAreaInputGUI('Erläuterung zur Bescheinigung:','explanation');
		$description->setRows(10);
		$form->addItem($description);


		$description2 = new ilTextAreaInputGUI('Erläuterung zur Bescheinigung zweiter Teil (fett gedruckt)','explanationTwo');
		$form->addItem($description2);

		$name_teacher = new ilTextInputGUI('Name Aussteller Dokument', 'nameteacher');
		$form->addItem($name_teacher);

		$function_teacher = new ilTextInputGUI('Funktion Aussteller Dokument','functionteacher');
		$form->addItem($function_teacher);

		$checkbox_yes = new ilCheckboxInputGUI('Print eMentoring', 'checkementoring');
		$form->addItem($checkbox_yes);


		$uploadfield = new ilFileInputGUI('Laden Sie Ihren PDF Header hoch','headerpic');

		$form->addItem($uploadfield);

		$form->addCommandButton(ilParticipationCertificateConfigGUI::CMD_SAVE, 'Speichern');

		return $form;
	}




	/**
	 * @return boolean
	 */
	public function fill(){

		$form = $this->initForm();
		$form->setValuesByPost();

		if(!$form->checkInput()) {
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

		//TODO Get Students who are in the group

		return true;
	}



	public function fillForm(&$form){

		$array = array('title' => $this->object->getTitle(),
			'desc' => $this->object->getDescription(),
			'functionteacher' => $this->object->getTeacherFunction(),
			'nameteacher' => $this->object->getTeacherName(),
			'explanation' => $this->object->getExplanation(),
			'explanationTwo' => $this->object->getExplanationTwo(),
			'checkementoring' => $this->object->isCheckeMentoring());

		$form->setValuesbyArray($array);
		/*
		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption('Print PDF');
		$b_print->setUrl($this->ctrl->getLinkTarget(new ilParticipationCertificateTwigParser(),
			ilParticipationCertificateTwigParser::CMD_PARSE));
		$this->toolbar->addButtonInstance($b_print);*/

	}


	/**
	 * @return bool
	 */
	public function save()
	{
		$form = $this->initForm();

		if(!$this->fill()) {
			return false;
		}





		$this->object->save();
		$this->ctrl->redirect($this,'configure');
		//$this->tpl->setContent($form->getHTML());
		return true;

	}

}