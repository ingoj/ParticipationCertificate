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
 * @ilCtrl_isCalledBy ilParticipationCertificateTwigParser: ilParticipationCertificateGUI
 */
class ilParticipationCertificateTwigParser {

	/**
	 * ilParticipationCertificateTwigParser constructor.
	 *
	 * @param int $group_ref_id
	 * @param array $twig_options
	 * @param bool $ementor
	 */
	public function __construct($group_ref_id = 0,$twig_options = array(),$ementor = true) {




		$this->group_ref_id = $group_ref_id;

		$cert_access = new ilParticipationCertificateAccess($group_ref_id);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		$this->loadTwig();
		$loader = new \Twig_Loader_Filesystem('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/templates/report/');
		$twig = new \Twig_Environment($loader, $twig_options);

		$this->ementor = $ementor;

		$this->twig_template = $twig->load('certificate.html');


	}

	public function parseData() {

		$arr_text_values = ilParticipationCertificateConfig::returnTextValues($this->group_ref_id,ilParticipationCertificateConfig::CONFIG_TYPE_GROUP);

		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$arr_lo_master_crs = ilLearningObjectivesMasterCrs::getData($this->usr_ids);
		$arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$arr_excercise_states = ilExcerciseStates::getData($this->usr_ids);
		$arr_iass_states = ilIassStates::getData($this->usr_ids);
		$arr_learn_reached_percentages = ilLearnObjectSuggReachedPercentages::getData($this->usr_ids);

		$date = new ilDate(time(),IL_CAL_UNIX);

		$part_pdf = new ilParticipationCertificatePDFGenerator();



		foreach($this->usr_ids as $user_id) {

			//Preprocess text values
			foreach($arr_text_values as $key => $value) {
				$twig = new \Twig_Environment(new \Twig_Loader_String());
				$peparsed_value = $twig->render($value,
					array("username" => ($arr_usr_data[$user_id]->getPartCertSalutation() ? $arr_usr_data[$user_id]->getPartCertSalutation().' ':'').$arr_usr_data[$user_id]->getPartCertFirstname().' '.$arr_usr_data[$user_id]->getPartCertLastname(),
						'date' => $date->get(IL_CAL_FKT_DATE,'d.m.Y'))
				);
				$arr_text_values[$key] = $peparsed_value;
			}


			//Learning Objective Master Course
			$arr_usr_lo_master_crs = array();
			if(is_array($arr_lo_master_crs[$user_id])) {
				$arr_usr_lo_master_crs = $arr_lo_master_crs[$user_id];
			}

			//Initial Test
			$initial_test_state = 0;
			if(is_object($arr_initial_test_states[$user_id])) {
				$initial_test_state = $arr_initial_test_states[$user_id]->getCrsitestItestSubmitted();
			}
			//Percentage final tests of suggested modules
			$learn_sugg_reached_percentage = 0;
			if(is_object($arr_learn_reached_percentages[$user_id])) {
				$learn_sugg_reached_percentage = $arr_learn_reached_percentages[$user_id]->getAveragePercentage();
			}
			//Video Conferences
			$iass_state = 0;
			if(is_object($arr_iass_states[$user_id])) {
				$iass_state = $arr_iass_states[$user_id]->getPassed();
			}
			//Home Work
			$excercise_percentage = 0;
			if(is_object($arr_excercise_states[$user_id])) {
				$excercise_percentage = $arr_excercise_states[$user_id]->getPassedPercentage();
			}


			$arr_render = array('text_values' => $arr_text_values,
								'show_ementoring' => $this->ementor,
								'arr_lo_master_crs' => $arr_usr_lo_master_crs,
								'crsitest_itest_submitted' => $initial_test_state,
								'learn_sugg_reached_percentage' => $learn_sugg_reached_percentage,
								'iass_state' => $iass_state,
								'excercise_percentage' => $excercise_percentage,
								'logo_path' => ilParticipationCertificateConfig::returnPicturePath()
			);

			$part_pdf->generatePDF($this->twig_template->render($arr_render),count($this->usr_ids));
		}


		/*

		$results_lernmodule = $this->all_result_learnmodule[$user_id];
		if($results_lernmodule['einstiegstest'] == 1){
			$check1 = 'Ja';
		}
		else{
			$check1 = 'Nein';
		}


		//Berechung des zweiten Prozentwertes... Bearbeitung von Aufgaben zur Vertiefung von Inhalten des Studienvo..
		$homework_done = $this->all_homework_done[$user_id];
		$vconf = $this->vConf[$user_id];
		if ($vconf['participated'] >= 1){
			$check2 = 'Ja';
		}
		else{
			$check2 = 'Nein';
		}

		$percent = 0;
		if($homework_done['total']) {
			$percent = $homework_done["passed"] / $homework_done['total'] * 100;
		}
		$secondresult = number_format($percent, 2) .'%';
		$firstresult = number_format($results_lernmodule['average_percentage'], 2) .'%';


		$percent = round($percent,2);
		$percent = $percent.'%';
		$this->replacePlaceholdersForm();

		//path to the header image
		$img_path = "/var/iliasdata/ilias/default/dhbw_part_cert/img/pic.png";


		if ($homework_done['passed'] == NULL){
			$homework_done['passed'] = '0';
		}
		if($vconf['participated'] == NULL){
			$vconf['participated'] = 'Es wurde nicht an den Videokonferenzen teilgenommen';
		}
		else{
			$vconf['participated'] = 'Es wurde aktiv an den Videokonferenzen teilgenommen';
		}

		$rendered = $template->render(array(
			'TITLE' => $this->title,
			'INTRODUCTION' => $this->object->getDescription(),
			'check1' => $check1,
			'check2' => $check2,
			'EXPLANATION' => $resultExp,
			'EXPLANATIONTWO' =>$this->explanationtwo,
			'nameteacher' => $this->nameTeach,
			'functionteacher' => $this->funcTeach,
			'dateget' => $date,
			'path' => $img_path,
			'username' => $user_id,
			//'homeworkdone' => $homework_done["passed"],
			'homeworkdone' => $percent,
			//'maxhomework' => $homework_done['total'],
			'resultlearnmodule' => $firstresult,
			'conferencesparticipated' => $vconf['participated'],
			'resultrecess' => $secondresult,
			'modulesdone' => $firstresult,
			'learning_objectives' => $this->theme_get,
			'ementoring' => $this->check_ementoring
		));*/




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