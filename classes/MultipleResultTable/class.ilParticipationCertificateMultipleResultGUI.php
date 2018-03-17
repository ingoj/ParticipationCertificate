<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/MultipleResultTable/class.ilParticipationCertificateMultipleResultTableGUI.php';

/**
 * Class ilParticipationCertificateMultipleResultGUI
 *
 * @author            Florian Wyss <fw@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilParticipationCertificateMultipleResultGUI: ilUIPluginRouterGUI
 */
class ilParticipationCertificateMultipleResultGUI {

	const CMD_SHOW_ALL_RESULTS = 'show_all_results';
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
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
	/**
	 * @var ilParticipationCertificateMultipleResultTableGUI[]
	 */
	protected $tables = array();
	/**
	 * @var int[]
	 */
	protected $usr_ids;
	/**
	 * @var int
	 */
	protected $ref_id;
	/**
	 * @var ilObjGroup
	 */
	protected $learnGroup;


	public function __construct() {
		global $DIC;

		$this->toolbar = $DIC->toolbar();
		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();
		$this->tpl = $DIC->ui()->mainTemplate();
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->ref_id = filter_input(INPUT_GET, 'ref_id');

		$this->learnGroup = ilObjectFactory::getInstanceByRefId($this->ref_id);

		$this->usr_ids = filter_input(INPUT_POST, 'record_ids', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

		if (!is_array($this->usr_ids) || count($this->usr_ids) === 0) {
			ilUtil::sendFailure($this->pl->txt('no_records_selected'), true);
			$this->ctrl->redirectByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_CONTENT);
		};

		$this->ctrl->saveParameterByClass(ilParticipationCertificateUIHookGUI::class, [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass(ilParticipationCertificateResultModificationGUI::class, [ 'ref_id', 'group_id' ]);
		//$this->ctrl->saveParameterByClass(ilParticipationCertificateUIHookGUI::class, 'record_ids');
		$this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, 'ref_id');
	}


	/**
	 *
	 */
	public function executeCommand() {
		//$nextClass = $this->ctrl->getNextClass($this);
		//switch ($nextClass) {
		//default:
		$cmd = $this->ctrl->getCmd(self::CMD_SHOW_ALL_RESULTS);
		switch ($cmd) {
			case self::CMD_SHOW_ALL_RESULTS:
				$this->{$cmd}();
				break;
		}
		//}
	}


	/**
	 *
	 */
	protected function show_all_results() {
		$this->tpl->getStandardTemplate();
		$this->tpl->addCss($this->pl->getDirectory() . '/Templates/css/table.css');
		$this->initHeader();

		$this->initTables();

		$html = '';
		foreach ($this->tables as $table) {
			$html .= $table->getHTML();
		}
		$this->tpl->setContent($html);
		$this->tpl->show();
	}


	/**
	 *
	 */
	protected function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));
		$this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_CONTENT));
	}


	/**
	 *
	 */
	protected function initTables() {
		foreach ($this->usr_ids as $usr_id) {
			$this->tables[] = new ilParticipationCertificateMultipleResultTableGUI($this, self::CMD_SHOW_ALL_RESULTS, $usr_id, $this->usr_ids);
		}
	}
}

?>