<?php

require_once 'ParticipationParser.php';
require_once './Modules/Group/classes/class.ilGroupParticipants.php';

/**
 * Class ilParticipationCertificateTwigParser
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateTwigParser: ilParticipationCertificateGUI
 */
class ilParticipationCertificateTwigParser implements ParticipationParser {

	const CMD_PARSE = 'parseAll';
	/**
	 * @var ilParticipationCertificate
	 */
	protected $object;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var
	 */
	protected $title;
	/**
	 * @var
	 */
	protected $desc;
	/**
	 * @var
	 */
	protected $nameTeach;
	/**
	 * @var
	 */
	protected $funcTeach;
	/**
	 * @var
	 */
	protected $explanation;
	/**
	 * @var
	 */
	protected $check_ementoring;
	/**
	 * @var
	 */
	protected $result_startexam;
	/**
	 * @var
	 */
	protected $all_result_learnmodule;
	/**
	 * @var
	 * @return bool
	 */
	protected $participation_videoconference;
	/**
	 * @var
	 */
	protected $result_recess;
	/**
	 * @var
	 */
	protected $name_user;
	/**
	 * @var
	 */
	protected $questions_count;
	/**
	 * @var
	 */
	protected $theme_get;
	/**
	 * @var
	 */
	protected $modules_done;
	/**
	 * @var
	 */
	protected $conferences_participated;
	/**
	 * @var
	 */
	protected $all_homework_done;
	/**
	 * @var ilGroupParticipants
	 */
	public $learnGroupParticipants;
	/**
	 * @var ilDateTime
	 */
	public $date;
	/**
	 * @var \ilDB
	 */
	protected $db;
	/**
	 * @var
	 */
	public $vConf;
	/**
	 * @var int
	 */
	public $groupRefId;
	/**
	 * @var int
	 */
	public $groupObjId;
	/**
	 * @var array
	 */
	public $memberids;
	/**
	 * @var
	 */
	public $explanationtwo;
	/**
	 * @var ilParticipationCertificateConfigGUI
	 */
	public $config;
	/**
	 * @var int
	 */
	public $membercount;
	/**
	 * @var array
	 */
	protected $options = array(
		'autoescape' => false,
	);


	/**
	 * ilParticipationCertificateTwigParser constructor.
	 *
	 * @param array $options
	 */
	public function __construct(array $options = array()) {
		global $ilCtrl, $ilDB, $date;
		$this->ctrl = $ilCtrl;
		$this->db = $ilDB;


		//Get Date and format it
		$this->date = new ilDateTime(time(),IL_CAL_UNIX);
		$date = explode('-',$this->date->get(IL_CAL_DATE));
		$date = $date[2].'.'.$date[1].'.'.$date[0];


		$this->groupRefId = (int)$_GET['ref_id'];
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);

		//Fill object with values
		$this->vConf = $this->getVconf();
		$this->all_result_learnmodule = $this->getResultMath();
		$this->theme_get = $this->getLernziele();
		$this->all_homework_done = $this->getAufgaben();

		$this->config = new ilParticipationCertificateConfigGUI();

		$this->learnGroupParticipants = new ilGroupParticipants($this->groupObjId);
		$this->membercount = $this->learnGroupParticipants->getCountMembers();
		$this->memberids = $this->learnGroupParticipants->getMembers();
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', 'ref_id');

		$gui = new ilParticipationCertificateGUI();

		$this->object = ilParticipationCertificate::where([ "group_id" => $gui->groupObjId ])->first();
		if (!$this->object) {
			$this->object = ilParticipationCertificate::where([ 'group_id' => 0 ])->first();
			$this->object->setId(null);
		}
			$this->check_ementoring = $this->object->isCheckementoring();

