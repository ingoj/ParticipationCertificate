<?php

use srag\DIC\Util\LibraryLanguageInstaller;
use srag\RemovePluginDataConfirm\PluginUninstallTrait;

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilParticipationCertificatePlugin
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificatePlugin extends ilUserInterfaceHookPlugin {

	use PluginUninstallTrait;

	const PLUGIN_ID = "dhbwparticipationpdf";
	const PLUGIN_NAME = "ParticipationCertificate";
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = ParticipationCertificateRemoveDataConfirm::class;


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
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(ilParticipationCertificateConfig::TABLE_NAME, false);
	}

	/**
	 * @inheritdoc
	 */
	public function updateLanguages($a_lang_keys = null) {
		parent::updateLanguages($a_lang_keys);

		LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__ . "/../vendor/srag/removeplugindataconfirm/lang")
			->updateLanguages();
	}


}
