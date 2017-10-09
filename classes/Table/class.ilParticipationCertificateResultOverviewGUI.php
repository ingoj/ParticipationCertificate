<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateTableGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateTableResultGUIConfig.php';

/**
 * Class ilParticipationCertificateResultOverviewGUI
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilParticipationCertificateResultOverviewGUI: ilParticipationCertificateTableGUI, ilUIPluginRouterGUI
 */
class ilParticipationCertificateResultOverviewGUI {

	CONST CMD_DISPLAY = 'display';
	CONST IDENTIFIER = 'usr_id';
	/**
	 * @var ilTabsGUI
	 */
	public $tabs;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;


	public function __construct() {
		global $ilCtrl, $ilTabs, $tpl, $ilToolbar;

		$this->toolbar = $ilToolbar;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->tpl = $tpl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->groupRefId = (int)$_GET['ref_id'];
		$group_ref_id = $this->groupRefId;
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultModificationGUI', [ 'ref_id', 'group_id' ]);

		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI', 'usr_id');
		$cert_access = new ilParticipationCertificateAccess($group_ref_id);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();
		$usr_id = $_GET[self::IDENTIFIER];
		$this->usr_id = $usr_id;

		$this->arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$this->arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$this->arr_learn_reached_percentages = ilLearnObjectSuggReachedPercentages::getData($this->usr_ids);
		$this->arr_iass_states = ilIassStates::getData($this->usr_ids);
		$this->arr_excercise_states = ilExcerciseStates::getData($this->usr_ids);
		$this->arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);
		$this->array_obj_ids = ilLearnObjectFinalTestStates::getData($this->usr_ids);
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_DISPLAY);
				$this->{$cmd}();
				break;
			case 'ilparticipationcertificatetablegui':
				$ilParticipationCertificateTableGUI = new ilParticipationCertificateTableGUI();
				$this->ctrl->forwardCommand($ilParticipationCertificateTableGUI);
				break;
			case 'ilparticipationcertificategui':
				$ilParticipationCertificateGUI = new ilParticipationCertificateGUI();
				$ret2 = $this->ctrl->forwardCommand($ilParticipationCertificateGUI);
				$this->tabs->setTabActive(ilParticipationCertificateGUI::CMD_CONFIG);
				break;
		}
		//$this->tpl->show();
	}


	public function display() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$this->initTable();

		$this->tpl->setContent($this->table->getHTML());
		$this->tpl->show();
	}


	public function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI', 'ref_id');
		$this->tabs->setBackTarget('Zurück', $this->ctrl->getLinkTargetByClass(ilParticipationCertificateTableGUI::class, ilParticipationCertificateTableGUI::CMD_CONTENT));
	}


	public function initTable() {

		$this->table = new ilParticipationCertificateTableResultGUIConfig($this, ilParticipationCertificateResultOverviewGUI::CMD_DISPLAY);
	}
}
?>