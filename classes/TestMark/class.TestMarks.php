<?php

class TestMarks {

	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilIassState[]
	 */
	public static function getData($test_obj) {
		global $DIC;
		$ilDB = $DIC->database();
		$result = $ilDB->query(self::getSQL($test_obj));
		$mark_data = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$mark_data = new TestMark();
			$mark_data->setPassed($row['passed']);
			$mark_data->setMarkId($row['mark_id']);
			$mark_data->setMinimumlvl($row['minimum_level']);
		}

		return $mark_data;
	}


	/**
	 * @param array $arr_usr_ids
	 *
	 * @return string
	 */
	protected static function getSQL($test_obj) {
		global $DIC;
		$ilDB = $DIC->database();
		$select = "Select passed, mark_id,minimum_level from tst_tests 
					inner join tst_mark on tst_tests.test_id = tst_mark.test_fi
 				WHERE passed = 1 AND obj_fi =" . $ilDB->quote($test_obj, "integer");

		return $select;
	}
}

?>