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
		global $ilCtrl, $ilDB;
		$this->ctrl = $ilCtrl;
		$this->db = $ilDB;

		$this->date = new ilDateTime();
		$this->date->get(IL_CAL_DATE);
		$this->date->__toString();

		$this->groupRefId = (int)$_GET['ref_id'];
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		//$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);

		$this->vConf = $this->getVconf();
		$this->all_result_learnmodule = $this->getResultMath();
		$this->theme_get = $this->getLernziele();
		$this->all_homework_done = $this->getAufgaben();

		$this->learnGroupParticipants = new ilGroupParticipants($this->groupObjId);
		$this->membercount = $this->learnGroupParticipants->getCountMembers();
		$this->memberids = $this->learnGroupParticipants->getMembers();
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', 'ref_id');

		$gui = new ilParticipationCertificateGUI();

		$this->object = ilParticipationCertificate::where([ "group_id" => $gui->groupObjId ])->first();

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

		foreach ($this->memberids as $learnGroupParticipant) {
			$this->parse($learnGroupParticipant);
		}
	}


	public function getUsername($userId) {
		global $resultsuser;
		$sql = "SELECT firstname,lastname,gender FROM usr_data WHERE usr_id =" . $this->db->quote($userId, "integer");
		$resultsuser = $this->db->query($sql);

		while ($row = $this->db->fetchAssoc($resultsuser)) {
			$datauser [] = $row;
		}
		$name = $datauser[0];
		$lastname = $name['lastname'];
		$surname = $name['firstname'];

		$fullname = $surname . ' ' . $lastname;

		if ($name['gender'] == 'm') {
			$fullername = 'Herr ' . $fullname;
		} else {
			$fullername = 'Frau ' . $fullname;
		}

		return $fullername;
	}


	/**
	 * @param string $text
	 * @param array  $replacements
	 *
	 * @return string
	 */
	public function parse($user_id) {

		$this->loadTwig();
		global $template;
		$loader = new \Twig_Loader_Filesystem('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/');
		$twig = new \Twig_Environment($loader, $this->options);

		//loads template for twig
		$template = $twig->load('Teilnahmebescheinigung.html');

		//foreach ($this->learnGroupParticipants as $learnGroupParticipant) {
		$results_lernmodule = $this->all_result_learnmodule[$user_id];
		if($results_lernmodule['einstiegstest'] == 1){
			$check1 = 'Ja';
		}
		else{
			$check1 = 'Nein';
		}




		$homework_done = $this->all_homework_done[$user_id];
		$vconf = $this->vConf[$user_id];
		if ($vconf['participated'] >= 1){
			$check2 = 'Ja';
		}
		else{
			$check2 = 'Nein';
		}

		$this->replacePlaceholdersForm();
		//TODO Fill Placeholders with Name and etc.
		if ($homework_done == NULL) {
			$rendered = $template->render(array(
				'TITLE' => $this->title,
				'INTRODUCTION' => $this->desc,
				'check1' => $check1,
				'check2' => $check2,
				'EXPLANATION' => $this->explanation,
				'nameteacher' => $this->nameTeach,
				'functionteacher' => $this->funcTeach,
				'dateget' => $this->date,
				'username' => $this->getUsername($user_id),
				//'homeworkdone' => $homework_done["passed"],
				'resultlearnmodule' => '0%',
				//'conferencesparticipated' => $vconf['participated'],
			));
		} else {
			$rendered = $template->render(array(
				'TITLE' => $this->title,
				'INTRODUCTION' => $this->desc,
				'check1' => $check1,
				'check2' => $check2,
				'EXPLANATION' => $this->explanation,
				'nameteacher' => $this->nameTeach,
				'functionteacher' => $this->funcTeach,
				'dateget' => $this->date,
				'username' => $this->getUsername($user_id),
				'homeworkdone' => $homework_done["passed"],
				'resultlearnmodule' => $results_lernmodule['average_percentage'],
				'conferencesparticipated' => $vconf['participated'],
			));
		}
		$mpdf = new ilParticipationCertificatePDFGenerator();
		$mpdf->generatePDF($rendered);
		//return $twig->render($text, $replacements);
		//}
	}


	public function replacePlaceholdersForm() {
		$this->title = $this->object->getTitle();
		$this->desc = $this->object->getDescription();
		$this->funcTeach = $this->object->getTeacherfunction();
		$this->nameTeach = $this->object->getTeachername();
		$this->explanation = $this->object->getExplanation();
		//TODO Get the values from the SQL queries
	}


	public function getLernziele() {
		global $results;
		$sql = "SELECT 
DISTINCT crso.title

FROM ilias.obj_members as memb
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
		$sql = "SELECT 
usr.usr_id,
usr.firstname,
usr.lastname,
usr.login,
crs_obj.obj_id as crs_obj_id,
crs_obj.title as crs_obj_title,
obj.title as learning_group_title,
tst.submitted as einstiegstest,
/*crso.objective_id as crso_objective_suggestion_id,
crso.title as learning_objective_suggestion,
crsolm_crs.title as learning_objectives_suggestion_crs_title,*/
avg(crsolm_crs_lp.percentage) as average_percentage

FROM ilias.obj_members as memb
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

inner join ut_lp_marks as crsolm_crs_lp on crsolm_crs_lp.obj_id = crsolm_crs.obj_id and crsolm_crs_lp.usr_id = memb.usr_id
where grp_ref.ref_id = " . $this->db->quote($this->groupRefId, "integer") . "
group by usr.usr_id,
crs_obj.obj_id,
usr.firstname,
usr.lastname,
usr.login,
crs_obj.title,
obj.title
";
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