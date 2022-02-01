<?php

/**
 * Class ilParticipationCertificateGlobalConfig
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilParticipationCertificateGlobalConfigSet extends ActiveRecord {

	const TABLE_NAME = 'dhbw_part_cert_gl_conf';


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
	protected $order_by = 0;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       1
	 */
	protected $active = 0;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected $title = "untitled";


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


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return int
	 */
	public function getActive() {
		return $this->active;
	}


	/**
	 * @param int $active
	 */
	public function setActive($active) {
		$this->active = $active;
	}


    /**
     * @param ilParticipationCertificateConfig[] $part_cert_configs
     *
     * @return ilParticipationCertificateGlobalConfigSet
     */
	public static function createNewFromConfigs(array $part_cert_configs) {

		$gl_configs = new ilParticipationCertificateGlobalConfigSets();
		$gl_config = $gl_configs->addNewConfig();
		$gl_config->setTitle("untitled");
		$gl_config->store();


		foreach($part_cert_configs as $config) {
            $file_path_id = 0;
            if($config->getGlobalConfigId() > 0) {
                $file_path_id = $config->getGlobalConfigId() ;
            } else {
                $file_path_id = $config->getGroupRefId();
            }
            $config->setGroupRefId(0);

			switch ($config->getConfigKey()) {
				case 'logo':
					if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $file_path_id, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
						copy(ilParticipationCertificateConfig::returnPicturePath('absolute', $file_path_id, ilParticipationCertificateConfig::LOGO_FILE_NAME), ilParticipationCertificateConfig::returnPicturePath('absolute', $gl_config->getId(), ilParticipationCertificateConfig::LOGO_FILE_NAME));
					}
					break;
                case 'page1_issuer_signature':
                    if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $file_path_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
                        copy(ilParticipationCertificateConfig::returnPicturePath('absolute', $file_path_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME), ilParticipationCertificateConfig::returnPicturePath('absolute', $gl_config->getId(), ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME));
                    }

			}

			$config->setGlobalConfigId($gl_config->getId());
            $config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
            $config->create();
		}

		return $gl_config;
	}
}
