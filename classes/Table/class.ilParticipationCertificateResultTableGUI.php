<?php

require_once './Services/Table/classes/class.ilTable2GUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultModificationGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/SingleResultTable/class.ilParticipationCertificateSingleResultGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/LearnObjectFinalTestStateOfSugg/class.ilLearnObjectFinalTestOfSuggStates.php';


/**
 * Class ilParticipationCertificateResultGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateResultTableGUI extends ilTable2GUI {

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
	protected $custom_export_formats = array();
	protected $custom_export_generators = array();


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

		$this->setPrefix('dhbw_part_cert');
		$this->setFormName('dhbw_part_cert');
		$this->setId('dhbw_part_cert');

		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->getEnableHeader();
		$this->setTitle($this->pl->txt('tbl_overview_results'));
		$this->addColumns();
		$this->setExportFormats(array( self::EXPORT_EXCEL, self::EXPORT_CSV ));

		if($cert_access->hasCurrentUserPrintAccess()) {
			$this->initFilter();
			$this->setSelectAllCheckbox('record_ids');
			$this->addMultiCommand('printSelected', $this->pl->txt('list_print'));
			$this->addMultiCommand('printSelectedWithouteMentoring', $this->pl->txt('list_print_without'));
		}
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
		//$cols['usr_id'] = array( 'txt' => 'usr_id', 'default' => false, 'width' => 'auto', 'sort_field' => 'usr_id' );
		$cols['firstname'] = array( 'txt' => $this->pl->txt('cols_firstname'), 'default' => true, 'width' => 'auto', 'sort_field' => 'firstname' );
		$cols['lastname'] = array( 'txt' => $this->pl->txt('cols_lastname'), 'default' => true, 'width' => 'auto', 'sort_field' => 'lastname' );
		$cols['initial_test_finished'] = array(
			'txt' => $this->pl->txt('cols_initial_test_finished'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'initial_test_finished'
		);
		$cols['result_qualifing_tests'] = array(
			'txt' => $this->pl->txt('cols_results_qualifying'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'result_qualifing_tests'
		);
		$cols['results_qualifing_tests'] = array(
			'txt' => $this->pl->txt('cols_result_qualifying'),
			'default' => false,
			'width' => 'auto',
			'sort_field' => 'results_qualifing_tests'
		);
		$cols['eMentoring_finished'] = array(
			'txt' => $this->pl->txt('cols_eMentoring_finished'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'eMentoring_finished'
		);
		$cols['eMentoring_homework'] = array(
			'txt' => $this->pl->txt('cols_eMentoring_homework'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'eMentoring_homework'
		);
		$cols['eMentoring_percentage'] = array(
			'txt' => $this->pl->txt('cols_eMentoring_percentage'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'eMentoring_percentage'
		);

		return $cols;
	}


	private function addColumns() {
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if ($cert_access->hasCurrentUserPrintAccess()) {
			$this->addColumn('', '', '', true);
		}
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
		if (!$this->getExportMode()) {
			$this->addColumn($this->pl->txt('cols_actions'));
		}
	}


	public function parseData() {


		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$arr_learn_reached_percentages = ilLearnObjectSuggReachedPercentages::getData($this->usr_ids);
		$arr_iass_states = ilIassStates::getData($this->usr_ids);
		$arr_new_iass_states = ilIassStatesMulti::getData($this->usr_ids);
		$arr_excercise_states = ilExcerciseStates::getData($this->usr_ids);
		$arr_FinalTestsStates = ilLearnObjectFinalTestOfSuggStates::getData($this->usr_ids);


		$rows = array();
		foreach ($this->usr_ids as $usr_id) {
			$row = array();
			$row['usr_id'] = $usr_id;
			if ($row['firstname'] = $arr_usr_data[$usr_id]->getPartCertFirstname() != NULL) {
				$row['firstname'] = $arr_usr_data[$usr_id]->getPartCertFirstname();
			} else {
				$row['firstname'] = '';
			}
			if ($row['lastname'] = $arr_usr_data[$usr_id]->getPartCertLastname() != NULL) {
				$row['lastname'] = $arr_usr_data[$usr_id]->getPartCertLastname();
			}
			else{
				$row['lastname'] = '';
			}

			if (is_object($arr_initial_test_states[$usr_id])) {
				$row['initial_test_finished'] = $arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted();
				if ($row['initial_test_finished'] == 1) {
					$row['initial_test_finished'] = 'Ja';
				} else {
					$row['initial_test_finished'] = 'Nein';
				}
			} else {
				$row['initial_test_finished'] = 'Nein';
			}
			if (is_object($arr_learn_reached_percentages[$usr_id])) {
				$row['result_qualifing_tests'] = $arr_learn_reached_percentages[$usr_id]->getAveragePercentage() . '%';
			} else {
				$row['result_qualifing_tests'] = 0 . '%';
			}


			if (is_array($arr_FinalTestsStates[$usr_id])) {

				foreach ($arr_FinalTestsStates[$usr_id] as $rec) {
					/**
					 * @var ilLearnObjectFinalTestOfSuggState $rec
					 */
					$rec_array[] = $rec->getLocftestObjectiveTitle().'<br/>'.$rec->getLocftestTestTitle().'<br/>'.round($rec->getLocftestPercentage(),0). '%<br/>';
				}
				$array_results = $rec_array;
				$row['results_qualifing_tests'] = $array_results;
			}
			else{
				$row['results_qualifing_tests'] = 'Keine Tests';
			}

			$countPassed = 0;
			$countTests = 0;
			if (is_array($arr_new_iass_states[$usr_id])){
				foreach ($arr_new_iass_states[$usr_id] as $item){
					$countPassed = $countPassed + $item->getPassed();
					$countTests = $countTests + $item->getTotal();
				}
				$row['eMentoring_finished'] = $countPassed . "/" .  $countTests;
			}
			else {
				$row['eMentoring_finished'] = "0/0";
			}
			/*
			if (is_object($arr_iass_states[$usr_id])) {
				$row['eMentoring_finished'] = $arr_iass_states[$usr_id]->getPassed();

				if ($row['eMentoring_finished'] >= 1) {
					$row['eMentoring_finished'] = 'Ja';
				} else {
					$row['eMentoring_finished'] = 'Nein';
				}
			} else {
				$row['eMentoring_finished'] = 'Nein';
			}
*/
			if (is_object($arr_excercise_states[$usr_id])) {
				$row['eMentoring_homework'] = $arr_excercise_states[$usr_id]->getPassed();
				$row['eMentoring_percentage'] = $arr_excercise_states[$usr_id]->getPassedPercentage() . '%';
			} else {
				$row['eMentoring_homework'] = 0;
				$row['eMentoring_percentage'] = 0 . '%';
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
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if ($cert_access->hasCurrentUserPrintAccess()) {
			$this->tpl->setCurrentBlock('record_id');
			$this->tpl->setVariable('RECORD_ID', $a_set['usr_id']);
			$this->tpl->parseCurrentBlock();
		}

		foreach ($this->getSelectableColumns() as $k => $v) {
			if($this->isColumnSelected($k)) {
				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode("<br/>", $a_set[$k]) : $a_set[$k]));
				$this->tpl->parseCurrentBlock();
			}
		}
		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('list_actions'));
		$current_selection_list->setId('_actions' . $a_set['usr_id']);
		$current_selection_list->setUseImages(false);
		$this->ctrl->setParameterByClass('ilParticipationCertificateResultGUI', 'usr_id', $a_set['usr_id']);

		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if ($cert_access->hasCurrentUserPrintAccess()) {
			$this->ctrl->setParameterByClass('ilParticipationCertificateResultGUI', 'ementor', true);
			$current_selection_list->addItem($this->pl->txt('list_print'), ilParticipationCertificateResultGUI::CMD_PRINT_PDF, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_PRINT_PDF));
			$this->ctrl->setParameterByClass('ilParticipationCertificateResultGUI', 'ementor', false);
			$current_selection_list->addItem($this->pl->txt('list_print_without'), ilParticipationCertificateResultGUI::CMD_PRINT_PDF, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_PRINT_PDF));
			$current_selection_list->addItem($this->pl->txt('list_results'), ilParticipationCertificateResultModificationGUI::CMD_DISPLAY, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultModificationGUI::class, ilParticipationCertificateResultModificationGUI::CMD_DISPLAY));
		}
			$current_selection_list->addItem($this->pl->txt('list_overview'), ilParticipationCertificateSingleResultGUI::CMD_DISPLAY, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateSingleResultGUI::class, ilParticipationCertificateSingleResultGUI::CMD_DISPLAY));


		$this->tpl->setVariable('ACTIONS', $current_selection_list->getHTML());
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * @param object $a_worksheet
	 * @param int    $a_row
	 * @param array  $a_set
	 */
	protected function fillRowExcel(ilExcel $a_excel, &$a_row, $a_set) {
		$col = 0;

		foreach ($a_set as $key => $value) {
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			if ($this->isColumnSelected($key)) {
				$a_excel->setCell($a_row, $col, strip_tags($value));
				$col ++;
			}
		}
	}


	/**
	 * @param object $a_csv
	 * @param array  $a_set
	 */
	protected function fillRowCSV($a_csv, $a_set) {
		foreach ($a_set as $key => $value) {
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			if ($this->isColumnSelected($key)) {
				$a_csv->addColumn(strip_tags($value));
			}
		}
		$a_csv->addRow();
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

		foreach ($custom_fields as $format_key) {
			if (isset($this->custom_export_formats[$format_key])) {
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
	public function addCustomExportGenerator($export_format_key, $custom_export_generators, $params = array()) {
		$this->custom_export_generators[$export_format_key] = array( 'generator' => $custom_export_generators, 'params' => $params );
	}


	/**
	 * @param $custom_export_format_key
	 * @param $custom_export_format_label
	 */
	public function addCustomExportFormat($custom_export_format_key, $custom_export_format_label) {
		$this->custom_export_formats[$$custom_export_format_key] = $custom_export_format_label;
	}
}

?>