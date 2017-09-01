<?php
include_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');
include_once ("./Services/Component/classes/class.ilPluginConfigGUI.php");
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePDFGenerator.php';
include_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePlugin.php');
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


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilToolbar;

		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->ilToolbar = $ilToolbar;
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->courseobject = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		//$this->learnGroupParticipants = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);

	}

	function performCommand($cmd) {
		switch ($cmd) {
			case 'configure':
			case 'save':
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

		$form = new ilPropertyFormGUI();

		$title = new ilTextInputGUI();
		$title->setTitle('Titel');
		$form->addItem($title);

		$introduction = new ilTextAreaInputGUI();
		$introduction->setTitle('Einleitung');
		$form->addItem($introduction);

		$description = new ilTextAreaInputGUI();
		$description->setTitle('Erläuterung zur Bescheinigung');
		$form->addItem($description);

		$form->addCommandButton(ilParticipationCertificateConfigGUI::CMD_SAVE, 'Save');
		$form->addCommandButton(ilParticipationCertificateConfigGUI::CMD_CANCEL, 'Cancel');

		$button1 = ilLinkButton::getInstance();
		$button1->setUrl($this->ctrl->getLinkTargetByClass(ilParticipationCertificatePDFGenerator::class,
			ilParticipationCertificatePDFGenerator::CMD_PDF));
		$this->ilToolbar->addButtonInstance($button1);

		$this->tpl->setContent($form->getHTML());
	}


	public function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		switch ($cmd) {
			case self::CMD_CONFIGURE:
				$this->{$cmd}();
				break;
			default:
				throw new Exception('Not allowed');
				break;
		}
	}

	public function save()
	{


	}


}