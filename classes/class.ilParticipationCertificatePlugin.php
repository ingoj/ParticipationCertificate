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
	const PLUGIN_CLASS_NAME = self::class;
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

    protected function afterUninstall(): void
    {
        $this->db->dropTable('participationcert', false);
        $this->db->dropTable('dhbw_part_cert_ob_conf', false);
        $this->db->dropTable('dhbw_part_cert_conf', false);
        $this->db->dropTable('dhbw_part_cert_gl_conf', false);

        $sequences = [
            'participationcert',
            'dhbw_part_cert_conf',
            'dhbw_part_cert_gl_conf'
        ];
        foreach ($sequences as $sequence) {
            try {
                $this->db->dropSequence($sequence);
            }catch (Exception $e){
                //ignore
            }
        }
    }

}
