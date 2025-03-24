<?php
class ilPartCertUsersData {
	/**
	 * @return ilPartCertUserData[]
	 */
	public static function getData(array $arr_usr_ids = array()): array
    {
		global $DIC;
		$ilDB = $DIC->database();
		$result = $ilDB->query(self::getSQL($arr_usr_ids));
		$usr_data = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$usr = new ilPartCertUserData();
			$usr->setPartCertUsrId($row['usr_id']);
			$usr->setPartCertUserName($row['login']);
			$usr->setPartCertFirstname($row['firstname']);
			$usr->setPartCertLastname($row['lastname']);

			$usr_data[$row['usr_id']] = $usr;
		}

		return $usr_data;
	}
	protected static function getSQL(array $arr_usr_ids = array()): string
    {
		global $DIC;
		$ilDB = $DIC->database();

        $select = "select 
					usr_data.usr_id,
					usr_data.login,
					udf_firstname.value as firstname,   
					udf_lastname.value as lastname
					from usr_data
					inner join " . ilParticipationCertificateConfig::TABLE_NAME . " as conf_udf_firstname on conf_udf_firstname.config_key = 'udf_firstname'
					left join udf_text as udf_firstname on udf_firstname.field_id = conf_udf_firstname.config_value and udf_firstname.usr_id = usr_data.usr_id
					inner join " . ilParticipationCertificateConfig::TABLE_NAME . " as conf_udf_lastname on conf_udf_lastname.config_key = 'udf_lastname'
					left join udf_text as udf_lastname on udf_lastname.field_id = conf_udf_lastname.config_value and udf_lastname.usr_id = usr_data.usr_id
	                where " . $ilDB->in('usr_data.usr_id', $arr_usr_ids, false, 'integer');

		return $select;
	}
}