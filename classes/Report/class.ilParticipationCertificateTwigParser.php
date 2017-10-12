<?php
require_once './Modules/Group/classes/class.ilGroupParticipants.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/CrsInitialTestState/class.ilCrsInitialTestStates.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/LearnObjectSugg/class.ilLearningObjectiveSuggestions.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/ExerciseState/class.ilExcerciseStates.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/IassState/class.ilIassStates.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/LearnObjectFinalTestState/class.ilLearnObjectFinalTestStates.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/LearnObjectSuggReachedPercentage/class.ilLearnObjectSuggReachedPercentages.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/LearnObjectMasterCrs/class.ilLearningObjectivesMasterCrs.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/PartCertUserData/class.ilPartCertUsersData.php';

/**
 * Class ilParticipationCertificateTwigParser
 *
 * @author            Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateTwigParser: ilParticipationCertificateGUI, ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateTwigParser {

	/**
	 * ilParticipationCertificateTwigParser constructor.
	 *
	 * @param int   $group_ref_id
	 * @param array $twig_options
	 * @param bool  $ementor
	 * @param int $usr_id
	 * @param bool $edited
	 * @param array $array
	 */
	public function __construct($group_ref_id = 0, $twig_options = array(), $usr_id, $ementor = true, $edited = false, $array = NULL) {


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
		$loader = new \Twig_Loader_Filesystem('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/templates/report/');
		$twig = new \Twig_Environment($loader, $twig_options);


		$this->twig_template = $twig->load('certificate.html');
	}


	public function parseData() {
		$arr_text_values = ilParticipationCertificateConfig::returnTextValues($this->group_ref_id, ilParticipationCertificateConfig::CONFIG_TYPE_GROUP);

		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$arr_lo_master_crs = ilLearningObjectivesMasterCrs::getData($this->usr_ids);
		$arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$arr_excercise_states = ilExcerciseStates::getData($this->usr_ids);
		$arr_iass_states = ilIassStates::getData($this->usr_ids);
		$arr_learn_reached_percentages = ilLearnObjectSuggReachedPercentages::getData($this->usr_ids);

		$date = new ilDate(time(), IL_CAL_UNIX);

		$part_pdf = new ilParticipationCertificatePDFGenerator();

		//quickfix, wenn nur ein User $this->usr_id ist kein array -> foreach kann also nicht gebraucht werden. Jetzt wird ein array erstellt auch wenn nur ein user
		if (count($this->usr_id) == 1) {
			$usr = $this->usr_id;
			$this->usr_id = array($usr);
		}

		foreach ($this->usr_id as $usr_id) {
			//quickfix, wenn man user auswählt kann es sein, das $usr_id ein array bleibt. Das führt weiter unten zum crash. So wird das array aufgelöst.
			if (is_array($usr_id)){
				$usr_id = $usr_id[0];
			}

			$processed_arr_text_values = $arr_text_values;
			//Preprocess text values
			foreach ($arr_text_values as $key => $value) {
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
				$learn_sugg_reached_percentage = $this->array[1];
				$iass_state = $this->array[2];
				$excercise_percentage = $this->array[3];
			} else {

				//Initial Test
				$initial_test_state = 0;
				if (is_object($arr_initial_test_states[$usr_id])) {
					$initial_test_state = $arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted();
				}
				//Percentage final tests of suggested modules
				$learn_sugg_reached_percentage = 0;
				if (is_object($arr_learn_reached_percentages[$usr_id])) {
					$learn_sugg_reached_percentage = $arr_learn_reached_percentages[$usr_id]->getAveragePercentage();
				}
				//Video Conferences
				$iass_state = 0;
				if (is_object($arr_iass_states[$usr_id])) {
					$iass_state = $arr_iass_states[$usr_id]->getPassed();
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
				'learn_sugg_reached_percentage' => $learn_sugg_reached_percentage,
				'iass_state' => $iass_state,
				'excercise_percentage' => $excercise_percentage,
				'logo_path' => ilParticipationCertificateConfig::returnPicturePath()
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
			require_once(dirname(dirname(dirname(__FILE__))) . '/vendor/twig/twig/lib/Twig/Autoloader.php');
			Twig_Autoloader::register();
			$loaded = true;
		}
	}
}

?>