<?php

/**
 * Class ilParticipationCertificatePlugin
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificatePlugin extends ilUserInterfaceHookPlugin
{
    public const PLUGIN_ID = "dhbwparticipationpdf";

    public const PLUGIN_NAME = "ParticipationCertificate";

    public const PLUGIN_CLASS_NAME = self::class;

    public const PLUGIN_DIRECTORY = "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate";

    public const CERTIFICATIONS_PATH = 'dhbw_part_cert';

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
            self::$instance = $plugin;
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

    protected function afterUninstall(): void
    {
        $this->db->dropTable('participationcert', false);
        $this->db->dropTable('dhbw_part_cert_ob_conf', false);
        $this->db->dropTable('dhbw_part_cert_conf', false);
        $this->db->dropTable('dhbw_part_cert_gl_conf', false);
        $this->db->dropTable('dhbw_part_cert_files', false);

        $sequences = [
            'participationcert',
            'dhbw_part_cert_ob_conf',
            'dhbw_part_cert_conf',
            'dhbw_part_cert_gl_conf',
            'dhbw_part_cert_files'
        ];
        foreach ($sequences as $sequence) {
            try {
                $this->db->dropSequence($sequence);
            } catch (Exception $e) {
                //ignore
            }
        }
    }

    public function getImagePath(string $imageName): string
    {
        return $this->getDirectory() . "/templates/images/" . $imageName;
    }

    /**
     * @return void
     */
    protected function afterUpdate(): void
    {
        // Get all files and directories in the certifications' path
        $path = CLIENT_WEB_DIR . '/' . self::CERTIFICATIONS_PATH;
        $items = scandir($path);

        // Filter out the current (.) and parent (..) directories, and keep only directories
        $directories = array_filter($items, function ($item) use ($path) {
            return is_dir($path . '/' . $item) && $item !== '.' && $item !== '..';
        });

        $directories = array_values($directories);

        foreach ($directories as $directory) {
            $file = new ilParticipationCertificateFiles();
            if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', (int) $directory, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
                $file->setFile($directory, 'logo', false);
            }

            if (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', (int) $directory, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
                $file->setFile($directory, 'page1_issuer_signature', false);
            }
        }
    }

}
