<?php

require_once __DIR__ . "/../vendor/autoload.php";

//TODO Refactoring - find a better way to save and display the form

/**
 * Class ilParticipationCertificateConfigGUI
 *
 * @author       Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_Calls ilParticipationCertificateConfigGUI: ilParticipationCertificatePDFGenerator
 */
class ilParticipationCertificateConfigGUI extends ilPluginConfigGUI {

	const CMD_SHOW_FORM = 'showForm';
	const CMD_ADD_CONFIG = 'addConfig';
	const CMD_COPY_CONFIG = 'copyConfig';
	const CMD_DELETE_CONFIG = 'deleteConfig';
	const CMD_SET_ACTIVE = 'setActive';
	const CMD_SET_INACTIVE = 'setInactive';
	const CMD_CONFIGURE = 'configure';
	const CMD_SAVE = 'save';
	const CMD_SAVE_ORDER = 'saveOrder';
	const CMD_CANCEL = 'cancel';
	/**
	 * @var ilParticipationCertificateConfig
	 */
	protected $object;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificateConfigSetTableGUI
	 */
	protected $table;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilToolbarGUI
	 */
	protected $ilToolbar;
	/**
	 * @var ilGroupParticipants
	 */
	protected $learnGroupParticipants;
	/**
	 * @var ilObjCourse
	 */
	protected $courseobject;
	/**
	 * @var ilDB
	 */
	protected $db;
	/**
	 * @var mixed
	 */
	public $dropValues;
	/**
	 * @var
	 */
	public $surname;
	/**
	 * @var
	 */
	public $lastname;
	/**
	 * @var
	 */
	public $gender;


	/**
	 * ilParticipationCertificateConfigGUI constructor.
	 *
	 */
	public function __construct() {
		global $DIC;

		$this->tpl = $DIC->ui()->mainTemplate();
		$this->db = $DIC->database();
		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();
		$this->ilToolbar = $DIC->toolbar();

		$this->pl = ilParticipationCertificatePlugin::getInstance();
	}


	/**
	 * @param string $cmd
	 */
	function performCommand($cmd) {
		switch ($cmd) {
			default:
			case self::CMD_ADD_CONFIG:
			case self::CMD_DELETE_CONFIG:
			case self::CMD_SET_ACTIVE:
			case self::CMD_SET_INACTIVE:
			case self::CMD_COPY_CONFIG:
			case self::CMD_SHOW_FORM:
			case self::CMD_CONFIGURE:
			case self::CMD_SAVE:
			case self::CMD_CANCEL:
			case self::CMD_SAVE_ORDER:
				$this->$cmd();
				break;
		}
	}

