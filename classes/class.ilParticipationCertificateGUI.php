<?php

use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;

/**
 * Class ilParticipationCertificateGUI
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls      ilParticipationCertificateGUI: ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateGUI
{

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
    public ilTemplate|ilGlobalTemplateInterface $tpl;
    public ilCtrl|ilCtrlInterface $ctrl;
    public ilTabsGUI $tabs;
    public ilGroupParticipants $learnGroupParticipants;
    public ilObjGroup $learningGroup;
    public ilParticipationCertificateConfig $object;
    public ilToolbarGUI $toolbar;
    public int $groupRefId;
    protected ilParticipationCertificatePlugin $pl;
    protected ilLanguage $lng;


    /**
     *
     */
    function __construct()
    {
        global $DIC;

        $this->toolbar = $DIC->toolbar();
        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->tabs = $DIC->tabs();
        //$this->objectDefinition = $DIC["objDefinition"];
        $this->groupRefId = (int)$_GET['ref_id'];
        $this->lng = $DIC->language();

        //Access
        $cert_access = new ilParticipationCertificateAccess($this->groupRefId);
        if (!$cert_access->hasCurrentUserAdminAccess()) {
            $this->tpl->setOnScreenMessage('failure',$this->lng->txt('no_permission'), true);
            ilUtil::redirect('login.php');
        }
        $this->objecttype = ilObject::_lookupType($this->groupRefId, true);
        $this->pl = ilParticipationCertificatePlugin::getInstance();
        $this->learnGroup = ilObjectFactory::getInstanceByRefId($this->groupRefId);
        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, ['ref_id', 'group_id']);
    }

    function executeCommand(): void
    {
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

    protected function config(): void
    {
        $this->configResultTable();
    }

    function initHeader(): void
    {
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
        $this->tabs->addTab(
            ilParticipationCertificateResultGUI::CMD_OVERVIEW,
            $this->pl->txt('header_overview'),
            $this->ctrl->getLinkTargetByClass(
                [ilUIPluginRouterGUI::class, ilParticipationCertificateResultGUI::class],
                ilParticipationCertificateResultGUI::CMD_CONTENT
            )
        );

        //$this->tabs->addTab(ilParticipationCertificateResultGUI::CMD_OVERVIEW,$this->pl->txt('header_overview'),$this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class,ilParticipationCertificateResultGUI::CMD_CONTENT));
        $this->tabs->addTab(self::TAB_CONFIG, $this->pl->txt('header_config'), $this->ctrl->getLinkTargetByClass(self::class, self::CMD_CONFIG));
        $this->tabs->activateTab(self::TAB_CONFIG);
    }

    protected function initConfTabs(): void
    {
        $this->tabs->addSubTab(self::TAB_CONFIG_RESULT_TABLE, $this->pl->txt('config_result_table'), $this->ctrl->getLinkTarget($this, self::CMD_CONFIG_RESULT_TABLE));
        $this->tabs->addSubTab(self::TAB_CONFIG_SELF_PRINT, $this->pl->txt('period_self_print'), $this->ctrl->getLinkTarget($this, self::CMD_SELF_PRINT));
        $this->tabs->addSubTab(self::TAB_CONFIG_DISPLAY, $this->pl->txt('plugin'), $this->ctrl->getLinkTarget($this, self::CMD_DISPLAY));
    }

    /**
     * @throws arException
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    protected function display(): void
    {
        global $DIC;

        $renderer = $DIC->ui()->renderer();

        if (method_exists($this->tpl, 'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
            $this->tpl->getStandardTemplate();
        }
        $this->initHeader();

        $this->initConfTabs();
        $this->tabs->activateSubTab(self::TAB_CONFIG_DISPLAY);

        $form = $this->initForm();

        $this->tpl->setContent($renderer->render($form));
        if (method_exists($this->tpl, 'printToStdout')) {
            $this->tpl->printToStdout();
        } else {
            $this->tpl->show();
        }
    }

    /**
     * @throws arException
     * @throws ilCtrlException
     */
    public function initForm(): Standard
    {
        global $DIC;
        $ui = $DIC->ui()->factory();


        $this->toolbar->setFormAction(
            $this->ctrl->getFormAction($this, self::CMD_CONFIG)
        );

        $inputFields = [];

        $cert_global_configs = new ilParticipationCertificateGlobalConfigSets();
        $options_template = $cert_global_configs->getSelectOptions();
        $select = $ui->input()->field()->select('', $options_template);

        $this->toolbar->addComponent($select);

        //$this->ctrl->setParameterByClass(ilRepositoryGUI::class, 'global_template_id', $this->groupRefId);

        $button_fixed_form = $ui->button()->standard(
            $this->pl->txt('btn_reset'),
            $DIC->ctrl()->getLinkTarget($this, self::CMD_SET_CERT_TEMPLATE)
        );
        $button_editable_form = $ui->button()->standard(
            $this->pl->txt('btn_modify'),
            $DIC->ctrl()->getLinkTarget($this, self::CMD_SET_OWN_CERT_TEXT_FROM_TEMPLATE)
        );


        $this->toolbar->addComponent($button_fixed_form);
        $this->toolbar->addComponent($button_editable_form);

        $cert_configs = new ilParticipationCertificateConfigs();
        $arr_config = $cert_configs->getObjConfigSetIfNoneCreateDefaultAndCreateNewObjConfigValues($this->groupRefId);

        $global_config_sets = new ilParticipationCertificateGlobalConfigSets();
        if (count($arr_config) > 0) {
            $global_config_id = reset($arr_config)->getGlobalConfigId();
        }

        if ($global_config_id > 0) {
            $global_config_set = $global_config_sets->getConfigSetById($global_config_id);
            $this->tpl->setOnScreenMessage('info',$this->pl->txt('configset_type_1'). ' ' . $global_config_set->getTitle(), true);
        } else {
            $this->tpl->setOnScreenMessage('info',$this->pl->txt('configset_type_2'), true);
        }

        foreach ($arr_config as $config) {
            $disabled = false;
            if ($config->getConfigType() == ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE) {
                $disabled = true;
            }

            switch ($config->getConfigKey()) {
                case 'logo':
                    if ($disabled) {
                        $file = new ilParticipationCertificateFiles();
                        $src = $file->getFileSrcByStorageType(
                            $config->getConfigValue(),
                            $global_config_id,
                            'logo'
                        );

                        $inputFields[$config->getConfigKey()] = $ui->input()->field()->file(
                            new ilParticipationCertificateFileUploadHandlerGUI(),
                            $this->pl->txt('logo'),
                            'Maximum upload size: 1024.0 MB. Allowed file types: .png' . "<br>\n" .
                            '<img src="' . $src . '">'
                        )->withAcceptedMimeTypes([
                            'image/jpeg',
                            'image/png'
                        ])->withMaxFileSize((2 * 1024 * 1024));


                        break;

                    } else {
                        $file = new ilParticipationCertificateFiles();
                        $src = $file->getFileSrcByStorageType(
                            $config->getConfigValue(),
                            $global_config_id,
                            'logo'
                        );

                        $inputFields[$config->getConfigKey()] = $ui->input()->field()->file(
                            new ilParticipationCertificateFileUploadHandlerGUI(),
                            $this->pl->txt('logo'),
                            'Maximum upload size: 1024.0 MB. Allowed file types: .png' . "<br>\n" .
                            '<img src="' . $src . '">'
                        )->withAcceptedMimeTypes([
                            'image/png'
                        ])->withMaxFileSize((2 * 1024 * 1024));

                    }
                    break;
                case 'page1_issuer_signature':
                    if ($disabled) {
                        $file = new ilParticipationCertificateFiles();
                        $src = $file->getFileSrcByStorageType(
                            $config->getConfigValue(),
                            $global_config_id,
                            'page1_issuer_signature'
                        );

                    } else {
                        $file = new ilParticipationCertificateFiles();
                        $src = $file->getFileSrcByStorageType(
                            $config->getConfigValue(),
                            $global_config_id,
                            'page1_issuer_signature'
                        );
                    }

                    $inputFields[$config->getConfigKey()] = $ui->input()->field()->file(
                        new ilParticipationCertificateFileUploadHandlerGUI(),
                        $this->pl->txt('logo'),
                        'Maximum upload size: 1024.0 MB. Allowed file types: .png' . "<br>\n" .
                        '<img src="' . $src . '">'
                    )->withAcceptedMimeTypes([
                        'image/png'
                    ])->withMaxFileSize((2 * 1024 * 1024));
                    break;

                default:
                    if($disabled){
                        $inputFields[$config->getConfigKey()] = $ui->input()->field()->textarea(
                            $config->getConfigKey()
                        )->withValue($config->getConfigValue() ?? '')
                         ->withDisabled(true);
                    } else {
                        $inputFields[$config->getConfigKey()] = $ui->input()->field()->textarea(
                            $config->getConfigKey()
                        )->withValue($config->getConfigValue() ?? '');
                    }
                    break;
            }
        }

        $section = $ui->input()->field()->section(
            $inputFields,
            $this->pl->txt('config_plugin'),
            $this->pl->txt("placeholders") . ' <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		&lbrace;&lbrace;date&rbrace;&rbrace;: Datum
		'
        );

        $formAction = $DIC->ctrl()->getFormActionByClass(
            self::class,
            self::CMD_SAVE
        );

        $form = $ui->input()->container()->form()->standard(
            $formAction,
            ['config' => $section]
        );

        if ($this->objecttype === 'grp') {
            $this->ctrl->saveParameterByClass(ilObjGroup::class, 'ref_id');
        } else {
            $this->ctrl->saveParameterByClass(ilObjCourse::class, 'ref_id');
        }

        return $form;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        global $DIC;

        $form = $this->initForm();

        $form  = $form->withRequest($DIC->http()->request());
        $form_data = $form->getData()['config'];

        foreach ($form_data as $key => $item) {

            $config = ilParticipationCertificateConfig::where(array(
                'config_key' => $key,
                "group_ref_id" => $this->groupRefId,
                'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
            ))->first();


            if (!is_object($config)) {
                $config = new ilParticipationCertificateConfig();
                $config->setGroupRefId($this->groupRefId);
                $config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP);
                $config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
                $config->setConfigKey($key);
                $config->setConfigValue("");
            }

            $input = $item;

            switch ($key) {
                case 'page1_issuer_signature':
                    //Picture
                    $file_data = $input;
                    if (key_exists('tmp_name', $file_data) && $file_data['tmp_name']) {
                        $input = ilParticipationCertificateConfig::storePicture($file_data, $this->groupRefId, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME);
                    } else {
                        // Previous upload
                        $input = $config->getConfigValue();
                    }
                    break;
                case 'logo':

                    $input = end($input);
                    break;
                default:
                    break;
            }

            $config->setConfigValue($input);

            $config->store();
        }

        $this->tpl->setOnScreenMessage('success',$this->pl->txt('successFormSave'), true);
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

    public function setCertTemplate(): void
    {
        $cert_configs = new ilParticipationCertificateConfigs();
        if ($global_template_id = filter_input(INPUT_POST, 'global_template_id')) {
            $cert_configs->setObjToUseCertTemplate($this->groupRefId, $global_template_id);
            $this->tpl->setOnScreenMessage('success',$this->pl->txt('successForm'), true);
        }

        $this->ctrl->redirect($this, self::CMD_DISPLAY);
    }

    public function setOwnCertTextFromTemplate(): void
    {
        // TODO here should be passed the global_template_id.
        // TODO the filter_input(INPUT_POST, 'global_template_id') doesnt wor in KS

        $globalTemplateId = $_GET['global_template_id'];
        dd($globalTemplateId);

        $cert_configs = new ilParticipationCertificateConfigs();
        if ($global_template_id = filter_input(INPUT_POST, 'global_template_id')) {
            $cert_configs->setOwnCertConfigFromTemplate($this->groupRefId, $global_template_id);
            $this->tpl->setOnScreenMessage('success',$this->pl->txt('successForm'), true);
        }

        $this->ctrl->redirect($this, self::CMD_DISPLAY);
    }


    /**
     *
     */
    public function configResultTable(): void
    {
        global $DIC;

        if (method_exists($this->tpl, 'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
            $this->tpl->getStandardTemplate();
        }
        $this->initHeader();

        $this->initConfTabs();
        $this->tabs->activateSubTab(self::TAB_CONFIG_RESULT_TABLE);

        $form = $this->initConfigResultTableForm();




        /*$this->tpl->setContent($form->getHTML());*/
        $renderer = $DIC->ui()->renderer();
        $this->tpl->setContent($renderer->render($form));

        if (method_exists($this->tpl, 'printToStdout')) {
            $this->tpl->printToStdout();
        } else {
            $this->tpl->show();
        }
    }

    /**
     * @return Standard
     * @throws ilCtrlException
     */
    protected function initConfigResultTableForm()
    {
        global $DIC;

        $ui = $DIC->ui()->factory();

        $dataFactory = new \ILIAS\Data\Factory();

        $periodStart = ilParticipationCertificateConfig::getConfig('period_start', $this->groupRefId);
        $startDate = !empty($periodStart) ? DateTimeImmutable::createFromFormat('d.m.Y', $periodStart) : null;

        $periodEnd = ilParticipationCertificateConfig::getConfig('period_end', $this->groupRefId);
        $endDate = !empty($periodEnd) ? DateTimeImmutable::createFromFormat('d.m.Y', $periodEnd) : null;

        $durationInput = $ui->input()->field()->duration($this->pl->txt('period'));
        if (!empty($startDate) && !empty($endDate)) {
            $period = $durationInput
                ->withTimezone('Europe/Berlin')
                ->withUseTime(false)
                ->withLabels($this->pl->txt('start'), $this->pl->txt('end'))
                ->withFormat($dataFactory->dateFormat()->germanShort())
                ->withMinValue($startDate)
                ->withMaxValue($endDate);
        }else {
            $period = $durationInput
                ->withTimezone('Europe/Berlin')
                ->withUseTime(false)
                ->withLabels($this->pl->txt('start'), $this->pl->txt('end'))
                ->withFormat($dataFactory->dateFormat()->germanShort());
        }

        $inputFields['period'] = $period;

        $calculationType = ilParticipationCertificateConfig::getConfig(
            'calculation_type_processing_state_suggested_objectives',
            $this->groupRefId
        ) ? ilParticipationCertificateConfig::getConfig(
                'calculation_type_processing_state_suggested_objectives',
                $this->groupRefId
        ) : ilLearnObjectSuggResult::CALC_TYPE_BY_POINTS;

        $radio = $ui->input()->field()->radio( $this->pl->txt('calculation_type_processing_state_suggested_objectives'))
                    ->withOption( ilLearnObjectSuggResult::CALC_TYPE_BY_POINTS, $this->pl->txt('calculation_by_points'))
                    ->withOption(   ilLearnObjectSuggResult::CALC_TYPE_BY_COMPLETED_OBJECTIVE, $this->pl->txt('calculation_by_completed_learning_objective'))
                    ->withOption( ilLearnObjectSuggResult::CALC_TYPE_HIGHEST_VALUE, $this->pl->txt('calculation_by_highest_value'))
                    ->withValue($calculationType);

        $inputFields['calculation_type'] = $radio;


        $ementoringSetting = ilParticipationCertificateConfig::getConfig('enable_ementoring', $this->groupRefId);
        if ($ementoringSetting === NULL) {
            $ementoringSetting = true;
        } else {
            $ementoringSetting = boolval($ementoringSetting);
        }

        $checkbox = $ui->input()->field()->checkbox($this->pl->txt('enable_ementoring'))
                             ->withValue($ementoringSetting);

        $inputFields['ementoring'] = $checkbox;

        $section = $ui->input()->field()->section(
            $inputFields,
            'Resultate für'
        );
        $formAction = $this->ctrl->getFormActionByClass(
            self::class,
            self::CMD_RESULT_TABLE_CONFIG,
            $this->pl->txt('save')
        );

        //Step 2: Define the form and attach the section.
        $form = $ui->input()->container()->form()->standard(
            $formAction,
            ['config' => $section]
        );
        return $form;
    }

    /**
     * @throws ilCtrlException
     */
    protected function saveResultTableConfig(): void
    {
        global $DIC;

        $form  = $this->initConfigResultTableForm();

        $form  = $form->withRequest($DIC->http()->request());
        $form_data = $form->getData()['config'];

        $period = $form_data['period'];
        $ementoring = $form_data['ementoring'];

        $periodStart = !empty($period['start']) ? $period['start']->format('d.m.Y') : null;
        $periodEnd = !empty($period['end']) ? $period['end']->format('d.m.Y') : null;

        ilParticipationCertificateConfig::setConfig('period_start', $periodStart, $this->groupRefId);
        ilParticipationCertificateConfig::setConfig('period_end', $periodEnd, $this->groupRefId);
        ilParticipationCertificateConfig::setConfig('enable_ementoring', $ementoring, $this->groupRefId);

        ilParticipationCertificateConfig::setConfig(
            'calculation_type_processing_state_suggested_objectives',
            $form_data['calculation_type'],
            $this->groupRefId
        );

        $this->tpl->setOnScreenMessage('success',$this->pl->txt('successFormSave'), true);
        $this->ctrl->redirect($this, self::CMD_CONFIG_RESULT_TABLE);
    }

    protected function selfPrint(): void
    {
        global $DIC;

        $renderer = $DIC->ui()->renderer();

        if (method_exists($this->tpl, 'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
            $this->tpl->getStandardTemplate();
        }
        $this->initHeader();

        $this->initConfTabs();
        $this->tabs->activateSubTab(self::TAB_CONFIG_SELF_PRINT);

        $form = $this->initSelfPrintForm();

      /*  $this->tpl->setContent($form->getHTML());*/

        $this->tpl->setContent($renderer->render($form));
        if (method_exists($this->tpl, 'printToStdout')) {
            $this->tpl->printToStdout();
        } else {
            $this->tpl->show();
        }
    }

    protected function initSelfPrintForm()
    {
        global $DIC;

        $ui = $DIC->ui()->factory();


        $dataFactory = new \ILIAS\Data\Factory();

        $periodStart = ilParticipationCertificateConfig::getConfig('self_print_start', $this->groupRefId);
        $startDate = !empty($periodStart) ? DateTimeImmutable::createFromFormat('d.m.Y', $periodStart) : null;

        $periodEnd = ilParticipationCertificateConfig::getConfig('self_print_end', $this->groupRefId);
        $endDate = !empty($periodEnd) ? DateTimeImmutable::createFromFormat('d.m.Y', $periodEnd) : null;


        $durationInput = $ui->input()->field()->duration($this->pl->txt('period'));
        $period = $durationInput
            ->withTimezone('Europe/Berlin')
            ->withUseTime(false)
            ->withLabels($this->pl->txt('start'), $this->pl->txt('end'))
            ->withFormat($dataFactory->dateFormat()->germanShort())
            ->withMinValue($startDate)
            ->withMaxValue($endDate);

        $inputFields['enable-self-printing'] = $ui->input()->field()->optionalGroup(
            [
                'period' => $period
            ],
            $this->pl->txt('period')
        );

        $section = $ui->input()->field()->section(
            $inputFields,
            $this->pl->txt('period_self_print')
        );
        $formAction = $this->ctrl->getFormActionByClass(
            self::class,
            self::CMD_SELF_PRINT_SAVE,
            $this->pl->txt('save')
        );

        //Step 2: Define the form and attach the section.
        $form = $ui->input()->container()->form()->standard(
            $formAction,
            ['config' => $section]
        );
        return $form;

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

    /*protected function initSelfPrintForm(): ilPropertyFormGUI
    {
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
    }*/

    protected function saveSelfPrint(): void
    {
        global $DIC;

        $form = $this->initSelfPrintForm();


        $form  = $form->withRequest($DIC->http()->request());
        $formData = $form->getData();

        // TODO implement this, in other files as well
        if ($form->getError()) {
            $this->tpl->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE, $form->getError());
            /*$this->configure();*/
            return;
        }

        dd($formData);

        if (!$form->checkInput()) {
            //TODO error message plus redirect
            return;
        }

        $enable = boolval($form->getInput("enable_self_print"));
        ilParticipationCertificateConfig::setConfig('enable_self_print', $enable, $this->groupRefId);

        $period = $form->getInput('period_self_print');
        ilParticipationCertificateConfig::setConfig('self_print_start', $period['start'], $this->groupRefId);
        ilParticipationCertificateConfig::setConfig('self_print_end', $period['end'], $this->groupRefId);

        $this->tpl->setOnScreenMessage('success',$this->pl->txt('successFormSave'), true);
        $this->ctrl->redirect($this, self::CMD_SELF_PRINT);
    }
}
