<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateTableGUI.php';
require_once './Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultModificationGUI.php';

/**
 * Class ilParticipationCertificateResultGUI
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateResultGUI: ilParticipationCertificateGUI, ilUIPluginRouterGUI, ilParticipationCertificateResultModificationGUI
 */
class ilParticipationCertificateResultGUI {

	CONST CMD_CONTENT = 'content';
	CONST CMD_OVERVIEW = 'overview';
	CONST CMD_PRINT_PDF = 'printpdf';
	CONST CMD_PRINT_PDF_WITHOUT_EMENTORING = 'printpdfwithoutmentoring';
	CONST CMD_INIT_TABLE = 'initTable';
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
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;


	/**
	 * ilParticipationCertificateResultGUI constructor.
	 */
	public function __construct() {
		global $ilCtrl, $ilTabs, $tpl, $ilToolbar;

		$this->toolbar = $ilToolbar;
		$this->tabs = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->usr_ids;

		$this->groupRefId = (int)$_GET['ref_id'];
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', [ 'ref_id', 'group_id' ]);
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			case 'ilparticipationcertificateresultmodificationgui':
				$ilparticipationcertificateresultmodificationgui = new ilParticipationCertificateResultModificationGUI();
				$ret1 = $this->ctrl->forwardCommand($ilparticipationcertificateresultmodificationgui);
				break;
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_CONTENT);
				$this->tabs->setTabActive(self::CMD_OVERVIEW);
				$this->{$cmd}();
				break;
			case 'ilparticipationcertificategui':
				$ilParticipationCertificateGUI = new ilParticipationCertificateGUI();
				$ret2 = $this->ctrl->forwardCommand($ilParticipationCertificateGUI);
				$this->tabs->setTabActive(self::CMD_OVERVIEW);
				break;
			case 'ilparticipationcertificateresultoverviewgui':
				$ilparticipationcertificateresultoverviewgui = new ilParticipationCertificateResultOverviewGUI();
				$ret3 = $this->ctrl->forwardCommand($ilparticipationcertificateresultoverviewgui);
				break;
		}
	}


	public function content() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();
		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption($this->pl->txt('header_btn_print'), false);
		$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF));
		$this->toolbar->addButtonInstance($b_print);

		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption($this->pl->txt('header_btn_print_eMentoring'), false);
		$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF_WITHOUT_EMENTORING));
		$this->toolbar->addButtonInstance($b_print);

		$this->initTable();

		$this->tpl->setContent($this->table->getHTML());
		$this->tpl->show();
	}


	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass('ilRepositoryGUI'));

		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', 'ref_id');
		$this->tabs->addTab('overview', $this->pl->txt('header_overview'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_CONTENT));

		$this->tabs->addTab('config', $this->pl->txt('header_config'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateGUI::class, ilParticipationCertificateGUI::CMD_DISPLAY));
		$this->tabs->activateTab('overview');
	}


	public function printPdf() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId);
		$twigParser->parseData();
	}


	public function printPdfWithoutMentoring() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), false);
		$twigParser->parseData();
	}


	public function printSelected() {
		if (!isset($_POST['record_ids']) || (isset($_POST['record_ids']) && !count($_POST['record_ids']))) {
			ilUtil::sendInfo('No Records selected');
			$this->initTable();

			return;
		}
		$this->usr_ids = ($_POST['record_ids']);

		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), false, false);
		$twigParser->parseDataSelected($this->usr_ids);
		$this->ctrl->redirect(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_CONTENT);
	}


	public function applyFilter() {
		/*$this->initTable();

		$this->table->writeFilterToSession();
		$this->table->resetOffset();

		$this->content();*/

		$table = new ilParticipationCertificateTableGUI($this, self::CMD_CONTENT);
		$table->writeFilterToSession();
		$table->resetOffset();
		$this->ctrl->redirect($this, self::CMD_CONTENT);
	}


	public function resetFilter() {
		/*$this->initTable();

		$this->table->resetOffset();
		$this->table->resetFilter();

		$this->content();*/
		$table = new ilParticipationCertificateTableGUI($this, self::CMD_CONTENT);
		$table->resetOffset();
		$table->resetFilter();
		$this->ctrl->redirect($this, self::CMD_CONTENT);
	}


	protected function initTable($override = false) {

		$this->table = new ilParticipationCertificateTableGUI($this, ilParticipationCertificateResultGUI::CMD_CONTENT);
	}
}
?>