	public function addConfig() {
		$gl_configs = new ilParticipationCertificateGlobalConfigSets();
		$gl_config = $gl_configs->getDefaultConfig();
		$new_config_set = $gl_config->duplicate();

		$this->ctrl->setParameter($this,"id",$new_config_set->getId());
		$this->ctrl->setParameter($this,"set_type",ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$this->ctrl->redirect($this, self::CMD_SHOW_FORM);
	}


	public function copyConfig() {
		$id = filter_input(INPUT_GET,'id');

		$gl_config = new ilParticipationCertificateGlobalConfigSet($id);
		$new_config_set = $gl_config->duplicate();

		$this->ctrl->setParameter($this,"id",$new_config_set->getId());
		$this->ctrl->setParameter($this,"set_type",ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$this->ctrl->redirect($this, self::CMD_SHOW_FORM);

	}

	public function deleteConfig() {
		$id = filter_input(INPUT_GET,'id');

		$gl_config = new ilParticipationCertificateGlobalConfigSet($id);

		if($gl_config->getOrderBy() === 1) {
			$this->ctrl->redirect($this, self::CMD_CONFIGURE);
		}

		$gl_config->delete();


		$configs = new ilParticipationCertificateConfigs();
		foreach($configs->getGlobalConfigSet($id) as $config) {
			$config->delete();
		}

		$this->ctrl->redirect($this, self::CMD_CONFIGURE);
	}

	public function setActive() {
		$id = filter_input(INPUT_GET,'id');

		$gl_config = new ilParticipationCertificateGlobalConfigSet($id);
		$gl_config->setActive(1);
		$gl_config->store();

		$this->ctrl->redirect($this, self::CMD_CONFIGURE);
	}

	public function setInactive() {
		$id = filter_input(INPUT_GET,'id');

		$gl_config = new ilParticipationCertificateGlobalConfigSet($id);

		if($gl_config->getOrderBy() === 1) {
			$this->ctrl->redirect($this, self::CMD_CONFIGURE);
		}
		$gl_config->setActive(0);
		$gl_config->store();

		$this->ctrl->redirect($this, self::CMD_CONFIGURE);
	}


	public function saveOrder() {

		$configs = new ilParticipationCertificateGlobalConfigSets();
		$configs->saveAndRearangeOrderBy($_POST['order_by']);

		$this->ctrl->redirect($this, self::CMD_CONFIGURE);
	}

	/**
	 * Configure
	 */
	public function showForm() {

		$id = filter_input(INPUT_GET,'id');
		$set_type = filter_input(INPUT_GET,'set_type');




		$this->ctrl->setParameter($this,"id",$id);

		$this->tpl->getStandardTemplate();

		$form = $this->initForm($id,$set_type);
		$this->tpl->setContent($form->getHTML());
	}


	/**
	 * @param int $global_config_id
	 * @param int $configset_type
	 *
	 * @return ilPropertyFormGUI
	 * @throws arException
	 */
	public function initForm($global_config_id = 0,$configset_type) {
		global $DIC;

		$DIC->ctrl()->setParameter($this,"id",$global_config_id);
		$DIC->ctrl()->setParameter($this,"set_type",$configset_type);


		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle($this->pl->txt('config_plugin'));
		$form->setDescription($this->pl->txt("placeholders") . ' <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		&lbrace;&lbrace;date&rbrace;&rbrace;: Datum
		');

		switch($configset_type) {
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
				case "keyword":
					$input = new ilTextInputGUI($this->pl->txt("keyword"), 'keyword');
					$input->setValue($config->getConfigValue());
					break;
				case "logo":
					$input = new ilFileInputGUI($this->pl->txt("logo"), 'headerpic');
					$input->setSuffixes(array( 'png' ));
					if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', 0, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
						$input->setInfo('<img src="'
							. ilParticipationCertificateConfig::returnPicturePath('relative', 0, ilParticipationCertificateConfig::LOGO_FILE_NAME) . '" />');
					}
					break;
				default:
					$input = new ilTextAreaInputGUI($config->getConfigKey(), $config->getConfigKey());
					$input->setRows(3);
					$input->setValue($config->getConfigValue());
					break;
			}

			if ($input !== NULL) {
				$form->addItem($input);
			}
		}



		$form->addCommandButton(ilParticipationCertificateConfigGUI::CMD_SAVE, $this->pl->txt("save"));

		return $form;
	}


	/**
	 * @return array
	 */
	protected function getUdfDropdownValues() {

		$sql = "SELECT * FROM udf_definition";

		$results = $this->db->query($sql);

		$data = array();
		while ($row = $this->db->fetchAssoc($results)) {
			$data[$row['field_id']] = $row['field_name'];
		}

		return $data;
	}


	/**
	 * @return bool
	 */
	public function save() {
		global $DIC;

		$global_config_id = filter_input(INPUT_GET,'id');
		$set_type = filter_input(INPUT_GET,'set_type');
		$form = $this->initForm($global_config_id,$set_type);

		$DIC->ctrl()->setParameter($this,"id",$global_config_id);
		$DIC->ctrl()->setParameter($this,"set_type",$set_type);

		if (!$form->checkInput()) {
			$this->tpl->setContent($form->getHTML());
			return false;
		}

		$part_cert_configs = new ilParticipationCertificateConfigs();
		switch($set_type) {
			case ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE:
				//save Text
				foreach ($form->getItems() as $item) {
					/**
					 * @var ilFormPropertyGUI $item
					 */
					switch($item->getPostVar()) {
						case 'config_title':
							/**
							 * @var ilParticipationCertificateGlobalConfigSet $global_config
							 */
							$global_config = ilParticipationCertificateGlobalConfigSet::findOrGetInstance($global_config_id);
							$global_config->setTitle($form->getInput($item->getPostVar()));
							$global_config->store();
							break;
						default:
							$global_config = $part_cert_configs->getParticipationTemplateConfigValueByKey($global_config_id,$item->getPostVar());
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
					switch($item->getPostVar()) {
						case 'headerpic':
							//Picture
							$file_data = $form->getInput('headerpic');
							if ($file_data['tmp_name']) {
								ilParticipationCertificateConfig::storePicture($file_data, 0, ilParticipationCertificateConfig::LOGO_FILE_NAME);
							}
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

		$this->ctrl->redirect($this, self::CMD_CONFIGURE);
		return true;
	}

	public function configure() {
		$this->tpl->getStandardTemplate();

		$this->initTable();

		$this->tpl->setContent($this->table->getHTML());
	}

	protected function initTable() {
		$this->table = new ilParticipationCertificateConfigSetTableGUI($this, self::CMD_CONFIGURE);
	}
}
