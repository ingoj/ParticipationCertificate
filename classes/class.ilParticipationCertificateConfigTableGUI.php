<?php

/**
 * Class ilParticipationCertificateResultTableGUI
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilParticipationCertificateConfigTableGUI extends ilTable2GUI {

	/*CONST IDENTIFIER = 'ilpartusr';
	const GREEN_PROGRESS = "ilCourseObjectiveProgressBarCompleted";
	const ORANGE_PROGRESS = "progress-bar-warning";
	const RED_PROGRESS = "ilCourseObjectiveProgressBarFailed";
	const NO_PROGRESS = "ilCourseObjectiveProgressBarNeutral";*/
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificateConfigTableGUI
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
	 * @var array
	 */
	protected $custom_export_formats = array();
	/**
	 * @var array
	 */
	protected $custom_export_generators = array();
	/**
	 * @var array
	 */
	protected $usr_ids;


	/**
	 *
	 * @param ilParticipationCertificateConfigGUI $a_parent_obj
	 * @param string                              $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd) {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->setPrefix('dhbw_cert_conf');
		$this->setFormName('dhbw_cert_conf');
		$this->setId('dhbw_cert_conf');

		$toolbar = $DIC->toolbar();
		$button = ilLinkButton::getInstance();
		$button->setCaption($this->pl->txt('add_config'), false);
		$button->setUrl($this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_ADD_CONFIG));
		$toolbar->addButtonInstance($button);


		parent::__construct($a_parent_obj, $a_parent_cmd);

		//$this->getEnableHeader();
		//$this->setTitle($this->pl->txt('tbl_overview_results'));
		$this->addColumns();
		//$this->setExportFormats(array( self::EXPORT_EXCEL, self::EXPORT_CSV ));

		$this->initFilter();
		//$this->setSelectAllCheckbox('record_ids');
		/*if ($cert_access->hasCurrentUserPrintAccess()) {
			$this->addMultiCommand(ilParticipationCertificateResultGUI::CMD_PRINT_SELECTED, $this->pl->txt('list_print'));
			$this->addMultiCommand(ilParticipationCertificateResultGUI::CMD_PRINT_SELECTED_WITHOUTE_MENTORING, $this->pl->txt('list_print_without'));
		}
		$this->addMultiCommand(ilParticipationCertificateMultipleResultGUI::CMD_SHOW_ALL_RESULTS, $this->pl->txt('list_overview'));*/
		$this->addMultiCommand(ilParticipationCertificateConfigGUI::CMD_SAVE_ORDER, $this->pl->txt('save_order'));
		$this->setRowTemplate('tpl.default_row.html', $this->pl->getDirectory());
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->parseData();
	}


	/**
	 * Get selectable columns
	 *
	 * @return array    key: column id, val: true/false -> default on/off
	 */
	function getSelectableColumns() {
		$cols = array();
		$cols['order_by'] = array( 'txt' => $this->pl->txt('order_by'), 'default' => false, 'width' => 'auto');
		$cols['title'] = array( 'txt' => $this->pl->txt('title'), 'default' => true, 'width' => 'auto');
		$cols['active'] = array( 'txt' => $this->pl->txt('active'), 'default' => true, 'width' => 'auto');
		return $cols;
	}


	/**
	 *
	 */
	private function addColumns() {
		$this->addColumn('', '', '', true);
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
		$global_configs = new ilParticipationCertificateGlobalConfigs();
		$this->setData($global_configs->getAllConfigsAsArray());
	}



	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$this->tpl->setCurrentBlock('record_id');
		$this->tpl->setVariable('RECORD_ID', $a_set['id']);
		$this->tpl->parseCurrentBlock();



		foreach ($this->getSelectableColumns() as $k => $v) {
			
			if ($this->isColumnSelected($k)) {

				switch ($k) {
					case 'order_by':
						$number_input_gui =  new ilNumberInputGUI('','order_by['.$a_set['id'].']');
						$value = intval($a_set[$k]) * 10;
						$number_input_gui->setSize(3);
						$number_input_gui->setValue($value);
						$this->tpl->setCurrentBlock('td');
						$this->tpl->setVariable('VALUE',$number_input_gui->render());
						$this->tpl->parseCurrentBlock();
						break;
					default:
						$this->tpl->setCurrentBlock('td');
						$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode("<br/>", $a_set[$k]) : ($a_set[$k]?$a_set[$k]:0)));
						$this->tpl->parseCurrentBlock();
					break;
				}


			}
		}

		$action_list = new ilAdvancedSelectionListGUI();
		$action_list->setListTitle($this->pl->txt('list_actions'));
		$action_list->setId('_actions' . $a_set['id']);
		$action_list->setUseImages(false);
		$this->ctrl->setParameterByClass(ilParticipationCertificateConfigGUI::class, 'id', $a_set['id']);

		$action_list->addItem($this->pl->txt('edit'), ilParticipationCertificateConfigGUI::CMD_SHOW_FORM, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_SHOW_FORM));

		$action_list->addItem($this->pl->txt('copy'), ilParticipationCertificateConfigGUI::CMD_COPY_CONFIG, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_COPY_CONFIG));

		if($a_set['order_by'] != 1) {
			$action_list->addItem($this->pl->txt('delete'), ilParticipationCertificateConfigGUI::CMD_DELETE_CONFIG, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_DELETE_CONFIG));

			if($a_set['active'] == 1) {
				$action_list->addItem($this->pl->txt('set_inactive'), ilParticipationCertificateConfigGUI::CMD_SET_INACTIVE, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_SET_INACTIVE));
			}
		}

		if($a_set['active'] == 0) {
			$action_list->addItem($this->pl->txt('set_active'), ilParticipationCertificateConfigGUI::CMD_SET_ACTIVE, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_SET_ACTIVE));
		}


		$this->tpl->setVariable('ACTIONS', $action_list->getHTML());
		$this->tpl->parseCurrentBlock();
	}


	/**
	 * @param ilExcel $a_excel
	 * @param int     $a_row
	 * @param array   $a_set
	 */
	/*protected function fillRowExcel(ilExcel $a_excel, &$a_row, $a_set) {
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
	}*/


	/**
	 * @param object $a_csv
	 * @param array  $a_set
	 */
	/*protected function fillRowCSV($a_csv, $a_set) {
		foreach ($a_set as $key => $value) {
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			if ($this->isColumnSelected($key)) {
				$a_csv->addColumn(strip_tags($value));
			}
		}
		$a_csv->addRow();
	}*/


	/**
	 *
	 */
	public function initFilter() {
		/*$firstname = new ilTextInputGUI($this->pl->txt('firstname'), 'firstname');
		$lastname = new ilTextInputGUI($this->pl->txt('lastname'), 'lastname');

		$this->addAndReadFilterItem($firstname);
		$this->addAndReadFilterItem($lastname);

		$firstname->readFromSession();
		$lastname->readFromSession();

		$this->filter['firstname'] = $firstname->getValue();
		$this->filter['lastname'] = $lastname->getValue();*/
	}


	/**
	 * @param ilFormPropertyGUI $item
	 */
	public function addAndReadFilterItem($item) {
		/*$this->addFilterItem($item);
		$item->readFromSession();

		$this->filter[$item->getPostVar()] = $item->getValue();

		$this->setDisableFilterHiding(true);*/
	}


	/**
	 * @param array $formats
	 */
	public function setExportFormats(array $formats) {
		/*parent::setExportFormats($formats);

		$custom_fields = array_diff($formats, $this->export_formats);

		foreach ($custom_fields as $format_key) {
			if (isset($this->custom_export_formats[$format_key])) {
				$this->export_formats[$format_key] = $this->pl->getPrefix() . '_' - $this->custom_export_formats[$format_key];
			}
		}*/
	}


	/**
	 * @param int  $format
	 * @param bool $send
	 */
	public function exportData($format, $send = false) {
		/*if (array_key_exists($format, $this->custom_export_formats)) {
			if ($this->dataExists()) {

				foreach ($this->custom_export_generators as $export_format => $generator_config) {
					if ($this->getExportMode() == $export_format) {
						$generator_config['generator']->generate();
					}
				}
			}
		} else {
			parent::exportData($format, $send);
		}*/
	}



}
