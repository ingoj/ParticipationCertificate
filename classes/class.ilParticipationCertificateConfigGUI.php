<?php
//TODO Refactoring - find a better way to save and display the form
require_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');
require_once("./Services/Component/classes/class.ilPluginConfigGUI.php");
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Report/class.ilParticipationCertificatePDFGenerator.php';
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificatePlugin.php');
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfig.php');
require_once './Services/Form/classes/class.ilPropertyFormGUI.php';

/**
 * Class ilParticipationCertificateConfigGUI
 *
 * @author       Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_Calls ilParticipationCertificateConfigGUI: ilParticipationCertificatePDFGenerator
 */
class ilParticipationCertificateConfigGUI extends ilPluginConfigGUI {

	const CMD_CONFIGURE = 'configure';
	const CMD_SAVE = 'save';
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
	 * @var \ilDB
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
		global $tpl, $ilCtrl, $ilTabs, $ilToolbar, $ilDB;

		$this->tpl = $tpl;
		$this->db = $ilDB;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->ilToolbar = $ilToolbar;

		$this->pl = ilParticipationCertificatePlugin::getInstance();
	}


	function performCommand($cmd) {
		switch ($cmd) {
			default:
			case 'configure':
			case 'save':
			case 'cancel':
				$this->$cmd();
				break;
		}
	}


	/**
	 * Configure
	 *
	 * @param
	 *
	 * @return
	 */
	public function configure() {

		$this->tpl->getStandardTemplate();

		$form = $this->initForm();
		$this->tpl->setContent($form->getHTML());
	}


	public function initForm() {
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle('Konfiguration Teilnahmebescheinigung');
		$form->setDescription('Folgende Platzhalter sind verfügbar: <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		&lbrace;&lbrace;date&rbrace;&rbrace;: Datum
		');

		foreach (ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT,
			"group_ref_id" => 0
		))->orderBy('order_by')->get() as $config) {
			/**
			 * @var ilParticipationCertificateConfig $config
			 */
			switch ($config->getConfigKey()) {
				case "page1_issuer_signature":
					// Skip
					$input = NULL;
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

		$uploadfield = new ilFileInputGUI('Logo', 'headerpic');
		$uploadfield->setSuffixes(array( 'png' ));
		if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', 0, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
			$uploadfield->setInfo('<img src="'
				. ilParticipationCertificateConfig::returnPicturePath('relative', 0, ilParticipationCertificateConfig::LOGO_FILE_NAME) . '" />');
		}
		$form->addItem($uploadfield);

		$options = $this->getUdfDropdownValues();
		$obj_value = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"config_key" => "udf_firstname",
			"group_ref_id" => 0
		))->first();
		$value = 0;
		if (is_object($obj_value)) {
			$value = $obj_value->getConfigValue();
		}
		$select = new ilSelectInputGUI('Benutzerdefiniertes Feld Vornamen', 'udf_firstname');
		$select->setOptions($options);
		$select->setValue($value);
		$form->addItem($select);

		$obj_value = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"config_key" => "udf_lastname",
			"group_ref_id" => 0
		))->first();
		$value = 0;
		if (is_object($obj_value)) {
			$value = $obj_value->getConfigValue();
		}
		$select = new ilSelectInputGUI('Benutzerdefiniertes Feld Nachnamen', 'udf_lastname');
		$select->setOptions($options);
		$select->setValue($value);
		$form->addItem($select);

		$obj_value = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"config_key" => "udf_gender",
			"group_ref_id" => 0
		))->first();
		$value = 0;
		if (is_object($obj_value)) {
			$value = $obj_value->getConfigValue();
		}
		$select = new ilSelectInputGUI('Benutzerdefiniertes Feld Geschlecht', 'udf_gender');
		$select->setOptions($options);
		$select->setValue($value);
		$form->addItem($select);

		$obj_value = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"config_key" => "percent_value",
			"group_ref_id" => 0
		))->first();
		$value = 0;
		if (is_object($obj_value)) {
			$value = $obj_value->getConfigValue();
		}
		$percent = new ilNumberInputGUI('Schwellenwert Videokonferenzen (Prozent)', 'percent_value');
		$percent->checkInput();
		$percent->setValue($value);
		$form->addItem($percent);

		$obj_value = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"config_key" => "color",
			"group_ref_id" => 0
		))->first();
		$value = 0;
		if (is_object($obj_value)) {
			$value = $obj_value->getConfigValue();
		}
		$color = new ilColorPickerInputGUI('Color', 'color');
		$color->setValue($value);
		$form->addItem($color);

		$obj_value = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"config_key" => "keyword",
			"group_ref_id" => 0
		))->first();
		$value = 0;
		if (is_object($obj_value)) {
			$value = $obj_value->getConfigValue();
		}
		$keyword = new ilTextInputGUI('Keyword', 'keyword');
		$keyword->setValue($value);
		$form->addItem($keyword);

		$form->addCommandButton(ilParticipationCertificateConfigGUI::CMD_SAVE, 'Speichern');

		return $form;
	}


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
		$form = $this->initForm();

		if (!$form->checkInput()) {
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
				'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
				'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT,
				"group_ref_id" => 0
			))->first();
			if (is_object($config)) {
				$input = $form->getInput($item->getPostVar());

				$config->setConfigValue($input);
				$config->store();
			}
		}

		//save UDF
		$config = ilParticipationCertificateConfig::where(array(
			'config_key' => 'udf_firstname',
			'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"group_ref_id" => 0
		))->first();
		if (!is_object($config)) {
			$config = new ilParticipationCertificateConfig();
		}
		$config->setConfigValue($form->getInput('udf_firstname'));
		$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
		$config->setConfigKey('udf_firstname');
		$config->setGroupRefId(0);
		$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
		$config->store();

		$config = ilParticipationCertificateConfig::where(array(
			'config_key' => 'udf_lastname',
			'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"group_ref_id" => 0
		))->first();
		if (!is_object($config)) {
			$config = new ilParticipationCertificateConfig();
		}
		$config->setConfigValue($form->getInput('udf_lastname'));
		$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
		$config->setConfigKey('udf_lastname');
		$config->setGroupRefId(0);
		$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
		$config->store();

		$config = ilParticipationCertificateConfig::where(array(
			'config_key' => 'udf_gender',
			'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"group_ref_id" => 0
		))->first();
		if (!is_object($config)) {
			$config = new ilParticipationCertificateConfig();
		}
		$config->setConfigValue($form->getInput('udf_gender'));
		$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
		$config->setConfigKey('udf_gender');
		$config->setGroupRefId(0);
		$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
		$config->store();

		$config = ilParticipationCertificateConfig::where(array(
			'config_key' => 'percent_value',
			'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"group_ref_id" => 0
		))->first();
		if (!is_object($config)) {
			$config = new ilParticipationCertificateConfig();
		}
		$config->setConfigValue($form->getInput('percent_value'));
		$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
		$config->setConfigKey('percent_value');
		$config->setGroupRefId(0);
		$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
		$config->store();

		$config = ilParticipationCertificateConfig::where(array(
			'config_key' => 'color',
			'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"group_ref_id" => 0
		))->first();
		if (!is_object($config)) {
			$config = new ilParticipationCertificateConfig();
		}

		$config->setConfigValue($form->getInput('color'));
		$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
		$config->setConfigKey('color');
		$config->setGroupRefId(0);
		$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
		$config->store();

		$config = ilParticipationCertificateConfig::where(array(
			'config_key' => 'keyword',
			'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"group_ref_id" => 0
		))->first();
		if (!is_object($config)) {
			$config = new ilParticipationCertificateConfig();
		}
		$config->setConfigValue($form->getInput('keyword'));
		$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
		$config->setConfigKey('keyword');
		$config->setGroupRefId(0);
		$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
		$config->store();

		//Picture
		$file_data = $form->getInput('headerpic');
		if ($file_data['tmp_name']) {
			ilParticipationCertificateConfig::storePicture($file_data, 0, ilParticipationCertificateConfig::LOGO_FILE_NAME);
		}

		$this->ctrl->redirect($this, 'configure');

		return true;
	}
}

?>