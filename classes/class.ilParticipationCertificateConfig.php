<?php

/**
 * Class ilParticipationCertificate
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateConfig extends ActiveRecord {

	const TABLE_NAME = 'dhbw_part_cert_conf';
	const LOGO_FILE_NAME = "pic.png";
    const ISSUER_SIGNATURE_FILE_NAME = "page1_issuer_signature.png";

	const CONFIG_SET_TYPE_TEMPLATE = 1;
	const CONFIG_SET_TYPE_GROUP = 2;
	const CONFIG_SET_TYPE_GLOBAL = 3;

	const CONFIG_VALUE_TYPE_CERT_TEXT = 1;
	const CONFIG_VALUE_TYPE_OTHER = 2;

	public function getConnectorContainerName(): string
    {
		return self::TABLE_NAME;
	}

	public static function returnDbTableName(): string
    {
		return self::TABLE_NAME;
	}

	public function __construct($primary_key = 0, arConnector $connector = null) {
		parent::__construct($primary_key, $connector);
	}

	static function getConfig(string $config_key, int $group_ref_id = 0, int $config_type = self::CONFIG_SET_TYPE_GROUP, int $config_value_type = self::CONFIG_VALUE_TYPE_OTHER, ?string $default = NULL): ?string
    {
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

	static function setConfig(string $config_key, ?string $config_value, int $group_ref_id = 0, int $config_type = self::CONFIG_SET_TYPE_GROUP, int $config_value_type = self::CONFIG_VALUE_TYPE_OTHER): void
    {
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
	protected ?int $id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected int $config_type;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected int $group_ref_id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected int $global_config_id = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected int $config_value_type;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @con_is_notnull  true
	 * @db_length       1024
	 */
	protected string $config_key;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected string $config_value = "";
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected int $order_by = 0;


	/**
	 * Get a path where the template layout file and static assets are stored
	 */
	public static function getFileStoragePath(string $type = 'img', string $path_type = "absolute", int $grp_ref_id = 0, bool $create = false): string
    {
        $path = match ($path_type) {
            "absolute" => CLIENT_WEB_DIR . '/dhbw_part_cert',
            "relative" => ilUtil::getWebspaceDir() . '/dhbw_part_cert',
            default => CLIENT_WEB_DIR . '/dhbw_part_cert',
        };

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

	public static function storePicture(array $file_data, int $template_id, string $file_name): string
    {

		self::deletePicture($template_id, $file_name);

		$file_path = self::getFileStoragePath('img', 'absolute', $template_id, true);
		ilUtil::moveUploadedFile($file_data['tmp_name'], '', $file_path . $file_name);

		return self::returnPicturePath("relative", $template_id, $file_name);
	}

	public static function deletePicture(int $template_id, string $file_name): void
    {
		$file_path = self::returnPicturePath('absolute', $template_id, $file_name);

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
	public static function returnPicturePath(string $path_type, int $grp_ref_id, $file_name): string
    {
		return self::getFileStoragePath('img', $path_type, $grp_ref_id, true) . $file_name;
	}

	public function getId(): int
    {
		return $this->id;
	}

	public function setId(int $id): void
    {
		$this->id = $id;
	}

	public function getConfigKey(): string
    {
		return $this->config_key;
	}

	public function setConfigKey(string $config_key): void
    {
		$this->config_key = $config_key;
	}

	public function getConfigValue(): string
    {
		return $this->config_value;
	}


	public function setConfigValue(string $config_value): void
    {
		$this->config_value = $config_value;
	}

	public function getConfigType(): int
    {
		return $this->config_type;
	}

	public function setConfigType(int $config_type): void
    {
		$this->config_type = $config_type;
	}

	public function getConfigValueType(): int
    {
		return $this->config_value_type;
	}

	public function setConfigValueType(int $config_value_type): void
    {
		$this->config_value_type = $config_value_type;
	}

	public function getGroupRefId(): int
    {
		return $this->group_ref_id;
	}

	public function setGroupRefId(int $group_ref_id): void
    {
		$this->group_ref_id = $group_ref_id;
	}

	public function getGlobalConfigId(): int
    {
		return $this->global_config_id;
	}

	public function setGlobalConfigId(int $global_config_id): void
    {
		$this->global_config_id = $global_config_id;
	}

	public function getOrderBy(): int
    {
		return $this->order_by;
	}

	public function setOrderBy(int $order_by): void
    {
		$this->order_by = $order_by;
	}

	public static function returnDefaultValuesTypeOther(): array
    {

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