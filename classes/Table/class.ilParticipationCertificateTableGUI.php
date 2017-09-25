<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateTableGUIConfig.php';
require_once './Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php';
/**
 * Class ilParticipationCertificateTableGUI
 * @ilCtrl_isCalledBy ilParticipationCertificateTableGUI: ilParticipationCertificateGUI, ilUIPluginRouterGUI
 */
class ilParticipationCertificateTableGUI {

	CONST CMD_CONTENT = 'content';
	CONST CMD_OVERVIEW = 'overview';
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
	protected $toolbar;



	/**
	 * ilParticipationCertificateTableGUI constructor.
	 */
	public function __construct() {
	global $ilCtrl, $ilTabs, $tpl,$ilToolbar;

		$this->toolbar = $ilToolbar;
		$this->tabs = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;

		$this->groupRefId = (int)$_GET['ref_id'];
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI', [ 'ref_id', 'group_id' ]);
	}


	public function executeCommand(){
		$this->tpl->getStandardTemplate();
		$this->initHeader();
		$nextClass = $this->ctrl->getNextClass();
		switch($nextClass){
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_CONTENT);
				$this->tabs->setTabActive(self::CMD_OVERVIEW);
				$this->{$cmd}();
				break;
		}
		$this->tpl->show();
	}


	public function content(){
		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption('Bescheinigung Drucken');
		$b_print->setUrl($this->ctrl->getLinkTarget($this, 'printPdf'));
		$this->toolbar->addButtonInstance($b_print);

		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption('Bescheinigung Drucken (exkl. eMentoring)');
		$b_print->setUrl($this->ctrl->getLinkTarget($this, 'printPdfWithoutMentoring'));
		$this->toolbar->addButtonInstance($b_print);

		$this->tpl->getStandardTemplate();
		$myTable = new ilParticipationCertificateTableGUIConfig($this,ilParticipationCertificateTableGUI::CMD_CONTENT);

		$this->tpl->setContent($myTable->getHTML());
	}

	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->tabs->setBackTarget('Zurück', $this->ctrl->getLinkTargetByClass('ilRepositoryGUI'));

		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI', [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', 'ref_id');
		$this->tabs->addTab('overview', 'Übersicht', $this->ctrl->getLinkTargetByClass(ilParticipationCertificateTableGUI::class,ilParticipationCertificateTableGUI::CMD_CONTENT));

		$this->tabs->addTab('config', 'Konfigurieren', $this->ctrl->getLinkTargetByClass(ilParticipationCertificateGUI::class,ilParticipationCertificateGUI::CMD_DISPLAY));
		$this->tabs->activateTab('config');
	}

	public function printPdf() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId);
		$twigParser->parseData();
	}

	public function printPdfWithoutMentoring() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId,array(),false);
		$twigParser->parseData();
	}


}