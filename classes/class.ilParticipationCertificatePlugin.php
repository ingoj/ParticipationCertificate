<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilParticipationCertificatePlugin
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificatePlugin extends ilUserInterfaceHookPlugin {

	const PLUGIN_ID = "dhbwparticipationpdf";
	const PLUGIN_NAME = "ParticipationCertificate";
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected static $instance;


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @return ilParticipationCertificatePlugin
	 */
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @var ilDB
	 */
	protected $db;


	/**
	 *
	 */
	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->db = $DIC->database();
	}


	/**
	 *
	 */
	protected function init() {
		parent::init();
		require_once __DIR__ . "/../../../../Cron/CronHook/LearningObjectiveSuggestions/vendor/autoload.php";
		require_once __DIR__ . "/../../../../EventHandling/EventHook/UserDefaults/vendor/autoload.php";
		require_once __DIR__ . "/../../../../UIComponent/UserInterfaceHook/LearningObjectiveSuggestionsUI/vendor/autoload.php";
	}


	/**
	 * @return bool
	 */
	protected function beforeUninstall() {
		$this->db->dropTable(ilParticipationCertificateConfig::TABLE_NAME, false);
		$this->db->dropTable(ilParticipationCert::TABLE_NAME, false);

		ilUtil::delDir(CLIENT_WEB_DIR . "/dhbw_part_cert");

		return true;
	}
}
