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
	 * @var
	 */
	public $learningGroup;


	function __construct() {
		global $ilCtrl, $tpl, $ilTabs, $objDefinition;

		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->objectDefinition = $objDefinition;
		$this->learnGroupParticipants = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		//	$this->courseObject = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		//	$this->learnGroupParticipants = new ilCourseParticipants($this->courseObject->getId());

	}


	function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		switch ($cmd) {
			case self::CMD_DISPLAY:
				$this->{$cmd}();
				break;
			default:
				throw new Exception('not allowed');
				break;
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
		$this->tpl->setTitle($this->courseObject->getTitle());
		$this->tpl->setDescription($this->learnGroupParticipants->getMembers());
		//$this->tpl->setTitleIcon(ilObject::_getIcon($this->courseObject->getId()));

		$this->ctrl->saveParameterByClass('ilObjCourseGUI', 'ref_id');
		$this->tabs->setBackTarget('Back', $this->ctrl->getLinkTargetByClass(array( 'ilRepositoryGUI', 'ilObjCourseGUI' ), 'members'));
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
		$form->addCommandButton('print', 'PRINT PDF');
		/*$this->ctrl->getLinkTargetByClass('ilparticipationcertificatepdfgenerator', ilParticipationCertificatePDFGenerator::CMD_PDF);

		$button = ilLinkButton::getInstance();
		$button->setCaption('Print PDF');
		$button->setOnClick(ilParticipationCertificatePDFGenerator::class);
		$button->setUrl($this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_CONFIGURE));

		$form->addItem($button);
*/
		return $form;
	}
}