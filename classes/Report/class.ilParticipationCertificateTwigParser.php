<?php

use Twig\Error\SyntaxError;
use Twig\Error\LoaderError;

/**
 * Class ilParticipationCertificateTwigParser
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateTwigParser: ilParticipationCertificateGUI, ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateTwigParser
{
    protected ilParticipationCertificatePlugin $pl;
    protected int $group_ref_id;
    protected array $usr_ids;
    protected null|int|array $usr_id;
    protected bool $ementor = false;
    protected bool $footer = false;
    protected bool $edited = false;
    protected ?array $array;
    public ilTemplate|ilGlobalTemplateInterface $tpl;

    protected \Twig\TemplateWrapper $twig_template;

    public function __construct(int $group_ref_id, array $twig_options, array $usr_id = null, bool $ementor = true, bool $edited = false, array|null $array = null)
    {
        global $DIC;
        $this->pl = ilParticipationCertificatePlugin::getInstance();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->group_ref_id = $group_ref_id;

        $cert_access = new ilParticipationCertificateAccess($group_ref_id);

        $this->usr_ids = $cert_access->getUserIdsOfGroup();
        $this->usr_id = $usr_id;

        if (empty($this->usr_id)) {
            $this->usr_id = $this->usr_ids;
        }

        $this->ementor = $ementor;
        //wenn die Resultate bearbeitet wurden wird automatisch der footer auf true gesetzt
        if ($edited == true) {
            $this->footer = true;
        }
        $this->edited = $edited;
        //$array sind die abgeänderten werte
        $this->array = $array;

        $loader = new Twig\Loader\FilesystemLoader($this->pl->getDirectory() . '/templates/report/');
        $twig = new Twig\Environment($loader, [
            'cache' => false,
        ]);

        $this->twig_template = $twig->load('certificate.html');
    }

    /**
     * @param array $userIds
     * @return array
     */
    private function excludeUsersFromPrintIfMissingUserData(array $userIds): array
    {
        $arr_usr_data = ilPartCertUsersData::getData($this->pl, $userIds);

        foreach ($userIds as $key => $usrId) {
            $user_data = new ilPartCertUserData();
            if (!$user_data->checkIfUserDataFilled(
                $arr_usr_data[$usrId]->getPartCertSalutation(),
                $arr_usr_data[$usrId]->getPartCertFirstname(),
                $arr_usr_data[$usrId]->getPartCertLastname()
            )) {
                unset($userIds[$key]);
            }
        }
        return array_values($userIds);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws SyntaxError
     * @throws arException
     * @throws ilDateTimeException
     */
    public function parseData(): void
    {
        $cert_configs = new ilParticipationCertificateConfigs();
        $arr_config = $cert_configs->getObjConfigSetIfNoneCreateDefaultAndCreateNewObjConfigValues($this->group_ref_id);

        $global_config_sets = new ilParticipationCertificateGlobalConfigSets();
        if (count($arr_config) > 0) {
            $global_config_id = reset($arr_config)->getGlobalConfigId();
        }

        $arr_config_text = [];
        foreach ($arr_config as $config) {
            $arr_config_text[$config->getConfigKey()] = $config->getConfigValue();
        }

        if (count($arr_config) > 0) {
            $global_config_id = reset($arr_config)->getGlobalConfigId();
        }

        $this->tpl->setOnScreenMessage('success', $this->pl->txt('print_done'), true);

        $refId = (int) $_GET['ref_id'];
        $arr_new_iass_states = ilIassStatesMulti::getData($this->usr_ids, $refId);
        $arr_xali_states = xaliStates::getData($this->usr_ids, $refId);

        $arr_usr_data = ilPartCertUsersData::getData($this->pl, $this->usr_ids);
        $arr_lo_master_crs = ilLearningObjectivesMasterCrs::getData(ilObject::_lookupObjectId($refId), $this->usr_ids);


        $arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
        $arr_excercise_states = ilExcerciseStates::getData($this->usr_ids, $this->group_ref_id);
        $arr_iass_states = ilIassStates::getData($this->usr_ids);
        $arr_learn_sugg_results = ilLearnObjectSuggResults::getData($this->usr_ids);

        $date = new ilDate(time(), IL_CAL_UNIX);

        $part_pdf = new ilParticipationCertificatePDFGenerator();


        $logo_path = '';
        $logoIsSavedInResourceStorage = false;
        if (is_numeric($global_config_id)) {
            $isFile = is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $refId, ilParticipationCertificateConfig::LOGO_FILE_NAME));

            $file = ilParticipationCertificateFiles::getFile(
                $refId,
                'logo'
            );

            if ($isFile && !empty($file) && !$file->getResourceStorage()) {
                $logo_path = ilParticipationCertificateConfig::returnPicturePath('absolute', $refId, ilParticipationCertificateConfig::LOGO_FILE_NAME);
            } else if(!empty($file) && $file->getResourceStorage()) {
                $logoIsSavedInResourceStorage = true;
            }
        } else {
            $file = ilParticipationCertificateFiles::getFile(
                $this->group_ref_id,
                'logo'
            );

            if (!empty($file) && !$file->getResourceStorage() && is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $this->group_ref_id, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
                $logo_path = ilParticipationCertificateConfig::returnPicturePath('absolute', $this->group_ref_id, ilParticipationCertificateConfig::LOGO_FILE_NAME);
            } elseif (!empty($file) && $file->getResourceStorage()) {
                $logoIsSavedInResourceStorage = true;
            }
        }

        $page1_issuer_signature = '';
        $signatureIsSavedInResourceStorage = false;
        if (is_numeric($global_config_id)) {
            $isFile = is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $refId, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME));

            $file = ilParticipationCertificateFiles::getFile(
                $refId,
                'page1_issuer_signature'
            );

            if ($isFile && !empty($file) && !$file->getResourceStorage()) {
                $page1_issuer_signature = ilParticipationCertificateConfig::returnPicturePath('absolute', $refId, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME);

            } else if(!empty($file) && $file->getResourceStorage()) {
                $signatureIsSavedInResourceStorage = true;
            }
        } else {
            $file = ilParticipationCertificateFiles::getFile(
                $this->group_ref_id,
                'page1_issuer_signature'
            );

            if (!empty($file) && !$file->getResourceStorage() && is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $this->group_ref_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
                $page1_issuer_signature = ilParticipationCertificateConfig::returnPicturePath('absolute', $this->group_ref_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME);
            } elseif (!empty($file) && $file->getResourceStorage()) {
                $signatureIsSavedInResourceStorage = true;
            }
        }

        $this->usr_id = $this->excludeUsersFromPrintIfMissingUserData($this->usr_id);

        foreach ($this->usr_id as $usr_id) {
            $percentage = 0;

            //quickfix, wenn man user auswählt kann es sein, das $usr_id ein array bleibt. Das führt weiter unten zum crash. So wird das array aufgelöst.
            if (is_array($usr_id)) {
                $usr_id = $usr_id[0];
            }
            $processed_arr_text_values = $arr_config_text;

            //Preprocess text values
            foreach ($arr_config_text as $key => $value) {
                $twig = new Twig\Environment(new Twig\Loader\ArrayLoader());

                // Twig use the placeholders {{ }}, but plugins the  [[ ]]
                $value = $this->preparePlaceholdersForTwig($value);

                $template = $twig->createTemplate((string) $value);

                $peparsed_value = $template->render([
                    'username' => ($arr_usr_data[$usr_id]->getPartCertSalutation() ?
                            $arr_usr_data[$usr_id]->getPartCertSalutation() . ' ' : '') .
                        $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' .
                        $arr_usr_data[$usr_id]->getPartCertLastname(),
                    'date' => $date->get(IL_CAL_FKT_DATE, 'd.m.Y')
                ]);

                $processed_arr_text_values[$key] = $peparsed_value;
            }

            //Learning Objective Master Course
            $arr_usr_lo_master_crs = array();
            if (is_array($arr_lo_master_crs) && array_key_exists($usr_id, $arr_lo_master_crs) && is_array($arr_lo_master_crs[$usr_id])) {
                $arr_usr_lo_master_crs = $arr_lo_master_crs[$usr_id];
            }
            if ($this->edited) {
                $initial_test_state = $this->array[0];
                $learn_sugg_result = $this->array[1];
                $iass_state = $this->array[2];
                $excercise_percentage = $this->array[3];
            } else {

                //Initial Test
                $initial_test_state = 0;
                if (key_exists($usr_id, $arr_initial_test_states) && is_object($arr_initial_test_states[$usr_id])) {
                    $initial_test_state = $arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted();
                }
                //Percentage final tests of suggested modules
                $learn_sugg_result = 0;
                if (key_exists($usr_id, $arr_learn_sugg_results) && is_object($arr_learn_sugg_results[$usr_id])) {
                    $learn_sugg_result = $arr_learn_sugg_results[$usr_id]->getAveragePercentage(ilParticipationCertificateConfig::getConfig('calculation_type_processing_state_suggested_objectives', $_GET['ref_id']), true);
                }
                //Home Work
                $excercise_percentage = 0;
                if (key_exists($usr_id, $arr_excercise_states) && is_object($arr_excercise_states[$usr_id])) {
                    $excercise_percentage = $arr_excercise_states[$usr_id]->getPassedPercentage();
                }
            }

            /*Video Conferences */
            $countPassed = 0;
            $countTests = 0;
            if (key_exists($usr_id, $arr_new_iass_states) && is_array($arr_new_iass_states[$usr_id])) {
                foreach ($arr_new_iass_states[$usr_id] as $item) {
                    $countPassed = $countPassed + $item->getPassed();
                    $countTests = $countTests + $item->getTotal();
                }
            }

            if (key_exists($usr_id, $arr_xali_states) && is_object($arr_xali_states[$usr_id])) {
                $countPassed = $countPassed + $arr_xali_states[$usr_id]->getPassed();
                $countTests = $countTests + $arr_xali_states[$usr_id]->getTotal();
            }

            if ($countTests > 0) {
                $percentage = $countPassed / $countTests * 100;

                switch ($countTests) {
                    case 1:
                        if ($countPassed == 1) {
                            $iass_states = "<img alt='' src=" . ILIAS_ABSOLUTE_PATH . "/" . $this->pl->getImagePath("passed_s.png") . ">";
                        } else {
                            $iass_states = "<img alt='' src=" . ILIAS_ABSOLUTE_PATH . "/" . $this->pl->getImagePath("failed_s.png") . ">";
                        }
                        break;
                    default:
                        $iass_states = $countPassed . "/" . $countTests;
                        break;
                }
            } else {
                $iass_states = "<img alt='' src=" . ILIAS_ABSOLUTE_PATH . "/" . $this->pl->getImagePath("not_attempted_s.png") . ">";
            }


            if ($logoIsSavedInResourceStorage) {

                if(!empty($processed_arr_text_values['logo'])) {
                    $file = new ilParticipationCertificateFiles();
                    $src = $file->getFileSrcByStorageType(
                        $processed_arr_text_values['logo'],
                        $this->group_ref_id,
                        'logo'
                    );

                    $logo_path = $src;
                }

            }

            if ($signatureIsSavedInResourceStorage && !empty($processed_arr_text_values['page1_issuer_signature'])) {
                $file = new ilParticipationCertificateFiles();
                $src = $file->getFileSrcByStorageType(
                    $processed_arr_text_values['page1_issuer_signature'],
                    $this->group_ref_id,
                    'page1_issuer_signature'
                );

                $page1_issuer_signature = $src;
            }

            $arr_render = array(
                'text_values' => $processed_arr_text_values,
                'show_ementoring' => $this->ementor,
                'show_footer' => $this->footer,
                'arr_lo_master_crs' => $arr_usr_lo_master_crs,
                'crsitest_itest_submitted' => $initial_test_state,
                'learn_sugg_reached_percentage' => $learn_sugg_result,
                'iass_state' => $percentage,
                'iass_states' => $iass_states,
                'excercise_percentage' => $excercise_percentage,
                'logo_path' => $logo_path,
                'page1_issuer_signature' => $page1_issuer_signature,
                'standard_value' => $cert_configs->returnPercentValue($this->group_ref_id),
                'individual_assessments' => [
                    'label' => $this->pl->txt('individual_assessments'),
                    'value' => '1/3' // TODO
                ]
            );

            $part_pdf->generatePDF($this->twig_template->render($arr_render), count($this->usr_id));
        }
        
    }

    /**
     * Replace the placeholders [[ ]] with the {{ }}
     *
     * @param string $value
     * @return array|string|string[]
     */
    private function preparePlaceholdersForTwig(string $value)
    {
        $value = str_replace('[[', '{{', $value);
        $value = str_replace(']]', '}}', $value);

        return $value;
    }
}
