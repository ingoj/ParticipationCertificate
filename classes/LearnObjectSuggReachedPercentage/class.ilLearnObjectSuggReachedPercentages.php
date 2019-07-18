<?php

class ilLearnObjectSuggReachedPercentages {

	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilLearnObjectSuggReachedPercentage[]
	 */
	public static function getData(array $arr_usr_ids = array()) {
		global $DIC;
		$ilDB = $DIC->database();
		$result = $ilDB->query(self::getSQL($arr_usr_ids));
		$reached_percentage_data = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$reached_percentage = new ilLearnObjectSuggReachedPercentage();
			$reached_percentage->setUsrId($row['usr_id']);
			$reached_percentage->setAveragePercentage($row['average_percentage']);
			$reached_percentage->setLimitPercentage($row['limit_perc']);
			$reached_percentage_data[$row['usr_id']] = $reached_percentage;
		}


		return $reached_percentage_data;
	}


	/**
	 * @param array $arr_usr_ids
	 *
	 * @return string
	 */
	protected static function getSQL(array $arr_usr_ids = array()) {



		ilLearnObjectFinalTestStates::createTemporaryTableLearnObjectFinalTest($arr_usr_ids, 'tmp_lo_fin_test');


		$select = "SELECT round((SUM(objectives_sug_percentage) / SUM(suggested)),0) as average_percentage, 
					usr_id, 
				
					round(avg(tst_req_percentage),0) as limit_perc
					from tmp_lo_fin_test
					group by usr_id";

		return $select;
	}
}

?>