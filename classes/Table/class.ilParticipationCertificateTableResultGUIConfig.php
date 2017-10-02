<?php

require_once './Services/Table/classes/class.ilTable2GUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultModificationGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultOverviewGUI.php';
/**
 * Class ilParticipationCertificateTableGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateTableResultGUIConfig extends ilTable2GUI {

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
	 * @var ilParticipationCertificateTableGUI
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
	 * ilParticipationCertificateTableGUI constructor.
	 *
	 * @param ilParticipationCertificateTableGUI $a_parent_obj
	 * @param string                             $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd) {
		global $ilCtrl, $tabs;

		$this->ctrl = $ilCtrl;
		$this->tabs = $tabs;

		$this->setPrefix('dhbw_part_cert_res');
		$this->setFormName('dhbw_part_cert_res');
		$this->setId('dhbw_part_cert_res');

		$this->groupRefId = (int)$_GET['ref_id'];
		$group_ref_id = $this->groupRefId;
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultModificationGUI', [ 'ref_id', 'group_id' ]);

		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI','usr_id');
		$cert_access = new ilParticipationCertificateAccess($group_ref_id);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();
		$usr_id = $_GET[self::IDENTIFIER];
		$this->usr_id = $usr_id;


		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$nameUser = $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' . $arr_usr_data[$usr_id]->getPartCertLastname();

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->setRowTemplate('tpl.default_row.html', $this->pl->getDirectory());

		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$cert_access = new ilParticipationCertificateAccess(73);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		$this->getEnableHeader();
		$this->setTitle('Alle Resultate für ' . $nameUser);



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
		$cols['initial_test_finished'] = array(
			'txt' => 'Einstiegstest abgeschlossen',
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'initial_test_finished'
		);
		$cols['results_qualifing_tests'] = array(
			'txt' => 'Resultate der qualifizierenden Tests',
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'results_qualifing_tests'
		);
		$cols['result_qualifing_tests'] = array(
			'txt' => 'Resultat qualifizierende Tests',
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'result_qualifing_tests'
		);
		$cols['eMentoring_finished'] = array(
			'txt' => 'Aktive Teilnahme an Videokonferenzen',
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'eMentoring_finished'
		);
		$cols['eMentoring_homework'] = array(
			'txt' => 'Anzahl Hausaufgaben abgegeben',
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'eMentoring_homework'
		);
		$cols['eMentoring_percentage'] = array(
			'txt' => 'Bearbeitung der aufgaben zu überfachlichen Themen',
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'eMentoring_percentage'
		);

		return $cols;
	}


	private function addColumns() {
		/*if(!$this->getExportMode()) {
			$this->addColumn('');
		}*/
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


		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$arr_learn_reached_percentages = ilLearnObjectSuggReachedPercentages::getData($this->usr_ids);
		$arr_iass_states = ilIassStates::getData($this->usr_ids);
		$arr_excercise_states = ilExcerciseStates::getData($this->usr_ids);
		$arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);
		$array_obj_ids = ilLearnObjectFinalTestStates::getData($this->usr_ids);

		$rows = array();
		$usr_id = $this->usr_id;
			$row = array();
			$row['usr_id'] = $usr_id;
			$row['firstname'] = $arr_usr_data[$usr_id]->getPartCertFirstname();
			$row['lastname'] = $arr_usr_data[$usr_id]->getPartCertLastname();

			if (is_object($arr_initial_test_states[$usr_id])) {
				$row['initial_test_finished'] = $arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted();
				if ($row['initial_test_finished'] == 1) {
					$row['initial_test_finished'] = 'Ja';
				} else {
					$row['initial_test_finished'] = 'Nein';
				}
			}
			if (is_object($arr_learn_reached_percentages[$usr_id])) {
				$row['result_qualifing_tests'] = $arr_learn_reached_percentages[$usr_id]->getAveragePercentage() . '%';
			}
			if (is_object($arr_iass_states[$usr_id])) {
				$row['eMentoring_finished'] = $arr_iass_states[$usr_id]->getPassed();

				if ($row['eMentoring_finished'] == 1) {
					$row['eMentoring_finished'] = 'Ja';
				} else {
					$row['eMentoring_finished'] = 'Nein';
				}
			}

			if (is_array($arr_FinalTestsStates[$usr_id])) {

				foreach ($arr_FinalTestsStates[$usr_id] as $rec) {
					$rec_array[] = $rec->getLocFtestTestTitle();
					array_push($rec_array, $rec->getLocftestPercentage());
					array_push($rec_array, '<br>');
				}
				$array_results = $rec_array;
				$row['results_qualifing_tests'] = $array_results;
			}

			if (is_object($arr_iass_states[$usr_id])) {
				$row['eMentoring_homework'] = $arr_excercise_states[$usr_id]->getPassed();
				$row['eMentoring_percentage'] = $arr_excercise_states[$usr_id]->getPassedPercentage() . '%';
			}

			if ($this->filter['firstname'] != false) {
				if (strtolower($row['firstname']) == strtolower($this->filter['firstname'])) {
					$rows[] = $row;
				}
			} elseif ($this->filter['lastname'] != false) {
				if (strtolower($row['lastname']) == strtolower($this->filter['lastname'])) {
					$rows[] = $row;
				}
			} else {
				$rows[] = $row;
			}

		$this->setData($rows);

		return $rows;
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {


		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if ($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode(", ", $a_set[$k]) : $a_set[$k]));
					$this->tpl->parseCurrentBlock();
				} else {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', '&nbsp;');
					$this->tpl->parseCurrentBlock();
				}
			}
		}



	}

}