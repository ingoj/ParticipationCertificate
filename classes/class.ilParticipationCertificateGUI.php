<?php
include_once './Services/Form/classes/class.ilPropertyFormGUI.php';
include_once './Services/Object/classes/class.ilObjectListGUIFactory.php';
include_once './Modules/Course/classes/class.ilObjCourseGUI.php';
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfigGUI.php';
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePDFGenerator.php';

/**
 * Class ilParticipationCertificateGUI
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateGUI: ilUIPluginRouterGUI, ilParticipationHookGUI ilParticipationCertificatePDFGenerator
 */
class ilParticipationCertificateGUI {

	const CMD_DISPLAY = 'display';
	const CMD_SAVE = 'save';
	const CMD_CANCEL = 'cancel';
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
	 * @var ilObjCourse
	 */
	public $courseObject;
	/**
	 * @var
	 */
	public $objectDefinition;
	/**
	 * @var ilGroupParticipants
	 */
	public $learnGroupParticipants;
	/**
	 * @var ilObjGroup
	 */
	public $learningGroup;
	/**
	 * @var ilToolbarGUI
	 */
	public $ilToolbar;


	function __construct() {
		global $ilCtrl, $tpl, $ilTabs, $objDefinition, $ilToolbar;

		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->ilToolbar = $ilToolbar;
		$this->objectDefinition = $objDefinition;
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI','ref_id');
	}


	function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass){
		case 'ilparticipationcertificatepdfgenerator':
			$ilParticipationCertificatePDFGenerator = new ilParticipationCertificatePDFGenerator();
			$ret = $this->ctrl->forwardCommand($ilParticipationCertificatePDFGenerator);
			break;
			default:
				$this->{$cmd}();
		}
	}


	protected function display() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$form = $this->initform();
		$this->tpl->setContent($form->getHTML());
		$this->tpl->show();
	}


	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->saveParameterByClass('ilObjGroup', 'ref_id');
		$this->tabs->setBackTarget('Back', $this->ctrl->getLinkTargetByClass('ilRepositoryGUI', 'members'));
	}


	function initForm() {
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle('Teilnahmebescheinigungen');

		$title = new ilTextInputGUI();
		$title->setTitle('Titel');
		$form->addItem($title);

		$introduction = new ilTextAreaInputGUI();
		$introduction->setTitle('Einleitung');
		$form->addItem($introduction);

		$description = new ilTextAreaInputGUI();
		$description->setTitle('Erläuterung zur Bescheinigung');
		$form->addItem($description);

		$form->addCommandButton(ilParticipationCertificateGUI::CMD_SAVE, 'Save');
		$form->addCommandButton(ilParticipationCertificateGUI::CMD_CANCEL, 'Cancel');

		$button = ilLinkButton::getInstance();
		$button->setCaption('Print PDF');
		$button->setOnClick(ilParticipationCertificatePDFGenerator::class);
		$button->setUrl($this->ctrl->getLinkTargetByClass(ilParticipationCertificatePDFGenerator::class, ilParticipationCertificatePDFGenerator::CMD_PDF));
		$this->ilToolbar->addButtonInstance($button);




		return $form;
	}
}