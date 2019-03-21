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

}
