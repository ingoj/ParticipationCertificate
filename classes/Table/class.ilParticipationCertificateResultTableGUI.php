<?php

/**
 * Class ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateResultTableGUI extends ilTable2GUI {

	CONST IDENTIFIER = 'ilpartusr';
	const GREEN_PROGRESS = "ilCourseObjectiveProgressBarCompleted";
	const ORANGE_PROGRESS = "progress-bar-warning";
	const RED_PROGRESS = "ilCourseObjectiveProgressBarFailed";
	const NO_PROGRESS = "ilCourseObjectiveProgressBarNeutral";
	protected ilTabsGUI $tabs;
	protected ilCtrl $ctrl;
	/**
	 * @var ilParticipationCertificateResultGUI
	 */
	protected ?object $parent_obj;
	protected ilParticipationCertificatePlugin $pl;
	protected array $filter = array();
	protected array $custom_export_formats = array();
	protected array $custom_export_generators = array();
	protected array $usr_ids;
    protected ?string $ementoring = null;

	public function __construct(ilParticipationCertificateResultGUI $a_parent_obj, string $a_parent_cmd) {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->setPrefix('dhbw_part_cert');
		$this->setFormName('dhbw_part_cert');
		$this->setId('dhbw_part_cert');

		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$ementoring=ilParticipationCertificateConfig::getConfig('enable_ementoring', $_GET['ref_id']);
		if ($ementoring === NULL) {
			$ementoring = true;
			} else {
			$ementoring = boolval($ementoring);
			}
		$this->ementoring = $ementoring;
		$this->getEnableHeader();
		$this->setTitle($this->pl->txt('tbl_overview_results'));
		$this->addColumns();
		$this->setExportFormats(array( self::EXPORT_EXCEL, self::EXPORT_CSV ));
		if ($cert_access->hasCurrentUserWriteAccess()) {
			$this->initFilter();
			$this->setSelectAllCheckbox('record_ids');
			if ($this->ementoring) {
				$this->addMultiCommand(ilParticipationCertificateResultGUI::CMD_PRINT_SELECTED, $this->pl->txt('list_print_with'));
				$this->addMultiCommand(ilParticipationCertificateResultGUI::CMD_PRINT_SELECTED_WITHOUTE_MENTORING, $this->pl->txt('list_print_without'));
			} else {
				$this->addMultiCommand(ilParticipationCertificateResultGUI::CMD_PRINT_SELECTED_WITHOUTE_MENTORING, $this->pl->txt('list_print'));
			}
			$this->addMultiCommand(ilParticipationCertificateMultipleResultGUI::CMD_SHOW_ALL_RESULTS, $this->pl->txt('list_overview'));
			}	
       			
		
		$this->setRowTemplate('tpl.default_row.html', $this->pl->getDirectory());
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->parseData();
	}

	function getSelectableColumns(): array
    {
		$cols = array();
		//$cols['usr_id'] = array( 'txt' => 'usr_id', 'default' => false, 'width' => 'auto', 'sort_field' => 'usr_id' );
		//access-dependent defaults via $write_access
        $cert_access = new ilParticipationCertificateAccess($_GET["ref_id"]);
		$write_access = $cert_access->hasCurrentUserWriteAccess();
		$cols['loginname'] = array( 
			'txt' => $this->pl->txt('loginname'), 
			'default' => $write_access, 
			'width' => 'auto', 
			'sort_field' => 'loginname' 
		);
		$cols['firstname'] = array( 
			'txt' => $this->pl->txt('cols_firstname'), 
			'default' => true, 
			'width' => 'auto', 
			'sort_field' => 'firstname' 
		);
		$cols['lastname'] = array( 
			'txt' => $this->pl->txt('cols_lastname'), 
			'default' => true, 
			'width' => 'auto', 
			'sort_field' => 'lastname' 
		);
	        if ($write_access) {
	    		$cols['alias_firstname'] = array(
				'txt' => $this->pl->txt('cols_alias_firstname'),
				'default' => false,
				'width' => 'auto',
   				'sort_field' => 'alias_firstname'
      			);
	 		$cols['alias_lastname'] = array(
    				'txt' => $this->pl->txt('cols_alias_lastname'),
				'default' => false,
				'width' => 'auto',
    				'sort_field' => 'alias_lastname'
			);
		}
		$cols['initial_test_finished'] = array(
			'txt' => $this->pl->txt('cols_initial_test_finished'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'initial_test_finished'
		);
		$cols['result_qualifing_tests'] = array(
			'txt' => $this->pl->txt('cols_result_qualifying'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'result_qualifing_tests'
		);
		$cols['results_qualifing_tests'] = array(
			'txt' => $this->pl->txt('cols_results_qualifying'),
			'default' => false,
			'width' => 'auto',
			'sort_field' => 'result_qualifing_tests'
		);
		$cols['eMentoring_finished'] = array(
			'txt' => $this->pl->txt('cols_eMentoring_finished'),
			'default' => $this->ementoring,
			'width' => 'auto',
			'sort_field' => 'eMentoring_finished'
		);
		$cols['eMentoring_homework'] = array(
			'txt' => $this->pl->txt('cols_eMentoring_homework'),
			'default' => $this->ementoring,
			'width' => 'auto',
			'sort_field' => 'eMentoring_homework'
		);
		$cols['eMentoring_percentage'] = array(
			'txt' => $this->pl->txt('cols_eMentoring_percentage'),
			'default' => $this->ementoring,
			'width' => 'auto',
			'sort_field' => 'eMentoring_percentage'
		);

		return $cols;
	}
	private function addColumns(): void
    {
		$this->addColumn('invisible', '', 'invisible', true);
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if (isset($v['sort_field'])) {
					$sort = $v['sort_field'];
				} else {
					$sort = NULL;
				}
				if ($sort == 'results_qualifying_tests') 
				{
					$sort = 'result_qualifying_tests';
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
		if (!$this->getExportMode()) {
			$this->addColumn($this->pl->txt('cols_actions'));
		}
	}
	public function parseData(): array
    {
		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$arr_learn_reached_percentages = ilLearnObjectSuggResults::getData($this->usr_ids);


		$arr_final_tests = ilLearnObjectFinalTestStates::getData($this->usr_ids);

		$arr_new_iass_states = ilIassStatesMulti::getData($this->usr_ids,$_GET['ref_id']);

		$arr_xali_states = xaliStates::getData($this->usr_ids,$_GET['ref_id']);


		$arr_excercise_states = ilExcerciseStates::getData($this->usr_ids,$_GET['ref_id']);
		//$arr_FinalTestsStates = ilLearnObjectFinalTestOfSuggStates::getData($this->usr_ids);

		$rows = array();
		foreach ($this->usr_ids as $usr_id) {
			$row = array();
			$row['usr_id'] = $usr_id;
			$row['loginname'] = $arr_usr_data[$usr_id]->getPartCertUserName();
			if ($arr_usr_data[$usr_id]->getPartCertFirstname()  != NULL) {
				$row['firstname'] = $arr_usr_data[$usr_id]->getPartCertFirstname();
			} else {
				$row['firstname'] = '';
			}
			if ($arr_usr_data[$usr_id]->getPartCertLastname() != NULL) {
				$row['lastname'] = $arr_usr_data[$usr_id]->getPartCertLastname();
			} else {
				$row['lastname'] = '';
			}
			$row['alias_firstname'] = $arr_usr_data[$usr_id]->getPartCertAliasFirstname();
			$row['alias_lastname']  = $arr_usr_data[$usr_id]->getPartCertAliasLastname();

			if (key_exists($usr_id, $arr_initial_test_states) && is_object($arr_initial_test_states[$usr_id])) {
				$row['initial_test_finished'] = $arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted();
				if ($row['initial_test_finished'] == 1) {
					$row['initial_test_finished'] = $this->pl->txt("yes");
				} else {
					$row['initial_test_finished'] = $this->pl->txt("no");
				}
			} else {
				$row['initial_test_finished'] = $this->pl->txt("no");
			}
			if ((key_exists($usr_id, $arr_learn_reached_percentages)) && (is_object($arr_learn_reached_percentages[$usr_id]))) {


				$row['result_qualifing_tests'] = $this->buildProgressBar($arr_learn_reached_percentages[$usr_id]->getAveragePercentage(ilParticipationCertificateConfig::getConfig('calculation_type_processing_state_suggested_objectives',$_GET['ref_id'])
                ), $arr_learn_reached_percentages[$usr_id]->getLimitPercentage());


			} else {
				//$row['result_qualifing_tests'] = 0 . '%';
				$row['result_qualifing_tests'] = $this->buildProgressBar(0,0);
			}

			$rec_array = [];
			$array_results = [];

			if (key_exists($usr_id, $arr_final_tests) && (is_array($arr_final_tests[$usr_id]))) {

				foreach ($arr_final_tests[$usr_id] as $usr_objectives) {

                    if (is_array($usr_objectives)) {
                        foreach ($usr_objectives as $rec) {

                            if ($rec->getObjectivesSuggested()) {

                                if (!array_key_exists($rec->getLocftestObjectiveId(), $rec_array)) {
                                    $rec_array[$rec->getLocftestObjectiveId()] = $rec->getLocftestLearnObjectiveTitle() . '<br/>';
                                }

                                /**
                                 * @var ilLearnObjectFinalTestState $rec
                                 */
                                $rec_array[$rec->getLocftestObjectiveId()] .= '- ' . round($rec->getLocftestPercentage(),
                                        0) . '% ' . $rec->getLocftestObjectiveTitle() . '<br/>';
                            }
                        }
                    }

				}
				$array_results = $rec_array;
				$row['results_qualifing_tests'] = $array_results;
			} else {
				$row['results_qualifing_tests'] = $this->pl->txt("no_tests");
			}

			$countPassed = 0;
			$countTests = 0;
			if (key_exists($usr_id, $arr_new_iass_states) && is_array($arr_new_iass_states[$usr_id])) {
                foreach ($arr_new_iass_states[$usr_id] as $item) {
                    $countPassed = $countPassed + $item->getPassed();
                    $countTests = $countTests + $item->getTotal();
                }
            }

            if (key_exists($usr_id, $arr_xali_states) && is_object($arr_xali_states[$usr_id])) {
                $countPassed = $countPassed + $arr_xali_states[$usr_id]->getPassed();
                $countTests = $countTests + $arr_xali_states[$usr_id]->getTotal();
            }

            if($countTests > 0) {
                $percentage = $countPassed / $countTests * 100;



				switch ($countTests) {
					case 1:
						if ($countPassed == 1) {
							$row['eMentoring_finished'] = ilUtil::img($this->pl->getImagePath("passed.svg"));
						} else {
							$row['eMentoring_finished'] = ilUtil::img($this->pl->getImagePath("failed.svg"));
						}
						break;
					default:
						$row['eMentoring_finished'] = $countPassed . "/" . $countTests;
						break;
				}
			} else {
						$row['eMentoring_finished'] = ilUtil::img($this->pl->getImagePath("not_attempted.svg"));
			}

			if (key_exists($usr_id, $arr_excercise_states) && is_object($arr_excercise_states[$usr_id])) {
				$row['eMentoring_homework'] = $arr_excercise_states[$usr_id]->getPassed();
				//$row['eMentoring_percentage'] = $arr_excercise_states[$usr_id]->getPassedPercentage() . '%';
				$row['eMentoring_percentage'] = $this->buildProgressBar($arr_excercise_states[$usr_id]->getPassedPercentage(),0);
			} else {
				$row['eMentoring_homework'] = 0;
				//$row['eMentoring_percentage'] = 0 . '%';
				$row['eMentoring_percentage'] = $this->buildProgressBar(0,0);
			}

			if ((key_exists('firstname',$this->filter)) && ($this->filter['firstname'] != false)) {
				if (strtolower($row['firstname']) == strtolower($this->filter['firstname'])) {
					$rows[] = $row;
				}
			} elseif ((key_exists('lastname',$this->filter)) &&($this->filter['lastname'] != false)) {
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
	protected function buildProgressBar(int $a_perc_result, int $a_perc_limit): string
    {
		$groupRefId = filter_input(INPUT_GET, 'ref_id');

		$start = ilParticipationCertificateConfig::getConfig('period_start', $groupRefId);
		$end = ilParticipationCertificateConfig::getConfig('period_end', $groupRefId);

		if ($start !== NULL && $end !== NULL) {
			// Period set
			$start = new DateTime($start);
			$end = new DateTime($end);
			$current = new DateTime();

			// Test
			/*$start = new DateTime("2018-01-01");
			$end = new DateTime("2018-06-30");
			$current = new DateTime("2018-03-26");*/

			if ($current >= $start) {
				if ($current <= $end) {
					// Running
					$rest_days = $end->diff($current)->days;
					$total_days = max(1, $end->diff($start)->days);
					$perc_limit = (100 - ($rest_days / $total_days * 100));
				} else {
					 // Ended
					 $perc_limit = 100;
				}



				if ($a_perc_result >= 90) {
					// 90% reached
					$css_class = self::GREEN_PROGRESS;
				} else {
					// <90%
					if ($current <= $end) {
						// End not reached
						if ($a_perc_result >= ($perc_limit - 30)) {
							// In time or already farer
							$css_class = self::GREEN_PROGRESS;
						} else {
							if ($a_perc_result >= ($perc_limit - 40)) {
								//
								$css_class = self::ORANGE_PROGRESS;
							} else {
								// Not in time
								$css_class = self::RED_PROGRESS;
							}
						}
					} else {
						// End reached
						$css_class = self::RED_PROGRESS;
					}
				}
				if ($perc_limit < 30) {
					//
					$perc_limit = 30;
				}
			} else {
				// Not started
				$perc_limit = 1;
				$css_class = self::NO_PROGRESS;
			}
		} else {
			// No period set
			$perc_limit = NULL;

			if ($a_perc_result >= 80) {
				// 80% reached
				$css_class = self::GREEN_PROGRESS;
			} else {
				// <80%
				$css_class = self::RED_PROGRESS;
			}
		}

		return ilContainerObjectiveGUI::renderProgressBar($a_perc_result, $perc_limit, $css_class);
	}
	public function fillRow(array $a_set): void
    {
		$this->tpl->setCurrentBlock('record_id');
		$this->tpl->setVariable('RECORD_ID', $a_set['usr_id']);
		$this->tpl->parseCurrentBlock();

		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode("<br/>", $a_set[$k]) : $a_set[$k]));
				$this->tpl->parseCurrentBlock();
			}
		}
		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle($this->pl->txt('list_actions'));
		$current_selection_list->setId('_actions' . $a_set['usr_id']);
		$current_selection_list->setUseImages(false);
		$this->ctrl->setParameterByClass(ilParticipationCertificateResultGUI::class, 'usr_id', $a_set['usr_id']);

		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if ($cert_access->hasCurrentUserPrintAccess()) {
			if ($this->ementoring) {
				$this->ctrl->setParameterByClass(ilParticipationCertificateResultGUI::class, 'ementor', true);
				$current_selection_list->addItem($this->pl->txt('list_print_with'), ilParticipationCertificateResultGUI::CMD_PRINT_PDF, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_PRINT_PDF));
				$this->ctrl->setParameterByClass(ilParticipationCertificateResultGUI::class, 'ementor', false);
				$current_selection_list->addItem($this->pl->txt('list_print_without'), ilParticipationCertificateResultGUI::CMD_PRINT_PDF, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_PRINT_PDF));
				} else {
				$this->ctrl->setParameterByClass(ilParticipationCertificateResultGUI::class, 'ementor', false);
				$current_selection_list->addItem($this->pl->txt('list_print'), ilParticipationCertificateResultGUI::CMD_PRINT_PDF, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_PRINT_PDF));
			}
		}
		$current_selection_list->addItem($this->pl->txt('list_overview'), ilParticipationCertificateSingleResultGUI::CMD_DISPLAY, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateSingleResultGUI::class, ilParticipationCertificateSingleResultGUI::CMD_DISPLAY)); // TODO: Call to undefined method ilParticipationCertificateResultGUI::display()
		if ($cert_access->hasCurrentUserWriteAccess()) {
			$current_selection_list->addItem($this->pl->txt('list_results'), ilParticipationCertificateResultModificationGUI::CMD_DISPLAY, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultModificationGUI::class, ilParticipationCertificateResultModificationGUI::CMD_DISPLAY));
			}

		$this->tpl->setVariable('ACTIONS', $current_selection_list->getHTML());
		$this->tpl->parseCurrentBlock();
	}
	protected function fillRowExcel(ilExcel $a_excel, int &$a_row, array $a_set): void
    {
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
	protected function fillRowCSV(object $a_csv, array $a_set): void
    {
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
	public function initFilter(): void
    {
		$firstname = new ilTextInputGUI($this->pl->txt('firstname'), 'firstname');
		$lastname = new ilTextInputGUI($this->pl->txt('lastname'), 'lastname');

		$this->addAndReadFilterItem($firstname);
		$this->addAndReadFilterItem($lastname);

		$firstname->readFromSession();
		$lastname->readFromSession();

		$this->filter['firstname'] = $firstname->getValue();
		$this->filter['lastname'] = $lastname->getValue();
	}
	public function addAndReadFilterItem(ilFormPropertyGUI $item): void
    {
		$this->addFilterItem($item);
		$item->readFromSession();

		$this->filter[$item->getPostVar()] = $item->getValue();

		$this->setDisableFilterHiding(true);
	}
	public function setExportFormats(array $formats): void
    {
		parent::setExportFormats($formats);

		$custom_fields = array_diff($formats, $this->export_formats);

		foreach ($custom_fields as $format_key) {
			if (isset($this->custom_export_formats[$format_key])) {
				$this->export_formats[$format_key] = $this->pl->getPrefix() . '_' - $this->custom_export_formats[$format_key];
			}
		}
	}
	public function exportData(int $format, bool $send = false): void
    {
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
	public function addCustomExportGenerator(string $export_format_key, object $custom_export_generators, array $params = array()): void
    {
		$this->custom_export_generators[$export_format_key] = array( 'generator' => $custom_export_generators, 'params' => $params );
	}
	public function addCustomExportFormat(string $custom_export_format_key, string $custom_export_format_label): void
    {
		$this->custom_export_formats[$$custom_export_format_key] = $custom_export_format_label;
	}
}
