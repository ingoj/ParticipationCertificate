<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/TestMark/class.TestMark.php';
class TestMarks {

	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilIassState[]
	 */
	public static function getData($test_obj) {
		global $ilDB;

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
		global $ilDB;

		$select = "SELECT * FROM tst_mark WHERE passed = 1 AND test_fi =".$ilDB->quote($test_obj, "integer");

		return $select;
	}
}
?>