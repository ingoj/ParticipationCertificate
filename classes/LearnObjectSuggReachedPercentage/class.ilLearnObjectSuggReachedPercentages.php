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
		ilLearningObjectiveSuggestions::createTemporaryTableLearnObjectSugg($arr_usr_ids, 'tmp_lo_sugg');
		ilLearnObjectFinalTestStates::createTemporaryTableLearnObjectFinalTest($arr_usr_ids, 'tmp_lo_fin_test');

		$select = "SELECT 
					sugg_for_user as usr_id,
					COALESCE(round(avg(average_percentage),0),0) as average_percentage
					from(
					select 
					sugg_for_user,
					locftest_crs_obj_id,
					COALESCE(round(avg(locftest_percentage),2),0) as average_percentage
					from 
					(select * from tmp_lo_sugg
					LEFT JOIN tmp_lo_fin_test on tmp_lo_fin_test.locftest_master_crs_objective_id = tmp_lo_sugg.sugg_objective_id 
					and tmp_lo_fin_test.locftest_usr_id = tmp_lo_sugg.sugg_for_user) as locftest group by sugg_for_user, locftest_crs_obj_id) as average group by sugg_for_user";

		return $select;
	}
}

?>