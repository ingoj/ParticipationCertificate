<?php

/**
 * Class ilParticipationCertificate
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateConfig extends ActiveRecord {

	const TABLE_NAME = 'dhbw_part_cert_conf';
	const LOGO_FILE_NAME = "pic.png";

	const CONFIG_SET_TYPE_TEMPLATE = 1;
	const CONFIG_SET_TYPE_GROUP = 2;
	const CONFIG_SET_TYPE_GLOBAL = 3;

	const CONFIG_VALUE_TYPE_CERT_TEXT = 1;
	const CONFIG_VALUE_TYPE_OTHER = 2;


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}

	public function __construct($primary_key = 0, arConnector $connector = null) {
		parent::__construct($primary_key, $connector);
	}


	/**
	 * @param string      $config_key
	 * @param int         $group_ref_id
	 * @param int         $config_type
	 * @param int         $config_value_type
	 * @param string|null $default
	 *
	 * @return string|null
	 */
	static function getConfig($config_key, $group_ref_id = 0, $config_type = self::CONFIG_SET_TYPE_GROUP, $config_value_type = self::CONFIG_VALUE_TYPE_OTHER, $default = NULL) {
		/**
		 * @var ilParticipationCertificateConfig|null $config
		 */

		$config = self::where([
			"config_key" => $config_key,
			"config_type" => $config_type,
			"config_value_type" => $config_value_type,
			"group_ref_id" => $group_ref_id
		])->first();

		if ($config !== NULL) {
			return $config->getConfigValue();
		} else {
			return $default;
		}
	}


	/**
	 * @param string      $config_key
	 * @param string|null $config_value
	 * @param int         $group_ref_id
	 * @param int         $config_type
	 * @param int         $config_value_type
	 * @param int         $group_ref_id
	 */
	static function setConfig($config_key, $config_value, $group_ref_id = 0, $config_type = self::CONFIG_SET_TYPE_GROUP, $config_value_type = self::CONFIG_VALUE_TYPE_OTHER) {
		/**
		 * @var ilParticipationCertificateConfig|null $config
		 */

		$config = self::where([
			"config_key" => $config_key,
			"config_type" => $config_type,
			"config_value_type" => $config_value_type,
			"group_ref_id" => $group_ref_id
		])->first();

		if ($config !== NULL) {
			$config->setConfigValue($config_value);

			$config->update();
		} else {
			$config = new self();

			$config->setConfigKey($config_key);

			$config->setConfigValue($config_value);

			$config->setConfigType($config_type);

			$config->setConfigValueType($config_value_type);

			$config->setGroupRefId($group_ref_id);

			$config->create();
		}
	}


	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 * @db_is_primary   true
	 * @db_sequence     true
	 */
	protected $id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $config_type;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $group_ref_id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $global_config_id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $config_value_type;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @con_is_notnull  true
	 * @db_length       1024
	 */
	protected $config_key;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected $config_value;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $order_by = 0;


	/**
	 * Get a path where the template layout file and static assets are stored
	 *
	 * @param string $type
	 * @param string $path_type absolute|relative
	 * @param int    $grp_ref_id
	 * @param bool   $create
	 *
	 * @return string
	 */
	public static function getFileStoragePath($type = 'img', $path_type = "absolute", $grp_ref_id = 0, $create = false) {


		switch ($path_type) {
			case "absolute":
				$path = CLIENT_WEB_DIR . '/dhbw_part_cert';
				break;
			case "relative":
				$path = ilUtil::getWebspaceDir() . '/dhbw_part_cert';
				break;
			default:
				$path = CLIENT_WEB_DIR . '/dhbw_part_cert';
				break;
		}

		if ($grp_ref_id) {
			$path = $path . '/' . $grp_ref_id;
		}

		switch ($type) {
			case 'img':
				$path = $path . '/img/';
				if (!is_dir($path) && $create) {
					ilUtil::makeDirParents($path);
				}

				return $path;
				break;
			default:
				if (!is_dir($path) && $create) {
					ilUtil::makeDirParents($path);
				}

				return $path;
		}
	}


	/**
	 * @param array  $file_data
	 * @param int    $grp_ref_id
	 * @param string $file_name
	 *
	 * @return string
	 */
	public static function storePicture(array $file_data, $grp_ref_id = 0, $file_name) {

		self::deletePicture($grp_ref_id, $file_name);

		$file_path = self::getFileStoragePath('img', 'absolute', $grp_ref_id, true);
		ilUtil::moveUploadedFile($file_data['tmp_name'], '', $file_path . $file_name);

		return self::returnPicturePath("relative", $grp_ref_id, $file_name);
	}


	/**
	 * @param int    $grp_ref_id
	 * @param string $file_name
	 */
	public static function deletePicture($grp_ref_id = 0, $file_name) {
		$file_path = self::returnPicturePath('absolute', $grp_ref_id, $file_name);

		if (is_file($file_path)) {
			unlink($file_path);
		}
	}


	/**
	 * @param string $path_type absolute|relative
	 * @param int    $grp_ref_id
	 *
	 * @return string
	 */
	public static function returnPicturePath($path_type = 'absolute', $grp_ref_id = 0, $file_name) {
		return self::getFileStoragePath('img', $path_type, $grp_ref_id) . $file_name;
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getConfigKey() {
		return $this->config_key;
	}


	/**
	 * @param string $config_key
	 */
	public function setConfigKey($config_key) {
		$this->config_key = $config_key;
	}


	/**
	 * @return string
	 */
	public function getConfigValue() {
		return $this->config_value;
	}


	/**
	 * @param string $config_value
	 */
	public function setConfigValue($config_value) {
		$this->config_value = $config_value;
	}


	/**
	 * @return int
	 */
	public function getConfigType() {
		return $this->config_type;
	}


	/**
	 * @param int $config_type
	 */
	public function setConfigType($config_type) {
		$this->config_type = $config_type;
	}


	/**
	 * @return int
	 */
	public function getConfigValueType() {
		return $this->config_value_type;
	}


	/**
	 * @param int $config_value_type
	 */
	public function setConfigValueType($config_value_type) {
		$this->config_value_type = $config_value_type;
	}


	/**
	 * @return int
	 */
	public function getGroupRefId() {
		return $this->group_ref_id;
	}


	/**
	 * @param int $group_ref_id
	 */
	public function setGroupRefId($group_ref_id) {
		$this->group_ref_id = $group_ref_id;
	}


	/**
	 * @return int
	 */
	public function getGlobalConfigId() {
		return $this->global_config_id;
	}


	/**
	 * @param int $global_config_id
	 */
	public function setGlobalConfigId($global_config_id) {
		$this->global_config_id = $global_config_id;
	}


	/**
	 * @return int
	 */
	public function getOrderBy() {
		return $this->order_by;
	}


	/**
	 * @param int $order
	 */
	public function setOrderBy($order_by) {
		$this->order_by = $order_by;
	}


	public static function returnDefaultValuesTypeOther() {

		return array(
			'udf_firstname' => 0,
			'udf_lastname' => 0,
			'udf_gender' => 0,
			'color' => '73B249',
			'keyword' => 'Lerngruppe',
			'Logo' => null,
		);
	}



}
