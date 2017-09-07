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


	public function __construct() {
		global $tpl, $ilCtrl, $ilTabs, $ilToolbar;

		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->ilToolbar = $ilToolbar;
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->courseobject = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);

	}

	function performCommand($cmd) {
		switch ($cmd) {
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

		$form = new ilPropertyFormGUI();
		$form->setTitle('Placeholders');

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
		$button1->setCaption('Print PDF');
		//$button1->setUrl($this->ctrl->getLinkTargetByClass(ilParticipationCertificatePDFGenerator::class,
			//ilParticipationCertificatePDFGenerator::CMD_PDF));
		//$button1->setUrl($this->ctrl->getLinkTarget($this, self::CMD_CONFIGURE));
		$button1->setUrl($this->ctrl->getLinkTargetByClass(ilParticipationCertificatePDFGenerator::class,
			ilParticipationCertificatePDFGenerator::CMD_PDF));
		$this->ilToolbar->addButtonInstance($button1);

		$this->tpl->setContent($form->getHTML());
	}




	public function save()
	{
		//TODO implement method to save the inputs to the db
	}

	public function cancel(){
		//TODO implement method to cancel and reset the input
	}


}