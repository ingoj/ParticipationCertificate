<?php
require_once 'class.ilLearnObjectFinalTestState.php';
class ilLearnObjectFinalTestStates {

	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilLearnObjectFinalTestState[][]
	 */
	public static function getData(array $arr_usr_ids = array()) {
		global $ilDB;

		$result = $ilDB->query(self::getSQL($arr_usr_ids));
		$locftst_data = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$locftst_state = new ilLearnObjectFinalTestState();
			$locftst_state->setLocftestUsrId($row['locftest_usr_id']);
			$locftst_state->setLocftestCrsObjId($row['locftest_crs_obj_id']);
			$locftst_state->setLocftestCrsTitle($row['locftest_crs_title']);
			$locftst_state->setLocftestObjectiveId($row['locftest_objective_id']);
			$locftst_state->setLocftestObjectiveTitle($row['locftest_objective_title']);
			$locftst_state->setLocftestTestObjId($row['locftest_test_obj_id']);
			$locftst_state->setLocftestTestRefId($row['locftest_test_ref_id']);
			$locftst_state->setLocftestTestTitle($row['locftest_test_title']);
			$locftst_state->setLocftestTries($row['locftest_tries']);
			$locftst_state->setLocftestPoints($row['locftest_points']);
			$locftst_state->setLocftestMaxpoints($row['locftest_maxpoints']);
			$locftst_state->setLocftestPercentage($row['locftest_percentage']);

			$locftst_data[$row['locftest_usr_id']][$row['locftest_objective_id']] = $locftst_state;
		}

		return $locftst_data;
	}


	/**
	 * @param array $arr_usr_ids
	 *
	 * @return string
	 */
	protected static function getSQL(array $arr_usr_ids = array()) {
		global $ilDB;

		self::createTemporaryTableTestMaxResult();

		$select = "SELECT 
					crsolm.objective_id as locftest_master_crs_objective_id,
					test_act.user_fi as locftest_usr_id,
					crsolm_crs.obj_id as locftest_crs_obj_id,
					crsolm_crs.title as locftest_crs_title,
					crsolm_crs_crso.objective_id as locftest_objective_id,
					crsolm_crs_crso.title as locftest_objective_title,
					tst_ref.ref_id as locftest_test_ref_id,
					tst_ref.obj_id as locftest_test_obj_id,
					tst_obj.title as locftest_test_title,
					test_act.tries as locftest_tries,
					tmp_test_max_result.points as locftest_points,
					tmp_test_max_result.maxpoints as  locftest_maxpoints,
					COALESCE(round(( tmp_test_max_result.points/tmp_test_max_result.maxpoints * 100 ),2),0) as locftest_percentage
					FROM 
					crs_objective_lm as crsolm
					inner join object_reference as crsolm_ref on crsolm_ref.ref_id = crsolm.ref_id
					inner join container_reference as crsolm_crs_ref on crsolm_crs_ref.obj_id = crsolm_ref.obj_id
					inner join object_data as crsolm_crs on crsolm_crs.obj_id = crsolm_crs_ref.target_obj_id and crsolm_crs.type = \"crs\"
					inner join crs_objectives as crsolm_crs_crso on crsolm_crs_crso.crs_id = crsolm_crs.obj_id
					inner join loc_tst_assignments as loc_crs_ass on loc_crs_ass.objective_id = crsolm_crs_crso.objective_id
					inner join object_reference as tst_ref on tst_ref.ref_id = loc_crs_ass.tst_ref_id
					inner join tst_tests as test on test.obj_fi = tst_ref.obj_id
					inner join object_data as tst_obj on tst_obj.obj_id = test.obj_fi
					left join tst_active as test_act on test_act.test_fi = test.test_id
					left join tmp_test_max_result on tmp_test_max_result.active_fi = test_act.active_id
					where  ".$ilDB->in('test_act.user_fi', $arr_usr_ids, false, 'integer')."
					group by test_act.user_fi,
					crsolm.objective_id,
					crsolm_crs.obj_id,
					crsolm_crs_crso.objective_id,
					locftest_test_ref_id,
					locftest_test_obj_id,
					test_act.tries,
					tmp_test_max_result.points,
					tmp_test_max_result.maxpoints,
					crsolm_crs.title,
					crsolm_crs.title,
					locftest_test_title";

		return $select;
	}


	/**
	 * @param string $table_name
	 */
	protected static function createTemporaryTableTestMaxResult($table_name = 'tmp_test_max_result') {
		global $ilDB;

		$sql = "DROP TABLE IF Exists $table_name";
		$ilDB->query($sql);

		$sql = "CREATE Temporary Table $table_name  (INDEX af (active_fi)) (SELECT max(points) as points, active_fi, maxpoints FROM tst_pass_result group by active_fi, maxpoints)";
		$ilDB->query($sql);
	}


	/**
	 * @param array  $arr_usr_ids
	 * @param string $table_name
	 */
	public static function createTemporaryTableLearnObjectFinalTest(array $arr_usr_ids = array(),$table_name = 'tmp_lo_fin_test') {
		global $ilDB;

		$sql = "DROP TABLE IF Exists $table_name";
		$ilDB->query($sql);

		$sql = "CREATE Temporary Table $table_name (".self::getSql($arr_usr_ids).")";

		$ilDB->query($sql);
	}
}
?>