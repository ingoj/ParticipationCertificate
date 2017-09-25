<?php
/**
 * Class ilParticipationCertificate
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateConfig extends ActiveRecord{

	const TABLE_NAME = 'dhbw_part_cert_conf';

	const LOGO_FILE_NAME = "pic.png";

	const CONFIG_TYPE_GLOBAL = 1;
	const CONFIG_TYPE_GROUP = 2;

	const CONFIG_VALUE_TYPE_CERT_TEXT = 1;
	const CONFIG_VALUE_TYPE_OTHER = 2;


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
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $config_type;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $group_ref_id;
	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @con_is_notnull  true
	 * @db_length       8
	 */
	protected $config_value_type;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @con_is_notnull  true
	 * @db_length       1024
	 */
	protected $config_key;
	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       1024
	 */
	protected $config_value;


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
	public static function getFileStoragePath($type = 'img') {
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


	public static function storePicture($file_data){
		$file_path = self::getFileStoragePath('img');
		ilUtil::moveUploadedFile($file_data['tmp_name'],'',$file_path.self::LOGO_FILE_NAME);
	}

	public static function returnPicturePath() {
		return self::getFileStoragePath('img').self::LOGO_FILE_NAME;
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
	public function getConfigKey() {
		return $this->config_key;
	}


	/**
	 * @param string $config_key
	 */
	public function setConfigKey($config_key) {
		$this->config_key = $config_key;
	}


	/**
	 * @return string
	 */
	public function getConfigValue() {
		return $this->config_value;
	}


	/**
	 * @param string $config_value
	 */
	public function setConfigValue($config_value) {
		$this->config_value = $config_value;
	}


	/**
	 * @return int
	 */
	public function getConfigType() {
		return $this->config_type;
	}


	/**
	 * @param int $config_type
	 */
	public function setConfigType($config_type) {
		$this->config_type = $config_type;
	}


	/**
	 * @return int
	 */
	public function getConfigValueType() {
		return $this->config_value_type;
	}


	/**
	 * @param int $config_value_type
	 */
	public function setConfigValueType($config_value_type) {
		$this->config_value_type = $config_value_type;
	}


	/**
	 * @return int
	 */
	public function getGroupRefId() {
		return $this->group_ref_id;
	}


	/**
	 * @param int $group_ref_id
	 */
	public function setGroupRefId($group_ref_id) {
		$this->group_ref_id = $group_ref_id;
	}


	public static function returnDefaultValues() {
		return	array(
		'page1_title' => 'Teilnahmebescheinigung',
		'page1_introduction1' => '{{username}}, hat am Studienvorbereitungsprogramm mit Schwerpunkt „Mathematik“ auf der Lernplattform studienvorbereitung.dhbw.de teilgenommen.',
		'page1_introduction2' => 'Die Teilnahme vor Studienbeginn an der DHBW Karlsruhe umfasste:',
		'page1_box1_title' => 'Studienvorbereitung - Mathematik:',
		'page1_box1_row1' => 'Abschluss Diagnostischer Einstiegstest Mathematik',
		'page1_box1_row2' => 'Bearbeitung von empfohlenen Mathematik- Lernmodulen',
		'page1_box2_title' => 'Studienvorbereitung -  eMentoring:',
		'page1_box2_row1' => 'Aktive Teilnahme an Videokonferenzen',
		'page1_box2_row2' => 'Bearbeitung der Aufgaben zu überfachlichen Themen:',
		'page1_location_date' => 'Karlsruhe, den {{date}}',
		'page1_issuer_name' => 'Max Mustermann',
		'page1_issuer_title' => '(Education Support Center)',
		'page2_title' => 'Erläuterungen zur Bescheinigung',
		'page2_introduction1' => 'Das  Studienvorbereitungsprogramm  mit  Schwerpunkt  Mathematik  auf  der  Lernplattform studienstart.dhbw.de,  richtet  sich  an  Studienanfänger/-innen der  Wirtschaftsinformatik  der DHBW Karlsruhe. Die Teilnehmer/-innen des Programms erhalten die Möglichkeit sich bereits vor  Studienbeginn,  Studientechniken anzueignen  sowie  das  fehlende  Vorwissen  im  Fach  „Mathematik“  aufzuarbeiten.  Dadurch  haben Studierende  mehr  Zeit  ihre  Wissenslücken  in  Mathematik zu schließen und sich mit dem neuen Lernen auseinanderzusetzen.',
		'page2_introduction2' => 'Ziel des Programms ist es,  Studienanfänger/-innen vor Studienbeginn auf das Fach Mathematik im Studium vorzubereiten. Neben der Vermittlung von mathematischen Inhalten, fördert der Online-Vorkurs  überfachliche  Kompetenzen  wie  Zeitmanagement  und  Lerntechniken  sowie  die Fähigkeit zum Selbststudium.',
		'page2_introduction3' => '{{username}} hat im Rahmen des Studienvorbereitungsprogramms mit Schwerpunkt Mathematik mit folgenden Aufgabenstellungen teilgenommen:',
		'page2_box1_title' => 'Studienvorbereitung – Mathematik',
		'page2_box1_row1' => 'Abschluss Diagnostischer Einstiegstest Mathematik',
		'page2_box2_title' => 'Studienvorbereitung – eMentoring',
		'page2_box2_row1' => 'Aktive Teilnahme an Videokonferenzen',
		'page2_box2_row2' => 'Bearbeitung der Aufgaben zu überfachlichen Themen:',
		);
	}


	/**
	 * @param group_ref_id $
	 * @param $config_type
	 */
	public static function returnTextValues($group_ref_id = 0,$config_type = self::CONFIG_TYPE_GLOBAL) {
		$arr_config = ilParticipationCertificateConfig::where(array("config_type" => $config_type, "group_ref_id" =>$group_ref_id,'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT ))->orderBy('id')->getArray('config_key','config_value');
		if(count($arr_config) == 0) {
			$arr_config = ilParticipationCertificateConfig::where(array("config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT))->orderBy('id')->getArray('config_key','config_value');
		}
		return $arr_config;
	}
}