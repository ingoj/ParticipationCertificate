<?php

require_once './Services/Table/classes/class.ilTable2GUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultModificationGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/SingleResultTable/class.ilParticipationCertificateSingleResultGUI.php';

/**
 * Class ilParticipationCertificateResultGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateSingleResultTableGUI extends ilTable2GUI {

	CONST IDENTIFIER = 'usr_id';
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificateResultGUI
	 */
	protected $parent_obj;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var array
	 */
	protected $filter = array();


	/**
	 * ilParticipationCertificateResultGUI constructor.
	 *
	 * @param ilParticipationCertificateResultGUI $a_parent_obj
	 * @param string                              $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd) {
		global $ilCtrl, $tabs;

		$this->ctrl = $ilCtrl;
		$this->tabs = $tabs;
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->setPrefix('dhbw_part_cert_res');
		$this->setFormName('dhbw_part_cert_res');
		$this->setId('dhbw_part_cert_res');

		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultModificationGUI', [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', 'usr_id');

		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		$usr_id = $_GET[self::IDENTIFIER];
		$this->usr_id = $usr_id;

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->getEnableHeader();
		$this->setRowTemplate('tpl.default_row.html', $this->pl->getDirectory());
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$nameUser = $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' . $arr_usr_data[$usr_id]->getPartCertLastname();
		$this->setTitle($this->pl->txt('result_for ') . ' ' . $nameUser);

		$this->initFilter();
		$this->addColumns();
		$this->parseData();
	}


	/**
	 * Get selectable columns
	 *
	 * @return        array    key: column id, val: true/false -> default on/off
	 */
	function getSelectableColumns() {
		$cols = array();

		$finalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);

		if(count($finalTestsStates[$this->usr_id] )) {
			foreach ($finalTestsStates[$this->usr_id] as $finalTestsState) {

				/**
				 * @var ilLearnObjectFinalTestState $finalTestsState
				 */
				$cols[$finalTestsState->getLocftestCrsObjId()] = array(
					'txt' => $finalTestsState->getLocftestCrsTitle(),
					'default' => true,
					'width' => 'auto',
				);
			}
		}



		return $cols;
	}


	private function addColumns() {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if (isset($v['sort_field'])) {
					$sort = $v['sort_field'];
				} else {
					$sort = NULL;
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
	}


	public function parseData() {

		$arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);

		$rows = array();
		$usr_id = $this->usr_id;

		$rec_array = array();

		if(count($arr_FinalTestsStates[$usr_id])) {
			foreach ($arr_FinalTestsStates[$usr_id] as $rec) {
				/**
				 * @var ilLearnObjectFinalTestOfSuggState $rec
				 */
				$rec_array[$usr_id][$rec->getLocftestCrsObjId()][] = $rec->getLocftestTestTitle() . '<br/>' . round($rec->getLocftestPercentage(), 0)
					. '%<br/>';
			}
		}



		$this->setData($rec_array);
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {


		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if ($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode("<br/>", $a_set[$k]) : $a_set[$k]));
					$this->tpl->parseCurrentBlock();
				} else {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', '&nbsp;');
					$this->tpl->parseCurrentBlock();
				}
			}
		}
	}


	public function fillInfos() {
	}
}

?>