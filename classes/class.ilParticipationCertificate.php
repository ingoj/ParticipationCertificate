<?php
require_once (dirname(dirname(__FILE__)) . '/classes/Parser/class.ilParticipationCertificateTwigParser.php');

/**
 * Class ilParticipationCertificate
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificate extends ActiveRecord{

	const TABLE_NAME = 'participationcert';



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
	 * @db_fieldtype    date
	 */
	protected $dates;
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
	 *
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected $studentsurname;
	/**
	 * @var string
	 *
	 * @db_fieldtype    text
	 * @db_is_unique    true
	 * @db_length       1024
	 */
	protected $studentlastname;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected $teachername;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected $teacherfunction;
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
	 * @db_has_field    true
	 * @db_fieldtype    integer(1)
	 */
	protected $checkementoring;
	/**
	 * @var string
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       4000;
	 */
	protected $explanation;
	/**
	 * @var string
	 * @db_has_field    true
	 * @db_fieldtype    text;
	 * @db_length       4000;
	 */
	protected $explanationtwo;
	/**
	 * @var integer
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $group_id;
	/**
	 * @var integer
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $surname;
	/**
	 * @var integer
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $lastname;
	/**
	 * @var integer
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       8
	 */
	protected $gender;






/*
	public static function getInstanceByStudent($studentSurname){
		return static::where(array('student' => $studentSurname))->first();
	}*/


	public static function returnDbTableName(){
		return self::TABLE_NAME;
	}


	/**
	/**
	 * Get a path where the template layout file and static assets are stored
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public function getTemplatePath($type = 'img') {
		$path = CLIENT_DATA_DIR . '/dhbw_part_cert';

		switch($type) {
			case 'img':
				$path = $path . '/img/';
				if (!is_dir($path)) {
					ilUtil::makeDirParents($path);
				}
				return $path;
				break;
			default:
				if (!is_dir($path)) {
					ilUtil::makeDirParents($path);
				}
				return $path;
		}
	}



/*
	public function getFilePath(){

		$path = './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/src';
		return $path;
	}*/

	public function storePicture($file_data){
		$file_path = $this->getTemplatePath('img');
		//$suffix = pathinfo($file_data['headerpic'], PATHINFO_EXTENSION);
		file_put_contents($file_path.'picture.jpg',$file_data);
		//return $file_data['headerpic'];
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
	public function getDates() {
		return $this->dates;
	}


	/**
	 * @param string $dates
	 */
	public function setDates($dates) {
		$this->dates = $dates;
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
	public function isCheckementoring() {
		return $this->checkementoring;
	}


	/**
	 * @param bool $checkementoring
	 */
	public function setCheckementoring($checkementoring) {
		$this->checkementoring = $checkementoring;
	}

	/**
	 * @return string
	 */
	public function getExplanation() {
		return $this->explanation;
	}


	/**
	 * @param string $explanation
	 */
	public function setExplanation($explanation) {
		$this->explanation = $explanation;
	}


	/**
	 * @return string
	 */
	public function getStudentsurname() {
		return $this->studentsurname;
	}


	/**
	 * @param string $studentsurname
	 */
	public function setStudentsurname($studentsurname) {
		$this->studentsurname = $studentsurname;
	}


	/**
	 * @return string
	 */
	public function getStudentlastname() {
		return $this->studentlastname;
	}


	/**
	 * @param string $studentlastname
	 */
	public function setStudentlastname($studentlastname) {
		$this->studentlastname = $studentlastname;
	}


	/**
	 * @return string
	 */
	public function getTeachername() {
		return $this->teachername;
	}


	/**
	 * @param string $teachername
	 */
	public function setTeachername($teachername) {
		$this->teachername = $teachername;
	}


	/**
	 * @return string
	 */
	public function getTeacherfunction() {
		return $this->teacherfunction;
	}


	/**
	 * @param string $teacherfunction
	 */
	public function setTeacherfunction($teacherfunction) {
		$this->teacherfunction = $teacherfunction;
	}


	/**
	 * @return int
	 */
	public function getGroupId() {
		return $this->group_id;
	}


	/**
	 * @param int $group_id
	 */
	public function setGroupId($group_id) {
		$this->group_id = $group_id;
	}


	/**
	 * @return string
	 */
	public function getExplanationtwo() {
		return $this->explanationtwo;
	}


	/**
	 * @param string $explanationtwo
	 */
	public function setExplanationtwo($explanationtwo) {
		$this->explanationtwo = $explanationtwo;
	}

	/**
	 * @return int
	 */
	public function getGender() {
		return $this->gender;
	}


	/**
	 * @param int $gender
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}


	/**
	 * @return int
	 */
	public function getSurname() {
		return $this->surname;
	}


	/**
	 * @param int $surname
	 */
	public function setSurname($surname) {
		$this->surname = $surname;
	}


	/**
	 * @return int
	 */
	public function getLastname() {
		return $this->lastname;
	}


	/**
	 * @param int $lastname
	 */
	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}






}