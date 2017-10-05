<?php

require_once './Services/Table/classes/class.ilTable2GUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultModificationGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultOverviewGUI.php';
/**
 * Class ilParticipationCertificateTableGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateTableGUIConfig extends ilTable2GUI {

	CONST IDENTIFIER = 'ilpartusr';

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

	protected $custom_export_formats = array();
	protected $custom_export_generators = array();


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

		$this->setPrefix('dhbw_part_cert');
		$this->setFormName('dhbw_part_cert');
		$this->setId('dhbw_part_cert');
		$cert_access = new ilParticipationCertificateAccess(73);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->getEnableHeader();
		$this->setTitle($this->pl->txt('tbl_overview_results'));

		$this->setExportFormats(array(self::EXPORT_EXCEL, self::EXPORT_CSV));

		$this->addColumns();
		$this->initFilter();

		$this->setSelectAllCheckbox('record_ids');
		$this->addMultiCommand('printSelected', 'Print');

		$this->setRowTemplate('tpl.default_row.html', $this->pl->getDirectory());
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->parseData();

	}


	/**
	 * Get selectable columns
	 *
	 * @return        array    key: column id, val: true/false -> default on/off
	 */
	function getSelectableColumns() {


		$cols = array();
		$cols['check'] = array( 'txt' => '', 'default' => true, 'width' => 'auto', 'sort_field' => 'firstname' );
		$cols['firstname'] = array( 'txt' => 'Vorname', 'default' => true, 'width' => 'auto', 'sort_field' => 'firstname' );
		$cols['lastname'] = array( 'txt' => 'Nachname', 'default' => true, 'width' => 'auto', 'sort_field' => 'lastname' );
		$cols['initial_test_finished'] = array(
			'txt' => 'Einstiegstest abgeschlossen',
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'initial_test_finished'
		);
		/*$cols['results_qualifing_tests'] = array(
			'txt' => 'Resultate der qualifizierenden Tests',
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'results_qualifing_tests'
		);*/
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
		$this->addColumn('');
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
		$this->addColumn($this->pl->txt('cols_actions'));

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
		foreach ($this->usr_ids as $usr_id) {
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
		}
		$this->setData($rows);

		return $rows;
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {

		$this->tpl->setCurrentBlock('record_id');
		$this->tpl->setVariable('RECORD_ID', $a_set['usr_id']);
		$this->tpl->parseCurrentBlock();

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
		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('list_actions'));
		$current_selection_list->setId('_actions' . $a_set['usr_id']);
		$current_selection_list->setUseImages(false);
		$this->ctrl->setParameterByClass('ilParticipationCertificateTableGUI', 'usr_id', $a_set['usr_id']);

		$current_selection_list->addItem($this->pl->txt('list_results'), ilParticipationCertificateResultModificationGUI::CMD_DISPLAY, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultModificationGUI::class, ilParticipationCertificateResultModificationGUI::CMD_DISPLAY));
		$current_selection_list->addItem($this->pl->txt('list_print'), ilParticipationCertificateResultModificationGUI::CMD_PRINT_PURE, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultModificationGUI::class, ilParticipationCertificateResultModificationGUI::CMD_PRINT_PURE));
		$current_selection_list->addItem($this->pl->txt('list_overview'), ilParticipationCertificateResultOverviewGUI::CMD_DISPLAY, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultOverviewGUI::class, ilParticipationCertificateResultOverviewGUI::CMD_DISPLAY));

		$this->tpl->setVariable('ACTIONS', $current_selection_list->getHTML());
		$this->tpl->parseCurrentBlock();
		$this->tpl->setVariable('CHECK_ID', $a_set['check_id']);
	}


	public function initFilter() {
		$firstname = new ilTextInputGUI($this->pl->txt('firstname'), 'firstname');
		$lastname = new ilTextInputGUI($this->pl->txt('lastname'), 'lastname');

		$this->addAndReadFilterItem($firstname);
		$this->addAndReadFilterItem($lastname);

		$firstname->readFromSession();
		$lastname->readFromSession();

		$this->filter['firstname'] = $firstname->getValue();
		$this->filter['lastname'] = $lastname->getValue();
	}


	/**
	 * @param $item
	 */
	public function addAndReadFilterItem($item) {
		$this->addFilterItem($item);
		$item->readFromSession();

		$this->filter[$item->getPostVar()] = $item->getValue();

		$this->setDisableFilterHiding(true);
	}


	/**
	 * @param array $formats
	 */
	public function setExportFormats(array $formats) {
		parent::setExportFormats($formats);

		$custom_fields = array_diff($formats, $this->export_formats);

		foreach ($custom_fields as $format_key){
			if (isset($this->custom_export_formats[$format_key])){
				$this->export_formats[$format_key] = $this->pl->getPrefix() . '_' - $this->custom_export_formats[$format_key];
			}
		}
	}

	public function exportData($format, $send = false) {
		if (array_key_exists($format, $this->custom_export_formats)) {
			if ($this->dataExists()) {

				foreach ($this->custom_export_generators as $export_format => $generator_config) {
					if ($this->getExportMode() == $export_format) {
						$generator_config['generator']->generate();
					}
				}
			}
		} else {
			parent::exportData($format, $send);
		}
	}



	/**
	 * @param       $export_format_key
	 * @param       $custom_export_generators
	 * @param array $params
	 */
	public function addCustomExportGenerator($export_format_key, $custom_export_generators, $params = array()){
		$this->custom_export_generators[$export_format_key] = array('generator' => $custom_export_generators, 'params' => $params);
	}


	/**
	 * @param $custom_export_format_key
	 * @param $custom_export_format_label
	 */
	public function addCustomExportFormat($custom_export_format_key, $custom_export_format_label){
		$this->custom_export_formats[$$custom_export_format_key] = $custom_export_format_label;
	}
}