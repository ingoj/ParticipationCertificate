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
	protected static ?ilParticipationCertificatePlugin $instance = null;


    public function getPluginName(): string
    {
		return self::PLUGIN_NAME;
	}
	public static function getInstance(): ilParticipationCertificatePlugin
    {
        global $DIC;
		if (is_null(self::$instance)) {
            /** @var $component_factory ilComponentFactory */
            $component_factory = $DIC['component.factory'];
            /** @var $plugin ilParticipationCertificatePlugin */
            $plugin = $component_factory->getPlugin(ilParticipationCertificatePlugin::PLUGIN_ID);
            self::$instance  = $plugin;
		}

		return self::$instance;
	}
	protected ilDBInterface $db;

    public function __construct(
        ilDBInterface $db,
        ilComponentRepositoryWrite $component_repository,
        string $id
    ) {
        global $DIC;
        parent::__construct($db, $component_repository, $id);

        $this->db = $DIC->database();
    }

	protected function init(): void
    {
		parent::init();
        if(file_exists(__DIR__ . "/../../../../Cron/CronHook/LearningObjectiveSuggestions/vendor/autoload.php")) {
            require_once __DIR__ . "/../../../../Cron/CronHook/LearningObjectiveSuggestions/vendor/autoload.php";
        }
        if(file_exists(__DIR__ . "/../../../../EventHandling/EventHook/UserDefaults/vendor/autoload.php")) {
            require_once __DIR__ . "/../../../../EventHandling/EventHook/UserDefaults/vendor/autoload.php";
        }
        if(file_exists(__DIR__ . "/../../../../UIComponent/UserInterfaceHook/LearningObjectiveSuggestionsUI/vendor/autoload.php")) {
            require_once __DIR__ . "/../../../../UIComponent/UserInterfaceHook/LearningObjectiveSuggestionsUI/vendor/autoload.php";
        }
	}

    /**
     * @throws \srag\DIC\Exception\DICException
     */
    protected function deleteData(): void {

		self::dic()->database()->dropTable(ilParticipationCertificateGlobalConfigSet::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilParticipationCertificateObjectConfigSet::TABLE_NAME, false);


		self::dic()->database()->manipulateF('DELETE FROM ctrl_classfile WHERE comp_prefix=%s', [ ilDBConstants::T_TEXT ], [ $this->getPrefix() ]);
		self::dic()->database()->manipulateF('DELETE FROM ctrl_calls WHERE comp_prefix=%s', [ ilDBConstants::T_TEXT ], [ $this->getPrefix() ]);

		self::dic()->database()->dropTable(ilParticipationCertificateConfig::TABLE_NAME, false);
	}


	public function updateLanguages(?array $a_lang_keys = null): void
    {
		parent::updateLanguages($a_lang_keys);

		LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__ . "/../vendor/srag/removeplugindataconfirm/lang")
			->updateLanguages();
	}


    /**
     * @return bool
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return false;
    }
}
