<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilParticipationCertificateGUI
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_Calls      ilParticipationCertificateGUI: ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateGUI {

	const CMD_SAVE = 'save';
	const CMD_CANCEL = 'cancel';
	const CMD_LOOP = 'loop';
	const CMD_CONFIG = 'config';
	const CMD_CONFIG_RESULT_TABLE = 'configResultTable';
	const CMD_RESULT_TABLE_CONFIG = 'saveResultTableConfig';
	const CMD_SELF_PRINT = 'selfPrint';
	const CMD_SELF_PRINT_SAVE = 'saveSelfPrint';
	const CMD_DISPLAY = 'display';

	const CMD_SET_CERT_TEMPLATE = 'setCertTemplate';
	const CMD_SET_OWN_CERT_TEXT_FROM_TEMPLATE = 'setOwnCertTextFromTemplate';




	/*const CMD_PRINT_PDF = 'printPdf';
	const CMD_PRINT_PDF_WITHOUT_MENTORING = 'printPdfWithoutMentoring';*/
	const TAB_CONFIG = 'config';
	const TAB_CONFIG_DISPLAY = 'config_display';
	const TAB_CONFIG_RESULT_TABLE = 'config_result_table';
	const TAB_CONFIG_SELF_PRINT = 'config_self_print';
	/**
	 * @var ilTemplate
	 */
	public $tpl;
	/**
	 * @var ilCtrl
	 */
	public $ctrl;
	/**
	 * @var ilTabsGUI
	 */
	public $tabs;
	/**
	 * @var ilGroupParticipants
	 */
	public $learnGroupParticipants;
	/**
	 * @var ilObjGroup
	 */
	public $learningGroup;
	/**
	 * @var ilParticipationCertificateConfig
	 */
	public $object;
	/**
	 * @var ilToolbarGUI
	 */
	public $toolbar;
	/**
	 * @var int
	 */
	public $groupRefId;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var ilLanguage
	 */
	protected $lng;


	/**
	 *
	 */
	function __construct() {
		global $DIC;

		$this->toolbar = $DIC->toolbar();
		$this->ctrl = $DIC->ctrl();
		$this->tpl = $DIC->ui()->mainTemplate();
		$this->tabs = $DIC->tabs();
		$this->objectDefinition = $DIC["objDefinition"];
		$this->groupRefId = (int)$_GET['ref_id'];
		$this->lng = $DIC->language();

		//Access
		$cert_access = new ilParticipationCertificateAccess($this->groupRefId);
		if (!$cert_access->hasCurrentUserAdminAccess()) {
			ilUtil::sendFailure($this->lng->txt('no_permission'), true);
			ilUtil::redirect('login.php');
		}
		$this->objecttype = ilObject::_lookupType($this->groupRefId, true);
		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($this->groupRefId);
		$this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, [ 'ref_id', 'group_id' ]);
	}


	/**
	 *
	 */
	function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		$nextClass = $this->ctrl->getNextClass();

		switch ($nextClass) {
			case strtolower(ilParticipationCertificateResultGUI::class):
				$ilParticipationCertificateTableGUI = new ilParticipationCertificateResultGUI();
				$this->ctrl->forwardCommand($ilParticipationCertificateTableGUI);
				break;
			default:
				switch ($cmd) {
					case self::CMD_CONFIG:
					case self::CMD_CONFIG_RESULT_TABLE:
					case self::CMD_DISPLAY:
					case self::CMD_SET_CERT_TEMPLATE:
					case self::CMD_SET_OWN_CERT_TEXT_FROM_TEMPLATE:
					case self::CMD_SAVE:
					case self::CMD_RESULT_TABLE_CONFIG:
					case self::CMD_SELF_PRINT:
					case self::CMD_SELF_PRINT_SAVE:
						/*case self::CMD_PRINT_PDF:
						case self::CMD_PRINT_PDF_WITHOUT_MENTORING:*/
						$this->{$cmd}();
						break;
					default:
						$this->{$cmd}();
						break;
				}
		}
	}


	/**
	 *
	 */
	protected function config() {
		$this->configResultTable();
	}


	/**
	 *
	 */
	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->saveParameterByClass(ilRepositoryGUI::class, 'ref_id');

		$this->ctrl->setParameterByClass(ilRepositoryGUI::class, 'ref_id', $this->groupRefId);
		$this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(array(
			ilRepositoryGUI::class//,
			//ilObjGroupGUI::class
		)));

		$this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, 'ref_id');
		$this->tabs->addTab(ilParticipationCertificateResultGUI::CMD_OVERVIEW, $this->pl->txt('header_overview'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_CONTENT));

		//$this->tabs->addTab(ilParticipationCertificateResultGUI::CMD_OVERVIEW,$this->pl->txt('header_overview'),$this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class,ilParticipationCertificateResultGUI::CMD_CONTENT));
		$this->tabs->addTab(self::TAB_CONFIG, $this->pl->txt('header_config'), $this->ctrl->getLinkTargetByClass(self::class, self::CMD_CONFIG));
		$this->tabs->activateTab(self::TAB_CONFIG);
	}


	/**
	 *
	 */
	protected function initConfTabs() {
		$this->tabs->addSubTab(self::TAB_CONFIG_RESULT_TABLE, $this->pl->txt('config_result_table'), $this->ctrl->getLinkTarget($this, self::CMD_CONFIG_RESULT_TABLE));
		$this->tabs->addSubTab(self::TAB_CONFIG_SELF_PRINT, $this->pl->txt('period_self_print'), $this->ctrl->getLinkTarget($this, self::CMD_SELF_PRINT));
		$this->tabs->addSubTab(self::TAB_CONFIG_DISPLAY, $this->pl->txt('plugin'), $this->ctrl->getLinkTarget($this, self::CMD_DISPLAY));
	}


	/**
	 *
	 */
	protected function display() {
		      if(method_exists($this->tpl,'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
$this->tpl->getStandardTemplate();
}
		$this->initHeader();

		$this->initConfTabs();
		$this->tabs->activateSubTab(self::TAB_CONFIG_DISPLAY);

		$form = $this->initform();

		$this->tpl->setContent($form->getHTML());
		if(method_exists($this->tpl, 'printToStdout'))
{
$this->tpl->printToStdout();
 } else {
$this->tpl->show();
 }
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	public function initForm() {
		$form = new ilPropertyFormGUI();

		$this->toolbar->setFormAction($this->ctrl->getFormAction($this,  self::CMD_CONFIG));

		$dropdown = new ilSelectInputGUI($this->pl->txt("choose_template"),"global_template_id");
		$cert_global_configs = new ilParticipationCertificateGlobalConfigSets();
		$dropdown->setOptions($cert_global_configs->getSelectOptions());
		$this->toolbar->addInputItem($dropdown);

		$button = ilSubmitButton::getInstance();
		$button->setCommand(self::CMD_SET_CERT_TEMPLATE);
		$button->setCaption($this->pl->txt('btn_reset'), false);
		$this->toolbar->addButtonInstance($button);

		$button = ilSubmitButton::getInstance();
		$button->setCommand(self::CMD_SET_OWN_CERT_TEXT_FROM_TEMPLATE);
		$button->setCaption($this->pl->txt('btn_modify'), false);
		$this->toolbar->addButtonInstance($button);

		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle($this->pl->txt('config_plugin'));
		$form->setDescription($this->pl->txt("placeholders") . ' <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		&lbrace;&lbrace;date&rbrace;&rbrace;: Datum
		');

		$cert_configs = new ilParticipationCertificateConfigs();
		$arr_config = $cert_configs->getObjConfigSetIfNoneCreateDefaultAndCreateNewObjConfigValues($this->groupRefId);

		$global_config_sets = new ilParticipationCertificateGlobalConfigSets();
		if(count($arr_config) > 0) {
            $global_config_id = reset($arr_config)->getGlobalConfigId();
        }

		if($global_config_id > 0) {
			$global_config_set = $global_config_sets->getConfigSetById($global_config_id);
			ilUtil::sendInfo($this->pl->txt('configset_type_1').' '.$global_config_set->getTitle());
		} else {
			ilUtil::sendInfo($this->pl->txt('configset_type_2'));
		}


		foreach ($arr_config as $config) {

			$disbaled = false;
			if($config->getConfigType() == ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE) {
				$disbaled = true;
			}

			/**
			 * @var ilParticipationCertificateConfig $config
			 */
			switch ($config->getConfigKey()) {
				case "logo":
					if($disbaled) {
						$input = new ilFileInputGUI($this->pl->txt("logo"), 'logo');
						if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $global_config_id, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
							$input->setInfo('<img src="'
								. ilParticipationCertificateConfig::returnPicturePath('relative', $global_config_id, ilParticipationCertificateConfig::LOGO_FILE_NAME) . '" />');
						}
					} else {
						$input = new ilFileInputGUI($this->pl->txt("logo"), 'logo');
						$input->setSuffixes(array( 'png' ));
						if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $this->groupRefId, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
							$input->setInfo('<img src="'
								. ilParticipationCertificateConfig::returnPicturePath('relative', $this->groupRefId, ilParticipationCertificateConfig::LOGO_FILE_NAME) . '" />');
						}

					}
					break;
				case "page1_issuer_signature":
                    if($disbaled) {
                        $input = new ilFileInputGUI("page1_issuer_signature", 'page1_issuer_signature');
                        if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $global_config_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
                            $input->setInfo('<img src="'
                                . ilParticipationCertificateConfig::returnPicturePath('relative', $global_config_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME) . '" />');
                        }
                    } else {
                        $input = new ilFileInputGUI("page1_issuer_signature", 'page1_issuer_signature');
                        $input->setSuffixes(array('png'));
                        if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $this->groupRefId, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
                            $input->setInfo('<img src="'
                                . ilParticipationCertificateConfig::returnPicturePath('relative', $this->groupRefId, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME) . '" />');
                        }
                    }
					break;

				default:
					$input = new ilTextAreaInputGUI($config->getConfigKey(), $config->getConfigKey());
					$input->setRows(3);
					$input->setValue($config->getConfigValue());
					break;
			}

			if($disbaled === true) {
				$input->setDisabled($disbaled);
			}

			$form->addItem($input);
		}
		if ($this->objecttype === 'grp') {
			$this->ctrl->saveParameterByClass(ilObjGroup::class, 'ref_id');
			} else {
			$this->ctrl->saveParameterByClass(ilObjCourse::class, 'ref_id');
		}
		$form->addCommandButton(self::CMD_SAVE, $this->pl->txt('save'));

		return $form;
	}


	/**
	 * @return bool
	 */
	public function save() {

		$form = $this->initForm();

		if (!$form->checkInput()) {
			$this->tpl->setContent($form->getHTML());
			      if(method_exists($this->tpl,'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
$this->tpl->getStandardTemplate();
}
			if(method_exists($this->tpl, 'printToStdout'))
{
$this->tpl->printToStdout();
 } else {
$this->tpl->show();
 }

			return false;
		}

		//save Text
		foreach ($form->getItems() as $item) {
			/**
			 * @var ilFormPropertyGUI                $item
			 * @var ilParticipationCertificateConfig $config
			 */

				$config = ilParticipationCertificateConfig::where(array(
					'config_key' => $item->getPostVar(),
					"group_ref_id" => $this->groupRefId,
					'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
				))->first();
				if (!is_object($config)) {
					$config = new ilParticipationCertificateConfig();
					$config->setGroupRefId($this->groupRefId);
					$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP);
					$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
					$config->setConfigKey($item->getPostVar());
					$config->setConfigValue("");
				}

				$input = $form->getInput($item->getPostVar());

				switch ($config->getConfigKey()) {
					case "page1_issuer_signature":
                        //Picture
                        $file_data = $form->getInput('page1_issuer_signature');
                        if ($file_data['tmp_name']) {
                            $input = ilParticipationCertificateConfig::storePicture($file_data, $this->groupRefId, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME);
                        } else {
                            // Previous upload
                            $input = $config->getConfigValue();
                        }
						break;
					case 'logo':
						//Picture
						$file_data = $form->getInput('logo');
						if ($file_data['tmp_name']) {
							$input = ilParticipationCertificateConfig::storePicture($file_data, $this->groupRefId, ilParticipationCertificateConfig::LOGO_FILE_NAME);
						} else {
							// Previous upload
							$input = $config->getConfigValue();
						}
						break;
					default:
						break;
				}

				$config->setConfigValue($input);
				$config->store();
			}


		ilUtil::sendSuccess($this->pl->txt('successFormSave'), true);

		$this->ctrl->redirect($this, self::CMD_DISPLAY);

		return true;
	}


	/*
	 *
	 * /
	public function printPdf() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId);
		$twigParser->parseData();
	}
	/*	/ **
	 *
	 * /
	public function printPdfWithoutMentoring() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId,array(),false);
		$twigParser->parseData();
	}
	*/
	/**
	 *
	 */
	public function setCertTemplate() {

		$cert_configs = new ilParticipationCertificateConfigs();
		if($global_template_id = filter_input(INPUT_POST,'global_template_id')) {
			$cert_configs->setObjToUseCertTemplate($this->groupRefId, $global_template_id);
			ilUtil::sendSuccess($this->pl->txt('successForm'), true);
		}

		$this->ctrl->redirect($this, self::CMD_DISPLAY);
	}

	public function setOwnCertTextFromTemplate() {
		$cert_configs = new ilParticipationCertificateConfigs();
		if($global_template_id = filter_input(INPUT_POST,'global_template_id')) {
			$cert_configs->setOwnCertConfigFromTemplate($this->groupRefId, $global_template_id);
			ilUtil::sendSuccess($this->pl->txt('successForm'), true);
		}

		$this->ctrl->redirect($this, self::CMD_DISPLAY);
	}


	/**
	 *
	 */
	public function configResultTable()
    {
        if (method_exists($this->tpl, 'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
$this->tpl->getStandardTemplate();
}
        $this->initHeader();

        $this->initConfTabs();
        $this->tabs->activateSubTab(self::TAB_CONFIG_RESULT_TABLE);

        $form = $this->initConfigResultTableForm();

        $this->tpl->setContent($form->getHTML());

        if (method_exists($this->tpl, 'printToStdout')) {
            $this->tpl->printToStdout();
        } else {
            $this->tpl->show();
        }
    }


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function initConfigResultTableForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->pl->txt('period'));

		$period = new ilDateDurationInputGUI($this->pl->txt('period'), 'period');
		$period->setStart(new ilDateTime(ilParticipationCertificateConfig::getConfig('period_start', $this->groupRefId), IL_CAL_DATE));
		$period->setEnd(new ilDateTime(ilParticipationCertificateConfig::getConfig('period_end', $this->groupRefId), IL_CAL_DATE));
		$form->addItem($period);

		$calculation_type_processing_state_suggested_objectives = new ilRadioGroupInputGUI(
            $this->pl->txt('calculation_type_processing_state_suggested_objectives'),
            'calculation_type_processing_state_suggested_objectives'
        );
            $option = new ilRadioOption(
                $this->pl->txt('calculation_by_points'),
                ilLearnObjectSuggResult::CALC_TYPE_BY_POINTS
            );
            $calculation_type_processing_state_suggested_objectives->addOption($option);

            $option = new ilRadioOption(
                $this->pl->txt('calculation_by_completed_learning_objective'),
                ilLearnObjectSuggResult::CALC_TYPE_BY_COMPLETED_OBJECTIVE
            );
            $calculation_type_processing_state_suggested_objectives->addOption($option);

            $option = new ilRadioOption(
                $this->pl->txt('calculation_by_highest_value'),
                ilLearnObjectSuggResult::CALC_TYPE_HIGHEST_VALUE
            );
            $calculation_type_processing_state_suggested_objectives->addOption($option);

            $value = ilParticipationCertificateConfig::getConfig
            (
                'calculation_type_processing_state_suggested_objectives',
                $this->groupRefId
            )
                ?
                ilParticipationCertificateConfig::getConfig(
                    'calculation_type_processing_state_suggested_objectives',
                    $this->groupRefId)
                :
                ilLearnObjectSuggResult::CALC_TYPE_BY_POINTS;

            $calculation_type_processing_state_suggested_objectives->setValue($value);

            $form->addItem($calculation_type_processing_state_suggested_objectives);

	    $ementoring = new ilCheckboxInputGUI($this->pl->txt('enable_ementoring'), 'enable_ementoring');
	    $ementoring_setting = ilParticipationCertificateConfig::getConfig('enable_ementoring', $this->groupRefId);
	    if ($ementoring_setting === NULL) {
		    $ementoring_setting = true;
	    	    } else {
		    $ementopting_setting = boolval($ementoring_setting);
	    	    }
	    	$ementoring->setChecked($ementoring_setting);
		$form->addItem($ementoring);

	    $form->addCommandButton(self::CMD_RESULT_TABLE_CONFIG, $this->pl->txt('save'));


		return $form;
	}


	/**
	 *
	 */
	protected function saveResultTableConfig() {
		$form = $this->initConfigResultTableForm();

		if (!$form->checkInput()) {
			//TODO error message plus redirect
			return;
		}

		$period = $form->getInput('period');
		$ementoring = $form->getInput('enable_ementoring');
		ilParticipationCertificateConfig::setConfig('period_start', $period['start'], $this->groupRefId);
		ilParticipationCertificateConfig::setConfig('period_end', $period['end'], $this->groupRefId);
		ilParticipationCertificateConfig::setConfig('enable_ementoring', $ementoring, $this->groupRefId);

        $calculation_type_processing_state_suggested_objectives = $form->getInput('calculation_type_processing_state_suggested_objectives');

        ilParticipationCertificateConfig::setConfig('calculation_type_processing_state_suggested_objectives', $calculation_type_processing_state_suggested_objectives, $this->groupRefId);


		ilUtil::sendSuccess($this->pl->txt('successFormSave'), true);

		$this->ctrl->redirect($this, self::CMD_CONFIG_RESULT_TABLE);
	}


	/**
	 *
	 */
	protected function selfPrint() {
		      if(method_exists($this->tpl,'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
$this->tpl->getStandardTemplate();
}
		$this->initHeader();

		$this->initConfTabs();
		$this->tabs->activateSubTab(self::TAB_CONFIG_SELF_PRINT);

		$form = $this->initSelfPrintForm();

		$this->tpl->setContent($form->getHTML());
		if(method_exists($this->tpl, 'printToStdout'))
{
$this->tpl->printToStdout();
 } else {
$this->tpl->show();
 }
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function initSelfPrintForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->pl->txt('period_self_print'));

		$enable = new ilCheckboxInputGUI($this->pl->txt('enable_self_print'), 'enable_self_print');
		$enable->setChecked(boolval(ilParticipationCertificateConfig::getConfig('enable_self_print', $this->groupRefId)));
		$form->addItem($enable);

		$period = new ilDateDurationInputGUI($this->pl->txt('period'), 'period_self_print');
		$period->setStart(new ilDateTime(ilParticipationCertificateConfig::getConfig('self_print_start', $this->groupRefId), IL_CAL_DATE));
		$period->setEnd(new ilDateTime(ilParticipationCertificateConfig::getConfig('self_print_end', $this->groupRefId), IL_CAL_DATE));
		$enable->addSubItem($period);

		$form->addCommandButton(self::CMD_SELF_PRINT_SAVE, $this->pl->txt('save'));

		return $form;
	}


	/**
	 *
	 */
	protected function saveSelfPrint() {
		$form = $this->initSelfPrintForm();

		if (!$form->checkInput()) {
			//TODO error message plus redirect
			return;
		}

		$enable = boolval($form->getInput("enable_self_print"));
		ilParticipationCertificateConfig::setConfig('enable_self_print', $enable, $this->groupRefId);

		$period = $form->getInput('period_self_print');
		ilParticipationCertificateConfig::setConfig('self_print_start', $period['start'], $this->groupRefId);
		ilParticipationCertificateConfig::setConfig('self_print_end', $period['end'], $this->groupRefId);

		ilUtil::sendSuccess($this->pl->txt('successFormSave'), true);

		$this->ctrl->redirect($this, self::CMD_SELF_PRINT);
	}
}
