<?php
include_once './Services/Form/classes/class.ilPropertyFormGUI.php';
include_once './Services/Object/classes/class.ilObjectListGUIFactory.php';
include_once './Modules/Course/classes/class.ilObjCourseGUI.php';
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfigGUI.php';
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePDFGenerator.php';
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Parser/class.ilParticipationCertificateTwigParser.php';
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateAccess.php";

/**
 * Class ilParticipationCertificateGUI
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateGUI: ilUIPluginRouterGUI, ilParticipationHookGUI ilParticipationCertificatePDFGenerator
 */
class ilParticipationCertificateGUI {

	const CMD_DISPLAY = 'display';
	const CMD_SAVE = 'save';
	const CMD_CANCEL = 'cancel';
	const CMD_LOOP = 'loop';
	/**
	 * @var ilTemplate
	 */
	public $tpl;
	/**
	 * @var ilCtrl
	 */
	public $ctrl;
	/**
	 * @var ilTabsGUI
	 */
	public $tabs;
	/**
	 * @var ilGroupParticipants
	 */
	public $learnGroupParticipants;
	/**
	 * @var ilObjGroup
	 */
	public $learningGroup;
	/**
	 * @var ilParticipationCertificate
	 */
	public $object;
	/**
	 * @var ilToolbarGUI
	 */
	public $toolbar;
	/**
	 * @var int
	 */
	public $groupRefId;


	function __construct() {
		global $ilCtrl, $tpl, $ilTabs, $objDefinition, $ilToolbar, $lng;

		$this->toolbar = $ilToolbar;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->objectDefinition = $objDefinition;
		$this->groupRefId = (int)$_GET['ref_id'];
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);

		//Access
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if (!$cert_access->hasCurrentUserPrintAccess()) {
			ilUtil::sendFailure($lng->txt('no_permission'), true);
			ilUtil::redirect('login.php');
		}

		$this->object = ilParticipationCertificate::where([ 'group_id' => $this->groupObjId ])->first();
		if (!$this->object) {
			$this->object = ilParticipationCertificate::where([ 'group_id' => 0 ])->first();
			$this->object->setId(NULL);
		}
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', [ 'ref_id', 'group_id' ]);
	}


	function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			case 'ilparticipationcertificatepdfgenerator':
				$ilParticipationCertificatePDFGenerator = new ilParticipationCertificatePDFGenerator();
				$ret = $this->ctrl->forwardCommand($ilParticipationCertificatePDFGenerator);
				break;
			case 'ilparticipationcertificatetwigparser':
				$ilParticipationCertificateTwigParser = new ilParticipationCertificateTwigParser();
				$ret1 = $this->ctrl->forwardCommand($ilParticipationCertificateTwigParser);
				break;
			case 'ilparticipationcertificategui':
				$ilParticipationCertificateGUI = new ilParticipationCertificateGUI();

			default:
				$this->{$cmd}();
		}
	}


	protected function display() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$form = $this->initform();
		$this->fillForm($form);
		$this->tpl->setContent($form->getHTML());
		$this->tpl->show();
	}


	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->saveParameterByClass('ilRepositoryGUI', 'ref_id');
		$this->tabs->setBackTarget('Zurück', $this->ctrl->getLinkTargetByClass('ilRepositoryGUI'));
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

		$this->ctrl->saveParameterByClass('ilObjGroup', 'ref_id');
		$form->addCommandButton(ilParticipationCertificateGUI::CMD_SAVE, 'Speichern');

		return $form;
	}


	public function fillForm(&$form) {

		$array = array(
			'title' => $this->object->getTitle(),
			'desc' => $this->object->getDescription(),
			'functionteacher' => $this->object->getTeacherFunction(),
			'nameteacher' => $this->object->getTeacherName(),
			'explanation' => $this->object->getExplanation(),
			'explanationTwo' => $this->object->getExplanationtwo(),
			'checkementoring' => $this->object->isCheckeMentoring()
		);

		$form->setValuesbyArray($array);

		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption('rpc_pdf_generation');
		$this->ctrl->saveParameterByClass('ilParticipationCertificateTwigParser', 'ref_id');
		$b_print->setUrl($this->ctrl->getLinkTarget(new ilParticipationCertificateTwigParser(), ilParticipationCertificateTwigParser::CMD_PARSE));
		$this->toolbar->addButtonInstance($b_print);
	}


	/**
	 * @return bool
	 */
	public function save() {
		if (!$this->fill()) {
			//TODO error message plus redirect
			return false;
		}

		$this->object->save();
		$this->ctrl->redirect($this, 'display');

		//$this->tpl->setContent($form->getHTML());
		return true;
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

		$this->object->setGroupId($this->groupObjId);
		$this->object->setTitle($form->getInput('title'));
		$this->object->setDescription($form->getInput('desc'));
		$this->object->setTeacherFunction($form->getInput('functionteacher'));
		$this->object->setTeacherName($form->getInput('nameteacher'));
		$this->object->setCheckeMentoring($form->getInput('checkementoring'));
		$this->object->setExplanation($form->getInput('explanation'));
		$this->object->setExplanationtwo($form->getInput('explanationTwo'));

		//TODO Get Students who are in the group

		return true;
	}
}