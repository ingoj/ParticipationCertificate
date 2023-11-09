<?php

/**
 * Class ilParticipationCertificateResultTableGUI
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilParticipationCertificateConfigSetTableGUI extends ilTable2GUI {
	protected ilTabsGUI $tabs;
    protected ilCtrl $ctrl;
	protected ?object $parent_obj;
	protected ilParticipationCertificatePlugin $pl;
	protected array $filter = array();
	protected array $custom_export_formats = array();
	protected array $custom_export_generators = array();
	protected array $usr_ids;

	public function __construct(ilParticipationCertificateConfigGUI $a_parent_obj, string $a_parent_cmd) {
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

		$button = ilLinkButton::getInstance();
		$button->setCaption($this->pl->txt('reset_config'), false);
		$button->setUrl($this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_CONFIRM_RESET_CONFIG));
		$toolbar->addButtonInstance($button);

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->addColumns();
		//$this->initFilter();

		$this->addMultiCommand(ilParticipationCertificateConfigGUI::CMD_SAVE_ORDER, $this->pl->txt('save_order'));
		$this->setRowTemplate('tpl.default_row.html', $this->pl->getDirectory());
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->parseData();
	}


	/**
	 * Get selectable columns
	 */
	function getSelectableColumns(): array
    {
		$cols = array();
		$cols['order_by'] = array( 'txt' => $this->pl->txt('order_by'), 'default' => false, 'width' => 'auto' );
		$cols['configset_type'] = array( 'txt' => $this->pl->txt('config_type'), 'default' => true, 'width' => 'auto' );
		$cols['title'] = array( 'txt' => $this->pl->txt('title'), 'default' => true, 'width' => 'auto' );
		$cols['parent_title'] = array( 'txt' => $this->pl->txt('parent_title'), 'default' => true, 'width' => 'auto' );
		$cols['active'] = array( 'txt' => $this->pl->txt('active'), 'default' => true, 'width' => 'auto' );

		return $cols;
	}

	private function addColumns(): void
    {
		//$this->addColumn('', '', '', true);
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if (isset($v['sort_field'])) {
					$sort = $v['sort_field'];
				} else {
					$sort = "";
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
		if (!$this->getExportMode()) {
			$this->addColumn($this->pl->txt('cols_actions'));
		}
	}

	public function parseData(): void
    {
		$global_configs = new ilParticipationCertificateConfigSets();
		$this->setData($global_configs->getAllConfigSets());
	}

	public function fillRow(array $a_set): void
    {
		global $DIC;
		foreach ($this->getSelectableColumns() as $k => $v) {

			if ($this->isColumnSelected($k)) {

				switch ($k) {
					case 'order_by':
						$value = intval($a_set[$k]) * 10;
						$this->tpl->setCurrentBlock('td');
						if ($a_set[$k] > 0) {
							$number_input_gui = new ilNumberInputGUI('', 'order_by[' . $a_set['conf_id'] . ']');
							$number_input_gui->setSize(3);
							$number_input_gui->setValue($value);

							$this->tpl->setVariable('VALUE', $number_input_gui->render());
						} else {
							$this->tpl->setVariable('VALUE', "&nbsp");
						}

						$this->tpl->parseCurrentBlock();
						break;
					case 'configset_type':
						$this->tpl->setCurrentBlock('td');
						if ($a_set[$k] > 0) {
							switch ($a_set['configset_type']) {
								case ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP:
									if(!ilParticipationCertificateGlobalConfigSet::find($a_set['object_gl_conf_template_id'])) {
										$this->tpl->setVariable('VALUE',"&nbsp");
										break;
									}
									$arr_type[] =  $this->pl->txt('configset_type_' . $a_set[$k]);
									$arr_type[] = $this->pl->txt('object_config_type_' . $a_set['object_config_type']);
									$template = new ilParticipationCertificateGlobalConfigSet($a_set['object_gl_conf_template_id']);
									$arr_type[] = $this->pl->txt('origin_template') . ": " . $template->getTitle();
									$this->tpl->setVariable('VALUE', implode($arr_type, "<br/>"));
									break;
								default:
									$this->tpl->setVariable('VALUE', $this->pl->txt('configset_type_' . $a_set[$k]));
									break;
							}
						} else {
							$this->tpl->setVariable('VALUE', "&nbsp");
						}
						$this->tpl->parseCurrentBlock();
						break;
					case "active":
						$factory = $DIC->ui()->factory();
						if ($a_set[$k] == 1) {
							$this->tpl->setVariable('VALUE',$DIC->ui()->renderer()->render($factory->image()->standard(  ilUtil::getImagePath("on.svg"), '')));
						} else {
							$this->tpl->setVariable('VALUE',$DIC->ui()->renderer()->render($factory->image()->standard( ilUtil::getImagePath("off.svg"), '')));
						}
						break;
					default:
						$this->tpl->setCurrentBlock('td');
						$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode("<br/>", $a_set[$k]) : ($a_set[$k] ? $a_set[$k] : "&nbsp;")));
						$this->tpl->parseCurrentBlock();
						break;
				}
			}
		}

		$action_list = new ilAdvancedSelectionListGUI();
		$action_list->setListTitle($this->pl->txt('list_actions'));
        if(array_key_exists('id', $a_set)) {
            $action_list->setId('_actions' . $a_set['id']);
        }
		$action_list->setUseImages(false);



		switch ($a_set['configset_type']) {
			case  ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL:
                $this->ctrl->setParameterByClass(ilParticipationCertificateConfigGUI::class, 'id', $a_set['conf_id']);
				$this->ctrl->setParameterByClass(ilParticipationCertificateConfigGUI::class, 'set_type', ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
				$action_list->addItem($this->pl->txt('edit'), ilParticipationCertificateConfigGUI::CMD_SHOW_FORM, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_SHOW_FORM));
				break;
			case  ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE:
                $this->ctrl->setParameterByClass(ilParticipationCertificateConfigGUI::class, 'id', $a_set['conf_id']);
				$this->ctrl->setParameterByClass(ilParticipationCertificateConfigGUI::class, 'set_type', ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
				$action_list->addItem($this->pl->txt('edit'), ilParticipationCertificateConfigGUI::CMD_SHOW_FORM, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_SHOW_FORM));
				$action_list->addItem($this->pl->txt('copy'), ilParticipationCertificateConfigGUI::CMD_COPY_CONFIG, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_COPY_CONFIG));
				if ($a_set['order_by'] != 1) {
					$action_list->addItem($this->pl->txt('delete'), ilParticipationCertificateConfigGUI::CMD_DELETE_CONFIG, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_DELETE_CONFIG));

					if ($a_set['active'] == 1) {
						$action_list->addItem($this->pl->txt('set_inactive'), ilParticipationCertificateConfigGUI::CMD_SET_INACTIVE, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_SET_INACTIVE));
					}
				}

				if ($a_set['active'] == 0) {
					$action_list->addItem($this->pl->txt('set_active'), ilParticipationCertificateConfigGUI::CMD_SET_ACTIVE, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_SET_ACTIVE));
				}
				break;
			case  ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP:
				$action_list->addItem($this->pl->txt('go_to_object'), $a_set['obj_ref_id'], ilLink::_getStaticLink($a_set['obj_ref_id']));

                $this->ctrl->setParameterByClass(ilParticipationCertificateConfigGUI::class, 'grp_ref_id', $a_set['obj_ref_id']);

                $action_list->addItem($this->pl->txt('create_template'), ilParticipationCertificateConfigGUI::CMD_CREATE_TEMPLATE_FRON_LOCAL_CONFIG, $this->ctrl->getLinkTargetByClass(ilParticipationCertificateConfigGUI::class, ilParticipationCertificateConfigGUI::CMD_CREATE_TEMPLATE_FRON_LOCAL_CONFIG));
				break;
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

	public function initFilter(): void
    {
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


	public function setExportFormats(array $formats): void
    {
		/*parent::setExportFormats($formats);

		$custom_fields = array_diff($formats, $this->export_formats);

		foreach ($custom_fields as $format_key) {
			if (isset($this->custom_export_formats[$format_key])) {
				$this->export_formats[$format_key] = $this->pl->getPrefix() . '_' - $this->custom_export_formats[$format_key];
			}
		}*/
	}

	public function exportData(int $format, bool $send = false): void
    {
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