		$this->options = array_merge($this->options, $options);
		$this->loadTwig();
	}


	public function executeCommand() {
		//$this->tpl->getStandardTemplate();
		$cmd = $this->ctrl->getCmd();
		switch ($cmd) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_PARSE);
				$this->{$cmd}();
				break;
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


	public function parseAll() {
		if ($this->membercount == 0){
			ilUtil::sendFailure('In dieser Gruppe existieren keine Mitglieder',true);
			$this->ctrl->redirectByClass(ilParticipationCertificateGUI::class,ilParticipationCertificateGUI::CMD_DISPLAY);

		}
		foreach ($this->memberids as $learnGroupParticipant) {
			$this->preparseDesc($learnGroupParticipant);
			$this->preparseExp($learnGroupParticipant);
			$this->parse($learnGroupParticipant);
		}
	}


	public function getUsername($userId) {
		global $resultsuser;
		$sql = "SELECT * FROM udf_text WHERE usr_id =" . $this->db->quote($userId, "integer");
		$resultsuser = $this->db->query($sql);

		while ($row = $this->db->fetchAssoc($resultsuser)) {
			$datauser [$row['field_id']] = $row;
		}
		$name = $datauser[$this->config->lastname]; //TODO Get the field_id from the dropdown
		$lastname = $name['value'];
		$names = $datauser[$this->config->surname];
		$surname = $names['value'];

		$fullname = $surname . ' ' . $lastname;
		$gender = $datauser[$this->config->gender];
		if ($gender['value'] == 'Männlich') {
			$fullername = 'Herr ' . $fullname;
		} elseif ($gender['gender'] == 'Weiblich') {
			$fullername = 'Frau ' . $fullname;
		}
		else{
			$fullername = $fullname;
		}


		return $fullername;
	}

	public function preparseDesc($user_id){
		global $resultdescri,$template1;
		$fileDescription =$this->object->getDescription();
		file_put_contents('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/description.html',$fileDescription);

		$this->loadTwig();

		$loader = new \Twig_Loader_Filesystem('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/');
		$twig = new \Twig_Environment($loader, $this->options);

		$template1 = $twig->load('description.html');

		$resultdescri = $template1->render(array('username' => $this->getUsername($user_id)));
	}

	public function preparseExp($user_id) {
		global $resultExp,$template2;
		$fileExp =$this->object->getExplanation();
		file_put_contents('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/explanation.html',$fileExp);


		$this->loadTwig();

		$loader = new \Twig_Loader_Filesystem('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/');
		$twig = new \Twig_Environment($loader, $this->options);

		$template2 = $twig->load('explanation.html');

		$resultExp = $template2->render(array('username' => $this->getUsername($user_id)));
	}


	public function preparseExptwo($user_id) {
		global $resultExptwo,$template3;
		$fileExptwo =$this->object->getExplanationtwo();
		file_put_contents('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/explanationtwo.html',$fileExptwo);


		$this->loadTwig();

		$loader = new \Twig_Loader_Filesystem('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/');
		$twig = new \Twig_Environment($loader, $this->options);

		$template3 = $twig->load('explanationtwo.html');

		$resultExptwo = $template3->render(array('username' => $this->getUsername($user_id)));
	}



	/**
	 * @param string $text
	 * @param array  $replacements
	 *
	 * @return string
	 */
	public function parse($user_id) {
		global $date,$resultdescri,$resultExp;

		$this->loadTwig();
		global $template;
		$loader = new \Twig_Loader_Filesystem('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/');
		$twig = new \Twig_Environment($loader, $this->options);

		//loads template for twig

		if ($this->check_ementoring == true) {
			$template = $twig->load('Teilnahmebescheinigung.html');
		}
		else{
			$template = $twig->load('TemplateWithouteMentoring.html');
		}
		//foreach ($this->learnGroupParticipants as $learnGroupParticipant) {
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


		$this->replacePlaceholdersForm();


		if ($homework_done['passed'] == NULL){
			$homework_done['passed'] = '0';
		}
		if($vconf['participated'] == NULL){
			$vconf['participated'] = 'Es wurde nicht an den Videokonoferenzen teilgenommen';
		}
		else{
			$vconf['participated'] = 'Es wurde aktiv an den Videokonferenzen teilgenommen';
		}

			$rendered = $template->render(array(
				'TITLE' => $this->title,
				'INTRODUCTION' => $resultdescri,
				'check1' => $check1,
				'check2' => $check2,
				'EXPLANATION' => $resultExp,
				'EXPLANATIONTWO' =>$this->explanationtwo,
				'nameteacher' => $this->nameTeach,
				'functionteacher' => $this->funcTeach,
				'dateget' => $date,
				//'username' => $this->getUsername($user_id),
				'homeworkdone' => $homework_done["passed"],
				'maxhomework' => $homework_done['total'],
				'resultlearnmodule' => $firstresult,
				'conferencesparticipated' => $vconf['participated'],
				'resultrecess' => $secondresult,
				'modulesdone' => $firstresult,
				'learning_objectives' => $this->theme_get
			));

		$mpdf = new ilParticipationCertificatePDFGenerator();
		$mpdf->generatePDF($rendered);

	}


	public function replacePlaceholdersForm() {
		$this->title = $this->object->getTitle();
		$this->desc = $this->object->getDescription();
		$this->funcTeach = $this->object->getTeacherfunction();
		$this->nameTeach = $this->object->getTeachername();
		$this->explanation = $this->object->getExplanation();
		$this->explanationtwo = $this->object->getExplanationtwo();

	}



	public function getLernziele() {
		global $results;
		$sql = "SELECT 
DISTINCT crso.title

FROM obj_members as memb
inner join usr_data as usr on usr.usr_id = memb.usr_id
inner join object_data as obj on obj.obj_id = memb.obj_id and obj.type = 'grp'
inner join object_reference as grp_ref on grp_ref.obj_id = obj.obj_id
inner join alo_suggestion as sugg on sugg.user_id = memb.usr_id
inner join crs_objectives as crso on crso.crs_id = sugg.course_obj_id
inner join object_data as crs_obj on crs_obj.obj_id = crso.crs_id
where grp_ref.ref_id = " . $this->db->quote($this->groupRefId, "integer") . "
";
		$results = $this->db->query($sql);

		while ($row = $this->db->fetchAssoc($results)) {
			$data [] = $row;
		}

		return $data;
	}


	public function getResultMath() {
		global $results1;

		$sql = "DROP TABLE IF Exists tmp_test_result";
		$this->db->query($sql);

		$sql = "CREATE Temporary Table tmp_test_result (SELECT max(points) as points, active_fi, maxpoints FROM tst_pass_result group by active_fi, maxpoints)";
		$this->db->query($sql);


		$sql = "DROP TABLE IF Exists lp_percentage";
		$this->db->query($sql);

		$sql = "CREATE Temporary Table lp_percentage (
					SELECT 
						usr.usr_id,
						usr.firstname,
						usr.lastname,
						usr.login,
						crs_obj.obj_id as crs_obj_id,
						crs_obj.title as crs_obj_title,
						obj.title as learning_group_title,
						tst.submitted as einstiegstest,
						crso.objective_id as crso_objective_suggestion_id,
						crso.title as learning_objective_suggestion,
						crsolm_crs.title as learning_objectives_suggestion_crs_title,
						crsolm_crs_crso.objective_id as learning_objectives_suggestion_crs_objective_id,
						crsolm_crs_crso.title as learning_objectives_suggestion_crs_objective,
						test_act.tries,
						CASE WHEN
						round(( tmp_test_result.points/tmp_test_result.maxpoints * 100 ),2) > 0 then
						round(( tmp_test_result.points/tmp_test_result.maxpoints * 100 ),2)
						 ELSE 0
						 END  as lp_percentage
						
						FROM obj_members as memb
						inner join usr_data as usr on usr.usr_id = memb.usr_id
						inner join object_data as obj on obj.obj_id = memb.obj_id and obj.type = 'grp'
						inner join object_reference as grp_ref on grp_ref.obj_id = obj.obj_id
						inner join alo_suggestion as sugg on sugg.user_id = memb.usr_id
						inner join crs_objectives as crso on crso.objective_id = sugg.objective_id
						inner join object_data as crs_obj on crs_obj.obj_id = crso.crs_id
						inner join loc_settings as itest on itest.obj_id = sugg.course_obj_id and itest.itest is not null
						inner join tst_active as tst on tst.objective_container = sugg.course_obj_id and tst.user_fi = memb.usr_id and tst.submitted = 1
						inner join crs_objective_lm as crsolm on crsolm.objective_id = crso.objective_id
						inner join object_reference as crsolm_ref on crsolm_ref.ref_id = crsolm.ref_id
						inner join container_reference as crsolm_crs_ref on crsolm_crs_ref.obj_id = crsolm_ref.obj_id
						inner join object_data as crsolm_crs on crsolm_crs.obj_id = crsolm_crs_ref.target_obj_id and crsolm_crs.type = \"crs\"
						inner join crs_objectives as crsolm_crs_crso on crsolm_crs_crso.crs_id = crsolm_crs.obj_id
						inner join crs_objective_lm as crsolm_crs_objlm on crsolm_crs_objlm.objective_id = crsolm_crs_crso.objective_id and crsolm_crs_objlm.type = \"tst\"
						inner join tst_tests as test on test.obj_fi = crsolm_crs_objlm.obj_id
						left join tst_active as test_act on test_act.test_fi = test.test_id and test_act.user_fi = memb.usr_id
						left join tmp_test_result on tmp_test_result.active_fi = test_act.active_id 
						where grp_ref.ref_id = " . $this->db->quote($this->groupRefId, "integer") . ")";
		$this->db->query($sql);

		$sql = "SELECT 
				usr_id,
				firstname,
				lastname,
				login,
				crs_obj_id,
				crs_obj_title,
				learning_group_title,
				einstiegstest,
				avg(lp_percentage) as average_percentage
				FROM lp_percentage
				group by usr_id,
				crs_obj_id,
				firstname,
				lastname,
				login,
				crs_obj_title,
				learning_group_title,
				einstiegstest";

		$results1 = $this->db->query($sql);

		$data1 = [];
		while ($row = $this->db->fetchAssoc($results1)) {
			$data1 [$row['usr_id']] = $row;
		}

		return $data1;
	}


	public function getVconf() {
		global $results2;
		$sql = "SELECT 
usr.usr_id,
usr.firstname,
usr.lastname,
usr.login,
COUNT(CASE WHEN learning_progress = 2 /*ersetzen durch statische Variable */ THEN learning_progress END)as participated,
COUNT(learning_progress) as total
 FROM 
obj_members as memb
inner join usr_data as usr on usr.usr_id = memb.usr_id
inner join object_data as obj on obj.obj_id = memb.obj_id and obj.type = 'grp'
inner join object_reference as grp_ref on grp_ref.obj_id = obj.obj_id
inner join iass_members as iass on iass.usr_id = memb.usr_id

where grp_ref.ref_id = " . $this->db->quote($this->groupRefId, "integer") . "
group by 
usr.usr_id,
usr.firstname,
usr.lastname,
usr.login
";
		$results2 = $this->db->query($sql);

		while ($row = $this->db->fetchAssoc($results2)) {
			$data2 [$row['usr_id']] = $row;
		}

		//TODO participated und total isolieren

		return $data2;
	}


	public function getAufgaben() {
		global $results3;
		$sql = "SELECT 
usr.usr_id,
usr.firstname,
usr.lastname,
usr.login,

COUNT(CASE WHEN exerc.status = 'passed' THEN exerc.status END)as passed,
COUNT(exerc.status) as total

 FROM 
obj_members as memb
inner join usr_data as usr on usr.usr_id = memb.usr_id
inner join object_data as obj on obj.obj_id = memb.obj_id and obj.type = 'grp'
inner join object_reference as grp_ref on grp_ref.obj_id = obj.obj_id
inner join exc_mem_ass_status as exerc on exerc.usr_id = memb.usr_id

where grp_ref.ref_id = " . $this->db->quote($this->groupRefId, "integer") . "
group by 
usr.usr_id,
usr.firstname,
usr.lastname,
usr.login
";
		$results3 = $this->db->query($sql);

		while ($row = $this->db->fetchAssoc($results3)) {
			$data3 [$row['usr_id']] = $row;
		}

		return $data3;
	}


}