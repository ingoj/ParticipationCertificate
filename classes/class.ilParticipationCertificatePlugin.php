<?php
include_once ('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');

/**
 * Class ilParticipationCertificatePlugin
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificatePlugin extends ilUserInterfaceHookPlugin {

	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected static $instance;



	public function getPluginName() {
		return 'ParticipationCertificate';
	}




	public static function getInstance(){

		if (is_null(self::$instance)){
			self::$instance = new self();

		}
		return self::$instance;
	}
}

