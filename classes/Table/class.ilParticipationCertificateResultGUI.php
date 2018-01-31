<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultTableGUI.php';
require_once './Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultModificationGUI.php';

/**
 * Class ilParticipationCertificateResultGUI
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateResultGUI: ilUIPluginRouterGUI
 *
 * @ilCtrl_Calls      ilParticipationCertificateResultGUI: ilParticipationCertificateSingleResultGUI, ilParticipationCertificateGUI, ilParticipationCertificateResultModificationGUI
 *
 */
class ilParticipationCertificateResultGUI {

	CONST CMD_CONTENT = 'content';
	CONST CMD_OVERVIEW = 'overview';
	CONST CMD_PRINT_PDF = 'printpdf';
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
	 * @var int
	 */
	protected $groupRefId;
	/**
	 * @var object
	 */
	protected $learnGroup;
	/**
	 * @var ilParticipationCertificateAccess
	 */
	protected $cert_access;


	/**
	 * ilParticipationCertificateResultGUI constructor.
	 */
	public function __construct() {
		global $ilCtrl, $ilTabs, $tpl, $ilToolbar, $lng;

		$this->toolbar = $ilToolbar;
		$this->tabs = $ilTabs;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->groupRefId = (int)$_GET['ref_id'];
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);

		/*Access
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if (!$cert_access->hasCurrentUserPrintAccess()) {
			ilUtil::sendFailure($lng->txt('no_permission'), true);
			ilUtil::redirect('login.php');
		}*/

		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', [ 'ref_id', 'group_id' ]);
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();

		switch ($nextClass) {
			case 'ilparticipationcertificateresultmodificationgui':
				$ilparticipationcertificateresultmodificationgui = new ilParticipationCertificateResultModificationGUI();
				$ret1 = $this->ctrl->forwardCommand($ilparticipationcertificateresultmodificationgui);
				break;
			case 'ilparticipationcertificategui':
				$ilParticipationCertificateGUI = new ilParticipationCertificateGUI();
				$ret2 = $this->ctrl->forwardCommand($ilParticipationCertificateGUI);
				$this->tabs->setTabActive(self::CMD_OVERVIEW);
				break;
			case 'ilparticipationcertificatesingleresultgui':
				$ilparticipationcertificateresultoverviewgui = new ilParticipationCertificateSingleResultGUI();
				$ret3 = $this->ctrl->forwardCommand($ilparticipationcertificateresultoverviewgui);
				break;
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_CONTENT);
				$this->tabs->setTabActive(self::CMD_OVERVIEW);
				switch ($cmd) {
					case ilParticipationCertificateMultipleResultGUI::CMD_SHOW_ALL_RESULTS:
						$this->ctrl->forwardCommand(new ilParticipationCertificateMultipleResultGUI());
						break;
					default:
						$this->{$cmd}();
						break;
				}
				break;
		}
	}


	public function content() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);

		if($cert_access->hasCurrentUserPrintAccess()) {
			$b_print = ilLinkButton::getInstance();
			$b_print->setCaption($this->pl->txt('header_btn_print'), false);
			$this->ctrl->setParameter($this, 'ementor', true);
			$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF));
			$this->toolbar->addButtonInstance($b_print);

			$b_print = ilLinkButton::getInstance();
			$this->ctrl->setParameter($this, 'ementor', false);
			$b_print->setCaption($this->pl->txt('header_btn_print_eMentoring'), false);
			$b_print->setUrl($this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF));
			$this->toolbar->addButtonInstance($b_print);
		}

		$this->initTable();

		$this->tpl->setContent($this->table->getHTML());
		$this->tpl->show();
	}


	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->setParameterByClass('ilrepositorygui', 'ref_id', (int)$_GET['ref_id']);
		$this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(array('ilrepositorygui', 'ilobjgroupgui')));
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', 'ref_id');

		$this->tabs->addTab('overview', $this->pl->txt('header_overview'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_CONTENT));
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if($cert_access->hasCurrentUserPrintAccess()) {
			$this->tabs->addTab('config', $this->pl->txt('header_config'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateGUI::class, ilParticipationCertificateGUI::CMD_DISPLAY));
		}
		$this->tabs->activateTab('overview');
	}


	public function printPdf() {
		global $lng;
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if($cert_access->hasCurrentUserPrintAccess()) {
			$ementor = $_GET['ementor'];
			$usr_id = $_GET['usr_id'];
			$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, $ementor, false);
			$twigParser->parseData();
		}
		else{
			ilUtil::sendFailure($lng->txt('no_permission'), true);
			ilUtil::redirect('login.php');
		}
	}


	public function printSelected() {
		global $lng;
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if($cert_access->hasCurrentUserPrintAccess()) {
			if (!isset($_POST['record_ids']) || (isset($_POST['record_ids']) && !count($_POST['record_ids']))) {
				ilUtil::sendFailure($this->pl->txt('no_records_selected'), true);
				$this->ctrl->redirect($this, self::CMD_CONTENT);
			}
			$usr_id = $_POST['record_ids'];
			$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, true, false, false);
			$twigParser->parseData();
		}
		else{
			ilUtil::sendFailure($lng->txt('no_permission'), true);
			ilUtil::redirect('login.php');
		}
	}


	public function printSelectedWithouteMentoring() {
		global $lng;
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if($cert_access->hasCurrentUserPrintAccess()) {
			if (!isset($_POST['record_ids']) || (isset($_POST['record_ids']) && !count($_POST['record_ids']))) {
				ilUtil::sendFailure($this->pl->txt('no_records_selected'), true);
				$this->ctrl->redirect($this, self::CMD_CONTENT);
			}

			$usr_id = $_POST['record_ids'];
			$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, false, false, false);
			$twigParser->parseData();
		}
		else{
			ilUtil::sendFailure($lng->txt('no_permission'), true);
			ilUtil::redirect('login.php');
		}
	}


	public function applyFilter() {
		$table = new ilParticipationCertificateResultTableGUI($this, self::CMD_CONTENT);
		$table->writeFilterToSession();
		$table->resetOffset();
		$this->ctrl->redirect($this, self::CMD_CONTENT);
	}


	public function resetFilter() {
		$table = new ilParticipationCertificateResultTableGUI($this, self::CMD_CONTENT);
		$table->resetOffset();
		$table->resetFilter();
		$this->ctrl->redirect($this, self::CMD_CONTENT);
	}


	protected function initTable($override = false) {
		$this->table = new ilParticipationCertificateResultTableGUI($this, ilParticipationCertificateResultGUI::CMD_CONTENT);
	}
}

?>