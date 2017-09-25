<?php
require_once "class.ilPartCertUserData.php";

class ilPartCertUsersData {

	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilPartCertUserData[]
	 */
	public static function getData(array $arr_usr_ids = array()) {

		global $ilDB;

		$result = $ilDB->query(self::getSQL($arr_usr_ids));
		$usr_data = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$usr = new ilPartCertUserData();
			$usr->setPartCertUsrId($row['usr_id']);
			$usr->setPartCertFirstname($row['firstname']);
			$usr->setPartCertLastname($row['lastname']);
			$usr->setPartCertGender($row['gender']);
			$usr->setPartCertSalutation(self::returnSalutation($row['gender']));

			$usr_data[$row['usr_id']] = $usr;
		}

		return $usr_data;
	}

	protected static function getSQL(array $arr_usr_ids = array()) {
		global $ilDB;

		$select = "select 
					usr_data.usr_id,
					udf_firstname.value as firstname,   
					udf_lastname.value as lastname,   
					udf_gender.value as gender
					from usr_data 
					inner join dhbw_part_cert_conf as conf_udf_firstname on conf_udf_firstname.config_key = 'udf_firstname'
					left join udf_text as udf_firstname on udf_firstname.field_id = conf_udf_firstname.config_value and udf_firstname.usr_id = usr_data.usr_id
					inner join dhbw_part_cert_conf as conf_udf_lastname on conf_udf_lastname.config_key = 'udf_lastname'
					left join udf_text as udf_lastname on udf_lastname.field_id = conf_udf_lastname.config_value and udf_lastname.usr_id = usr_data.usr_id
					inner join dhbw_part_cert_conf as conf_udf_gender on conf_udf_gender.config_key = 'udf_gender'
					left join udf_text as udf_gender on udf_gender.field_id = conf_udf_gender.config_value and udf_gender.usr_id = usr_data.usr_id
	                where ".$ilDB->in('usr_data.usr_id', $arr_usr_ids, false, 'integer');

		return $select;
	}

	public static function returnSalutation($gender) {
		switch($gender) {
			case 'Männlich':
				return "Herr";
				break;
			case 'Weiblich':
				return "Frau";
				break;
			default:
				return "";
				break;
		}
	}
}
?>