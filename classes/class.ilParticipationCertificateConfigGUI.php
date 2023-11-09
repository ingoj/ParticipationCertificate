<?php

require_once __DIR__ . "/../vendor/autoload.php";

//TODO Refactoring - find a better way to save and display the form

/**
 * Class ilParticipationCertificateConfigGUI
 * @author       Silas Stulz <sst@studer-raimann.ch>
 * @ilCtrl_IsCalledBy  ilParticipationCertificateConfigGUI: ilObjComponentSettingsGUI
 * @ilCtrl_Calls ilParticipationCertificateConfigGUI: ilParticipationCertificatePDFGenerator
 */
class ilParticipationCertificateConfigGUI extends ilPluginConfigGUI
{

    const CMD_CONFIRM_RESET_CONFIG = 'confirm_reset_config';
    const CMD_RESET_CONFIG = 'resetConfig';
    const CMD_SHOW_FORM = 'showForm';
    const CMD_SHOW_FORM_ERR = 'showErrForm';
    const CMD_ADD_CONFIG = 'addConfig';
    const CMD_COPY_CONFIG = 'copyConfig';
    const CMD_CREATE_TEMPLATE_FRON_LOCAL_CONFIG = 'createTemplateFromLocalConfig';
    const CMD_DELETE_CONFIG = 'deleteConfig';
    const CMD_SET_ACTIVE = 'setActive';
    const CMD_SET_INACTIVE = 'setInactive';
    const CMD_CONFIGURE = 'configure';
    const CMD_SAVE = 'save';
    const CMD_SAVE_ORDER = 'saveOrder';
    const CMD_CANCEL = 'cancel';
    protected ilParticipationCertificateConfig $object;
    protected ilParticipationCertificatePlugin $pl;
    protected ilTemplate|ilGlobalTemplateInterface $tpl;
    protected ilCtrl|ilCtrlInterface $ctrl;
    protected ilParticipationCertificateConfigSetTableGUI $table;
    protected ilTabsGUI $tabs;
    protected ilToolbarGUI $ilToolbar;
    protected ilGroupParticipants $learnGroupParticipants;
    protected ilObjCourse $courseobject;
    protected ilDBInterface $db;
    public mixed $dropValues;
    public string $surname;
    public string $lastname;
    public string $gender;
    /**
     * ilParticipationCertificateConfigGUI constructor.
     */
    public function __construct()
    {
        global $DIC;
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->db = $DIC->database();
        $this->ctrl = $DIC->ctrl();
        $this->tabs = $DIC->tabs();
        $this->ilToolbar = $DIC->toolbar();
        $this->pl = ilParticipationCertificatePlugin::getInstance();
    }

    function performCommand(string $cmd): void
    {
        switch ($cmd) {
            default:
            case self::CMD_ADD_CONFIG:
            case self::CMD_RESET_CONFIG:
            case self::CMD_CONFIRM_RESET_CONFIG;
            case self::CMD_DELETE_CONFIG:
            case self::CMD_SET_ACTIVE:
            case self::CMD_SET_INACTIVE:
            case self::CMD_COPY_CONFIG:
            case self::CMD_SHOW_FORM:
            case self::CMD_SHOW_FORM_ERR:
            case self::CMD_CONFIGURE:
            case self::CMD_SAVE:
            case self::CMD_CANCEL:
            case self::CMD_SAVE_ORDER:
                $this->$cmd();
                break;
        }
    }

