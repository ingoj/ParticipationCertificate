<?php

/**
 * Class ilParticipationCertificateConfigs
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilParticipationCertificateConfigs {

	public function __construct() {

	}


	/**
	 * @param int $global_config_id
	 *
	 * @return ilParticipationCertificateConfig[]
	 */
	public function getGlobalConfigSet($global_config_id = 0) {
		return ilParticipationCertificateConfig::where(array("global_config_id" => $global_config_id))->get();
	}


	/**
	 * @param int $obj_ref_id
	 *
	 * @return array|ilParticipationCertificateConfig[]
	 * @throws arException
	 */
	public function getObjConfigSetIfNoneCreateDefault($obj_ref_id) {

		$cert_obj_config = ilParticipationCertificateObjectConfig::where(['obj_ref_id' => $obj_ref_id])->first();

		if(!is_object($cert_obj_config)) {
			$global_configs = new ilParticipationCertificateGlobalConfigs();

			$cert_obj_config = new ilParticipationCertificateObjectConfig();
			$cert_obj_config->setConfigType(ilParticipationCertificateObjectConfig::CONFIG_TYPE_TEMPLATE);
			$cert_obj_config->setObjRefId($obj_ref_id);
			$cert_obj_config->setGlConfTemplateId($global_configs->getDefaultConfig()->getId());
			$cert_obj_config->store();
		}


		switch($cert_obj_config->getConfigType()) {
			case ilParticipationCertificateObjectConfig::CONFIG_TYPE_TEMPLATE:
				return $this->getGlobalConfigSet($cert_obj_config->getGlConfTemplateId());
				break;
			case ilParticipationCertificateObjectConfig::CONFIG_TYPE_OWN:
				return ilParticipationCertificateConfig::where(array(
					"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP,
					"group_ref_id" => $obj_ref_id,
					"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
				))->orderBy('id')->get();
				break;
		}
	}

}
