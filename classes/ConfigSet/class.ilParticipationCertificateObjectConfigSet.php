<?php

/**
 * Class ilParticipationCertificateObjectConfig
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilParticipationCertificateObjectConfigSet extends ActiveRecord {

	const TABLE_NAME = 'dhbw_part_cert_ob_conf';

	const CONFIG_TYPE_TEMPLATE = 1;
	const CONFIG_TYPE_OWN = 2;


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
	protected $obj_ref_id;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $config_type = self::CONFIG_TYPE_TEMPLATE;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $gl_conf_template_id;



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
	public function getObjRefId() {
		return $this->obj_ref_id;
	}


	/**
	 * @param int $obj_ref_id
	 */
	public function setObjRefId($obj_ref_id) {
		$this->obj_ref_id = $obj_ref_id;
	}


	/**
	 * @return int
	 */
	public function getGlConfTemplateId() {
		return $this->gl_conf_template_id;
	}


	/**
	 * @param int $gl_conf_template_id
	 */
	public function setGlConfTemplateId($gl_conf_template_id) {
		$this->gl_conf_template_id = $gl_conf_template_id;
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
}