    public function addConfig(): void
    {
        $gl_configs = new ilParticipationCertificateGlobalConfigSets();
        $gl_config = $gl_configs->getDefaultConfig();
        $configs = new ilParticipationCertificateConfigs();

        $new_config_set = ilParticipationCertificateGlobalConfigSet::createNewFromConfigs($configs->getGlobalConfigSet($gl_config->getId()));

        $this->ctrl->setParameter($this, "id", $new_config_set->getId());
        $this->ctrl->setParameter($this, "set_type", ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
        $this->ctrl->redirect($this, self::CMD_SHOW_FORM);
    }

    public function createTemplateFromLocalConfig(): void
    {
        $grp_ref_id = (int) filter_input(INPUT_GET, 'grp_ref_id');

        if ($grp_ref_id == 0) {
            $this->ctrl->redirect($this, '');
        }

        $configs = new ilParticipationCertificateConfigs();

        $new_config_set = ilParticipationCertificateGlobalConfigSet::createNewFromConfigs($configs->getObjectConfigSet($grp_ref_id));

        $this->ctrl->setParameter($this, "id", $new_config_set->getId());
        $this->ctrl->setParameter($this, "set_type", ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
        $this->ctrl->redirect($this, self::CMD_SHOW_FORM);
    }

    /**
     * @throws ilCtrlException
     */
    public function copyConfig(): void
    {
        $id = (int) filter_input(INPUT_GET, 'id');

        if ($id == 0) {
            $this->ctrl->redirect($this, '');
        }

        $configs = new ilParticipationCertificateConfigs();

        $new_config_set = ilParticipationCertificateGlobalConfigSet::createNewFromConfigs($configs->getGlobalConfigSet($id));

        $this->ctrl->setParameter($this, "id", $new_config_set->getId());
        $this->ctrl->setParameter($this, "set_type", ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
        $this->ctrl->redirect($this, self::CMD_SHOW_FORM);
    }

    public function resetConfig(): void
    {
        global $DIC;

        foreach (ilParticipationCertificateConfig::get() as $config) {
            $config->delete();
        }
        foreach (ilParticipationCertificateGlobalConfigSet::get() as $configset) {
            $configset->delete();
        }

        //Global Config
        //set global plugin configurations
        $config = ilParticipationCertificateConfig::where(["config_key" => 'udf_firstname'])->first();
        if (!is_object($config)) {
            	$config = new ilParticipationCertificateConfig();
        	$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
        	$config->setConfigKey('udf_firstname');
        	$config->setGlobalConfigId(0);
        	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
        	$config->setOrderBy(1);
        	$config->store();
        	}

        $config = ilParticipationCertificateConfig::where(["config_key" => 'udf_lastname'])->first();
        if (!is_object($config)) {
            	$config = new ilParticipationCertificateConfig();
        	$config->setConfigKey('udf_lastname');
        	$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
        	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
        	$config->setGlobalConfigId(0);
        	$config->setOrderBy(2);
        	$config->store();
		}

        $config = ilParticipationCertificateConfig::where(["config_key" => 'udf_gender'])->first();
        if (!is_object($config)) {
            	$config = new ilParticipationCertificateConfig();
        	$config->setConfigKey('udf_gender');
        	$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
        	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
        	$config->setGlobalConfigId(0);
        	$config->setOrderBy(3);
        	$config->store();
		}

        $config = ilParticipationCertificateConfig::where(["config_key" => 'keyword'])->first();
        if (!is_object($config)) {
            	$config = new ilParticipationCertificateConfig();
        	$config->setConfigKey('keyword');
        	$config->setConfigValue("Lerngruppe");
        	$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
        	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
        	$config->setGlobalConfigId(0);
        	$config->setOrderBy(0);
        	$config->store();
		}

        $config = ilParticipationCertificateConfig::where(["config_key" => 'color'])->first();
        if (!is_object($config)) {
            	$config = new ilParticipationCertificateConfig();
        	$config->setConfigKey('color');
        	$config->setConfigValue("fff5ba");
        	$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
        	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
        	$config->setGlobalConfigId(0);
        	$config->setOrderBy(4);
        	$config->store();
		}

        $config = ilParticipationCertificateConfig::where(["config_key" => 'unsugg_color'])->first();
        if (!is_object($config)) {
            	$config = new ilParticipationCertificateConfig();
        	$config->setConfigKey('unsugg_color');
        	$config->setConfigValue("000a35");
        	$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
        	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
        	$config->setGlobalConfigId(0);
        	$config->setOrderBy(5);
		$config->store();
	}

        $config = ilParticipationCertificateConfig::where(["config_key" => 'true_name_helper'])->first();
        if (!is_object($config)) {
            	$config = new ilParticipationCertificateConfig();
        	$config->setConfigKey('true_name_helper');
        	$config->setConfigValue("");
        	$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
        	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
        	$config->setGlobalConfigId(0);
        	$config->setOrderBy(6);
		$config->store();
	}

        //Config Template
        require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
        $part_cert_default_config_set = new ilParticipationCertificateGlobalConfigSet();
        $part_cert_default_config_set->setOrderBy(1);
        $part_cert_default_config_set->store();

        $part_cert_configs = new ilParticipationCertificateConfigs();
        foreach ($part_cert_configs->returnCertTextDefaultValues() as $key => $value) {
            /**
             * @var $value ilParticipationCertificateConfig
             */
            $config = new ilParticipationCertificateConfig();
            $config->setGlobalConfigId($part_cert_default_config_set->getId());
            $config->setConfigKey($value->getConfigKey());
            $config->setOrderBy($value->getOrderBy());
            $config->setConfigType($value->getConfigType());
            $config->setConfigValueType($value->getConfigValueType());
            $config->setOrderBy($value->getOrderBy());

            $config->store();
        }

        ilUtil::sendSuccess($this->pl->txt('config_reseted'), true);
        $DIC->ctrl()->redirect($this, self::CMD_CONFIGURE);
    }

    /**
     * @throws ilCtrlException
     */
    public function confirm_reset_config(): void
    {
        global $DIC;

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction($DIC->ctrl()->getFormAction($this));

        $confirmation->setHeaderText($this->pl->txt("confirm_reset_config"));

        $confirmation->setConfirm($this->pl->txt("reset_config"), self::CMD_RESET_CONFIG);
        $confirmation->setCancel($DIC->language()->txt("cancel"), self::CMD_CONFIGURE);

        $DIC->ui()->mainTemplate()->setContent($confirmation->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
    public function deleteConfig(): void
    {
        $id = filter_input(INPUT_GET, 'id');

        $gl_config = new ilParticipationCertificateGlobalConfigSet($id);

        if ($gl_config->getOrderBy() === 1) {
            $this->ctrl->redirect($this, self::CMD_CONFIGURE);
        }

        $gl_config->delete();

        $configs = new ilParticipationCertificateConfigs();
        foreach ($configs->getGlobalConfigSet($id) as $config) {
            $config->delete();
        }

        $this->ctrl->redirect($this, self::CMD_CONFIGURE);
    }

    /**
     * @throws ilCtrlException
     */
    public function setActive(): void
    {
        $id = filter_input(INPUT_GET, 'id');

        $gl_config = new ilParticipationCertificateGlobalConfigSet($id);
        $gl_config->setActive(1);
        $gl_config->store();

        $this->ctrl->redirect($this, self::CMD_CONFIGURE);
    }

    /**
     * @throws ilCtrlException
     */
    public function setInactive(): void
    {
        $id = filter_input(INPUT_GET, 'id');

        $gl_config = new ilParticipationCertificateGlobalConfigSet($id);

        if ($gl_config->getOrderBy() === 1) {
            $this->ctrl->redirect($this, self::CMD_CONFIGURE);
        }
        $gl_config->setActive(0);
        $gl_config->store();

        $this->ctrl->redirect($this, self::CMD_CONFIGURE);
    }

    /**
     * @throws ilCtrlException
     * @throws Exception
     */
    public function saveOrder(): void
    {

        $configs = new ilParticipationCertificateGlobalConfigSets();
        $configs->saveAndRearangeOrderBy($_POST['order_by']);

        $this->ctrl->redirect($this, self::CMD_CONFIGURE);
    }

    public function showErrForm(): void
    {
	    self::showForm(true);
    }


    /**
     * @throws arException
     * @throws ilCtrlException
     */
    public function showForm(bool $err=false): void
    {
        $id = filter_input(INPUT_GET, 'id');
        $set_type = filter_input(INPUT_GET, 'set_type');

        $this->ctrl->setParameter($this, "id", $id);
        $this->tpl->loadStandardTemplate();

        $form = $this->initForm($id, $set_type);
	    $this->tpl->setContent($form->getHTML());
        if ($id == 0 and $set_type == 3 and $err) {
            ilUtil::sendFailure($this->pl->txt("nonnumeric_ref"));
        }
    }

    /**
     * @throws arException
     * @throws ilCtrlException
     */
    public function initForm(int $global_config_id, int $configset_type): ilPropertyFormGUI
    {
        global $DIC;

        $DIC->ctrl()->setParameter($this, "id", $global_config_id);
        $DIC->ctrl()->setParameter($this, "set_type", $configset_type);

        $form = new ilPropertyFormGUI();
        $form->setFormAction($this->ctrl->getFormAction($this));
        $form->setTitle($this->pl->txt('config_plugin'));
        $form->setDescription($this->pl->txt("placeholders") . ' <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		&lbrace;&lbrace;date&rbrace;&rbrace;: Datum
		');

        switch ($configset_type) {
            case ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE:
                /**
                 * @var ilParticipationCertificateGlobalConfigSet $global_config
                 */
                $global_config = ilParticipationCertificateGlobalConfigSet::findOrGetInstance($global_config_id);
                $input = new ilTextInputGUI($this->pl->txt("config_title"), "config_title");
                $input->setRequired(true);
                $input->setValue($global_config->getTitle());
                $form->addItem($input);
                break;
        }

        foreach (ilParticipationCertificateConfig::where(array(
            "config_type" => $configset_type,
            "global_config_id" => $global_config_id
        ))->orderBy('order_by')->get() as $config) {
            /**
             * @var ilParticipationCertificateConfig $config
             */
            switch ($config->getConfigKey()) {
                /*case "page1_issuer_signature":
                    // Skip
                    $input = NULL;
                    break;*/
                case "udf_firstname":
                case "udf_lastname":
                case "udf_gender":
                    $options = $this->getUdfDropdownValues();
                    $input = new ilSelectInputGUI($this->pl->txt($config->getConfigKey()), $config->getConfigKey());
                    $input->setOptions($options);
                    $input->setValue($config->getConfigValue());
                    break;
                case "color":
                    $input = new ilColorPickerInputGUI($this->pl->txt("color"), 'color');
                    $input->setValue($config->getConfigValue());
                    break;
                case "unsugg_color":
                    $input = new ilColorPickerInputGUI($this->pl->txt("unsugg_color"), 'unsugg_color');
                    $input->setValue($config->getConfigValue());
		    break;
                case "keyword":
                    $input = new ilTextInputGUI($this->pl->txt("keyword"), 'keyword');
                    $input->setValue($config->getConfigValue());
                    break;
                case "logo":
                    $input = new ilFileInputGUI($this->pl->txt("logo"), 'logo');
                    $input->setSuffixes(array('png'));
                    if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $global_config_id,
                        ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
                        $input->setInfo('<img src="'
                            . ilParticipationCertificateConfig::returnPicturePath('relative', $global_config_id,
                                ilParticipationCertificateConfig::LOGO_FILE_NAME) . '" />');
                    }
                    break;
                case "page1_issuer_signature":
                    $input = new ilFileInputGUI($config->getConfigKey(), $config->getConfigKey());
                    $input->setSuffixes(array('png'));
                    if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $global_config_id,
                        ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
                        $input->setInfo('<img src="'
                            . ilParticipationCertificateConfig::returnPicturePath('relative', $global_config_id,
                                ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME) . '" />');
                    }
                    break;
		case "true_name_helper":
                    $input = new ilTextAreaInputGUI($this->pl->txt("true_name_helper"), $config->getConfigKey());
		    //TODO add RepoPicker
		    //$input = new ilRepositorySelectorExplorerGUI($this, "showTargetSelectionTree");
		    //$input->setTypeWhiteList(array("xudf"));
                    //$input->setSelectMode("target",true);
                    $input->setValue($config->getConfigValue());
		    $input->setRows(1);
                    break;

		default:
                    $input = new ilTextAreaInputGUI($config->getConfigKey(), $config->getConfigKey());
                    $input->setRows(3);
                    $input->setValue($config->getConfigValue());
                    break;
            }

            if ($input !== null) {
                $form->addItem($input);
            }
        }

        $form->addCommandButton(ilParticipationCertificateConfigGUI::CMD_SAVE, $this->pl->txt("save"));

        return $form;
    }

    protected function getUdfDropdownValues(): array
    {

        $sql = "SELECT * FROM udf_definition";

        $results = $this->db->query($sql);

        $data = array();
        while ($row = $this->db->fetchAssoc($results)) {
            $data[$row['field_id']] = $row['field_name'];
        }

        return $data;
    }

    /**
     * @throws arException
     * @throws ilCtrlException
     */
    public function save(): bool
    {
        global $DIC;
        $global_config_id = filter_input(INPUT_GET, 'id');
        $set_type = filter_input(INPUT_GET, 'set_type');
        $form = $this->initForm($global_config_id, $set_type);

        $DIC->ctrl()->setParameter($this, "id", $global_config_id);
        $DIC->ctrl()->setParameter($this, "set_type", $set_type);

	$this->err_helper = false;
        if (!$form->checkInput()) {
            $this->tpl->setContent($form->getHTML());
            return false;
        }

        $part_cert_configs = new ilParticipationCertificateConfigs();
        switch ($set_type) {
            case ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE:
                //save Text
                foreach ($form->getItems() as $item) {
                    /**
                     * @var ilFormPropertyGUI $item
                     */
                    switch ($item->getPostVar()) {
                        case 'config_title':
                            /**
                             * @var ilParticipationCertificateGlobalConfigSet $global_config
                             */
                            $global_config = ilParticipationCertificateGlobalConfigSet::findOrGetInstance($global_config_id);
                            $global_config->setTitle($form->getInput($item->getPostVar()));
                            $global_config->store();
                            break;
                        case 'logo':
                            //Picture
                            $file_data = $form->getInput('logo');
                            if ($file_data['tmp_name']) {
                                ilParticipationCertificateConfig::storePicture($file_data, $global_config_id,
                                    ilParticipationCertificateConfig::LOGO_FILE_NAME);
                            }
                            break;
                        case "page1_issuer_signature":
                            $file_data = $form->getInput('page1_issuer_signature');
                            if ($file_data['tmp_name']) {
                                /**
                                 * @var array $input
                                 */
                                ilParticipationCertificateConfig::storePicture($file_data, $global_config_id,
                                    ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME);
                            }
                            break;
                        default:
                            if (is_array($form->getInput($item->getPostVar()))) {
                                echo $item->getPostVar();
                                exit;
                            }
                            $global_config = $part_cert_configs->getParticipationTemplateConfigValueByKey($global_config_id,
                                $item->getPostVar());
                            $global_config->setConfigValue($form->getInput($item->getPostVar()));
                            $global_config->store();
                            break;
                    }
                }
                break;
            case ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL:
                foreach ($form->getItems() as $item) {
                    /**
                     * @var ilFormPropertyGUI $item
                     */
                    switch ($item->getPostVar()) {
                        case 'logo':
                            //Picture
                            $file_data = $form->getInput('logo');
                            if ($file_data['tmp_name']) {
                                ilParticipationCertificateConfig::storePicture($file_data, $global_config_id,
                                    ilParticipationCertificateConfig::LOGO_FILE_NAME);
                            }
			    break;
			case 'true_name_helper';
                            $global_config = $part_cert_configs->getParticipationGlobalConfigValueByKey($item->getPostVar());
			    $userinput=trim($form->getInput($item->getPostVar()));
			    if (!ctype_digit($userinput) and $userinput != "") {
				    $userinput="";
				    $this->err_helper = true;
			    }
			    $global_config->setConfigValue($userinput);
                            $global_config->store();
			    break;

                        default:
                            /**
                             * @var ilFormPropertyGUI $item
                             */
                            $global_config = $part_cert_configs->getParticipationGlobalConfigValueByKey($item->getPostVar());
                            $global_config->setConfigValue($form->getInput($item->getPostVar()));
                            $global_config->store();
                            break;
                    }
                }
                break;
        }
	    if ($this->err_helper) {
		    $this->ctrl->redirect($this, self::CMD_SHOW_FORM_ERR);
		} else {
        	$this->ctrl->redirect($this, self::CMD_CONFIGURE);
		}
	    return true;
    }

    public function configure(): void
    {
        $this->tpl->loadStandardTemplate();

        $this->initTable();

        $this->tpl->setContent($this->table->getHTML());
    }

    protected function initTable()
    {
        $this->table = new ilParticipationCertificateConfigSetTableGUI($this, self::CMD_CONFIGURE);
    }
}
