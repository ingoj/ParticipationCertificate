<?php

/**
 * Class ilParticipationCertificateTwigParser
 *
 * @author            Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateTwigParser: ilParticipationCertificateGUI, ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateTwigParser {

	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var int
	 */
	protected $group_ref_id;
	/**
	 * @var array
	 */
	protected $usr_ids;
	/**
	 * @var array
	 */
	protected $usr_id;
	/**
	 * @var bool
	 */
	protected $ementor;
	/**
	 * @var bool
	 */
	protected $footer;
	/**
	 * @var bool
	 */
	protected $edited;
	/**
	 * @var array
	 */
	protected $array;
	/**
	 * @var Twig_TemplateWrapper
	 */
	protected $twig_template;


	/**
	 * ilParticipationCertificateTwigParser constructor.
	 *
	 * @param int   $group_ref_id
	 * @param array $twig_options
	 * @param bool  $ementor
	 * @param int   $usr_id
	 * @param bool  $edited
	 * @param array $array
	 */
	public function __construct($group_ref_id = 0, $twig_options = array(), $usr_id, $ementor = true, $edited = false, $array = NULL) {
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->group_ref_id = $group_ref_id;

		$cert_access = new ilParticipationCertificateAccess($group_ref_id);

		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		$this->usr_id = $usr_id;
		//wenn keine $usr_id übegeben wird, werden alle in der Gruppe gedruckt
		if ($usr_id == NULL) {
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

		$this->loadTwig();
		$loader = new Twig\Loader\FilesystemLoader($this->pl->getDirectory() . '/templates/report/');
		$twig = new Twig\Environment($loader, $twig_options);

		$this->twig_template = $twig->load('certificate.html');
	}


	public function parseData() {


        $cert_configs = new ilParticipationCertificateConfigs();
        $arr_config = $cert_configs->getObjConfigSetIfNoneCreateDefaultAndCreateNewObjConfigValues($this->group_ref_id);

        $global_config_sets = new ilParticipationCertificateGlobalConfigSets();
        if(count($arr_config) > 0) {
            $global_config_id = reset($arr_config)->getGlobalConfigId();
        }

        if($global_config_id > 0) {
            $global_config_set = $global_config_sets->getConfigSetById($global_config_id);
            ilUtil::sendInfo($this->pl->txt('configset_type_1').' '.$global_config_set->getTitle());
        } else {
            ilUtil::sendInfo($this->pl->txt('configset_type_2'));
        }

        $arr_config_text = [];
        foreach ($arr_config as $config) {
            $arr_config_text[$config->getConfigKey()] = $config->getConfigValue();
        }

        if(count($arr_config) > 0) {
            $global_config_id = reset($arr_config)->getGlobalConfigId();
        }

        if($global_config_id > 0) {
            $global_config_set = $global_config_sets->getConfigSetById($global_config_id);
            ilUtil::sendInfo($this->pl->txt('configset_type_1').' '.$global_config_set->getTitle());
        } else {
            ilUtil::sendInfo($this->pl->txt('configset_type_2'));
        }


        $arr_new_iass_states = ilIassStatesMulti::getData($this->usr_ids,$_GET['ref_id']);
        $arr_xali_states = xaliStates::getData($this->usr_ids,$_GET['ref_id']);

		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$arr_lo_master_crs = ilLearningObjectivesMasterCrs::getData($this->usr_ids);
		$arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$arr_excercise_states = ilExcerciseStates::getData($this->usr_ids,$this->group_ref_id);
		$arr_iass_states = ilIassStates::getData($this->usr_ids);
		$arr_learn_sugg_results = ilLearnObjectSuggResults::getData($this->usr_ids);

		$date = new ilDate(time(), IL_CAL_UNIX);

		$part_pdf = new ilParticipationCertificatePDFGenerator();

		if (is_int($global_config_id) && is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $global_config_id, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
			$logo_path = ilParticipationCertificateConfig::returnPicturePath('absolute', $global_config_id, ilParticipationCertificateConfig::LOGO_FILE_NAME);
		} elseif (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $this->group_ref_id, ilParticipationCertificateConfig::LOGO_FILE_NAME))) {
            $logo_path = ilParticipationCertificateConfig::returnPicturePath('absolute', $this->group_ref_id, ilParticipationCertificateConfig::LOGO_FILE_NAME);
        } else {
			$logo_path = '';
		}

        if (is_int($global_config_id) && is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $global_config_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
            $page1_issuer_signature = ilParticipationCertificateConfig::returnPicturePath('absolute', $global_config_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME);
        }  elseif (is_file(ilParticipationCertificateConfig::returnPicturePath('absolute', $this->group_ref_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME))) {
            $page1_issuer_signature = ilParticipationCertificateConfig::returnPicturePath('absolute', $this->group_ref_id, ilParticipationCertificateConfig::ISSUER_SIGNATURE_FILE_NAME);
        } else {
            $page1_issuer_signature = '';
        }

		//quickfix, wenn nur ein User $this->usr_id ist kein array -> foreach kann also nicht gebraucht werden. Jetzt wird ein array erstellt auch wenn nur ein user
		if (!is_array($this->usr_id)) {
			$usr = $this->usr_id;
			$this->usr_id = array( $usr );
		}

		foreach ($this->usr_id as $usr_id) {
			//quickfix, wenn man user auswählt kann es sein, das $usr_id ein array bleibt. Das führt weiter unten zum crash. So wird das array aufgelöst.
			if (is_array($usr_id)) {
				$usr_id = $usr_id[0];
			}


			$processed_arr_text_values = $arr_config_text;
			//Preprocess text values
			foreach ($arr_config_text as $key => $value) {
				$twig = new \Twig_Environment(new \Twig_Loader_String());
				$peparsed_value = $twig->render($value, array(
					"username" => ($arr_usr_data[$usr_id]->getPartCertSalutation() ? $arr_usr_data[$usr_id]->getPartCertSalutation() . ' ' : '')
						. $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' . $arr_usr_data[$usr_id]->getPartCertLastname(),
					'date' => $date->get(IL_CAL_FKT_DATE, 'd.m.Y')
				));
				$processed_arr_text_values[$key] = $peparsed_value;
			}

			//Learning Objective Master Course
			$arr_usr_lo_master_crs = array();
			if (is_array($arr_lo_master_crs[$usr_id])) {
				$arr_usr_lo_master_crs = $arr_lo_master_crs[$usr_id];
			}
			if ($this->edited == true) {
				$initial_test_state = $this->array[0];
				$learn_sugg_result = $this->array[1];
				$iass_state = $this->array[2];
				$excercise_percentage = $this->array[3];
			} else {

				//Initial Test
				$initial_test_state = 0;
				if (is_object($arr_initial_test_states[$usr_id])) {
					$initial_test_state = $arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted();
				}
				//Percentage final tests of suggested modules
				$learn_sugg_result = 0;
				if (is_object($arr_learn_sugg_results[$usr_id])) {
					$learn_sugg_result = $arr_learn_sugg_results[$usr_id]->getAveragePercentage(ilParticipationCertificateConfig::getConfig('calculation_type_processing_state_suggested_objectives',$_GET['ref_id']),true);
				}
				/*Video Conferences */
                $countPassed = 0;
                $countTests = 0;
                if (is_array($arr_new_iass_states[$usr_id])) {
                    foreach ($arr_new_iass_states[$usr_id] as $item) {
                        $countPassed = $countPassed + $item->getPassed();
                        $countTests = $countTests + $item->getTotal();
                    }
                }

                if (is_object($arr_xali_states[$usr_id])) {
                    $countPassed = $countPassed + $arr_xali_states[$usr_id]->getPassed();
                    $countTests = $countTests + $arr_xali_states[$usr_id]->getTotal();
                }

                if($countTests > 0) {
                    $percentage = $countPassed / $countTests * 100;

                    switch ($countTests) {
                        case 1:
                            if ($countPassed == 1) {
                                $iass_states = "<img alt='' src=" . ILIAS_ABSOLUTE_PATH . substr($this->pl->getImagePath("passed_s.png"), 1) . ">";
                            } else {
                                $iass_states = "<img alt='' src=" . ILIAS_ABSOLUTE_PATH . substr($this->pl->getImagePath("failed_s.png"), 1) . ">";
                            }
                            break;
                        default:
                            $iass_states = $countPassed . "/" . $countTests;
                            break;
                    }
                } else {
                    $iass_states =  "<img alt='' src=" . ILIAS_ABSOLUTE_PATH . substr($this->pl->getImagePath("not_attempted_s.png"), 1) . ">";
                }

				//Home Work
				$excercise_percentage = 0;
				if (is_object($arr_excercise_states[$usr_id])) {
					$excercise_percentage = $arr_excercise_states[$usr_id]->getPassedPercentage();
				}
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
				'standard_value' => $cert_configs->returnPercentValue($this->group_ref_id)
			);

			$part_pdf->generatePDF($this->twig_template->render($arr_render), count($this->usr_id));
		}
	}


	/**
	 * Bootstrap twig engine
	 */
	protected function loadTwig() {
		static $loaded = false;
		if (!$loaded) {
			Twig_Autoloader::register();
			$loaded = true;
		}
	}
}

?>