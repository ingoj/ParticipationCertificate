<?php
require_once (dirname(dirname(__FILE__)) . '/Parser/class.ilParticipationCertificateTwigParser.php');

/**
 * Class ilParticipationCertificate
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificate extends ActiveRecord{

	const TABLE_NAME = 'il_participationcertificate';


	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 * @db_is_primary   true
	 * @db_sequence     true
	 */
	protected $id = 0;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 *
	 */

	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    date
	 */
	protected $date;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected $title;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_is_unique    true
	 * @db_length       1024
	 */
	protected $student;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_is_unique    true
	 * @db_length       1024
	 */
	protected $teacher;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       4000
	 */
	protected $description;
	/**
	 * @var bool
	 *
	 * @db_has_field true
	 * @db_fieldtype    image
	 * check ja/nein
	 */
	protected $result_startexam;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 * prozent
	 */
	protected $result_learnmodule;
	/**
	 * @var bool
	 *
	 * @db_has_field true
	 * @db_fieldtype image
	 * check ja/nein
	 */
	protected $participation_videoconference;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 * prozent
	 */
	protected $result_recess;
	/**
	 * @var array
	 * array von themen
	 */
	protected $themes;
	/**
	 * @var integer
	 *
	 * @db_has_field    true
	 * @db_fieldtype    int
	 */
	protected $questions_count;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 */
	protected $modules_done;
	/**
	 * @var integer
	 *
	 * @db_has_field    true
	 * @db_fieldtype    int
	 */
	protected $conferences_participated;
	/**
	 * @var integer
	 *
	 * @db_has_field    true
	 * @db_fieldtype    int
	 */
	protected $homework_done;


	public static function getInstanceByStudent($student){
		return static::where(array('student' => $student))->first();
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getDate() {
		return $this->date;
	}


	/**
	 * @param string $date
	 */
	public function setDate($date) {
		$this->date = $date;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getStudent() {
		return $this->student;
	}


	/**
	 * @param string $student
	 */
	public function setStudent($student) {
		$this->student = $student;
	}


	/**
	 * @return string
	 */
	public function getTeacher() {
		return $this->teacher;
	}


	/**
	 * @param string $teacher
	 */
	public function setTeacher($teacher) {
		$this->teacher = $teacher;
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}


	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return bool
	 */
	public function isResultStartexam() {
		return $this->result_startexam;
	}


	/**
	 * @param bool $result_startexam
	 */
	public function setResultStartexam($result_startexam) {
		$this->result_startexam = $result_startexam;
	}


	/**
	 * @return string
	 */
	public function getResultLearnmodule() {
		return $this->result_learnmodule;
	}


	/**
	 * @param string $result_learnmodule
	 */
	public function setResultLearnmodule($result_learnmodule) {
		$this->result_learnmodule = $result_learnmodule;
	}


	/**
	 * @return bool
	 */
	public function isParticipationVideoconference() {
		return $this->participation_videoconference;
	}


	/**
	 * @param bool $participation_videoconference
	 */
	public function setParticipationVideoconference($participation_videoconference) {
		$this->participation_videoconference = $participation_videoconference;
	}


	/**
	 * @return string
	 */
	public function getResultRecess() {
		return $this->result_recess;
	}


	/**
	 * @param string $result_recess
	 */
	public function setResultRecess($result_recess) {
		$this->result_recess = $result_recess;
	}


	/**
	 * @return array
	 */
	public function getThemes() {
		return $this->themes;
	}


	/**
	 * @param array $themes
	 */
	public function setThemes($themes) {
		$this->themes = $themes;
	}


	/**
	 * @return int
	 */
	public function getQuestionsCount() {
		return $this->questions_count;
	}


	/**
	 * @param int $questions_count
	 */
	public function setQuestionsCount($questions_count) {
		$this->questions_count = $questions_count;
	}


	/**
	 * @return string
	 */
	public function getModulesDone() {
		return $this->modules_done;
	}


	/**
	 * @param string $modules_done
	 */
	public function setModulesDone($modules_done) {
		$this->modules_done = $modules_done;
	}


	/**
	 * @return int
	 */
	public function getConferencesParticipated() {
		return $this->conferences_participated;
	}


	/**
	 * @param int $conferences_participated
	 */
	public function setConferencesParticipated($conferences_participated) {
		$this->conferences_participated = $conferences_participated;
	}


	/**
	 * @return int
	 */
	public function getHomeworkDone() {
		return $this->homework_done;
	}


	/**
	 * @param int $homework_done
	 */
	public function setHomeworkDone($homework_done) {
		$this->homework_done = $homework_done;
	}



}