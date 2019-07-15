<?php
use srag\Plugins\UserDefaults\UserSearch\usrdefObj;
class ilLearnObjectFinalTestStates {



	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilLearnObjectFinalTestState[][]
	 */
	public static function getData(array $arr_usr_ids = array()) {
		global $DIC;
		$ilDB = $DIC->database();
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
			$locftst_state->setLocftestPercentage($row['locftest_percentage']);
			$locftst_state->setObjectivesAllCompleted($row['objectives_all_completed']);
			$locftst_state->setObjectivesSugCompleted($row['objectives_sug_completed']);
			$locftst_state->setObjectivesSuggested($row['suggested']);
			$locftst_state->setLocftestQplsRequiredPercentage($row['locftest_qpls_required_percentage']);

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
		global $DIC;
		$ilDB = $DIC->database();


		$select = "SELECT
DISTINCT
					crsolm_crs.obj_id,
					crsolm.objective_id as locftest_master_crs_objective_id,
					crs_memb.usr_id as locftest_usr_id,
					crsolm_crs.obj_id as locftest_crs_obj_id,
					crsolm_crs.title as locftest_crs_title,
					tst.title as locftest_test_title,
					crs_objectives.objective_id as locftest_objective_id,
					crs_objectives.title as locftest_objective_title,
					loc_rnd_qpl.percentage as locftest_qpls_required_percentage,
					loc_user_results.result_perc as locftest_percentage,
    				CASE WHEN loc_user_results.result_perc > loc_rnd_qpl.percentage  then 1 else 0 end as objectives_all_completed,
    				CASE WHEN sug.course_obj_id > 0 AND loc_user_results.result_perc > loc_rnd_qpl.percentage then 1 else 0 end as objectives_sug_completed,
    				CASE WHEN sug.course_obj_id > 0 then 1 else 0 end as suggested
                    FROM 
					crs_objective_lm AS crsolm
				
					INNER JOIN loc_rnd_qpl on loc_rnd_qpl.objective_id = crsolm.objective_id and crsolm.type = 'tst'
					 INNER JOIN object_data as tst on tst.obj_id = loc_rnd_qpl.tst_id
					INNER JOIN crs_objectives on crs_objectives.objective_id = crsolm.objective_id  and loc_rnd_qpl.container_id = crs_objectives.crs_id 
                    INNER JOIN object_data AS crsolm_crs ON crsolm_crs.obj_id = crs_objectives.crs_id 
					INNER JOIN obj_members as crs_memb on ".$ilDB->in('crs_memb.usr_id ', $arr_usr_ids, false, 'integer')." and crs_memb.obj_id = crsolm_crs.obj_id
                    LEFT JOIN
			    loc_user_results ON loc_user_results.objective_id = crsolm.objective_id
			        AND loc_user_results.user_id = crs_memb.usr_id
			        AND loc_user_results.objective_id = crsolm.objective_id
			        AND loc_user_results.type = ".ilLOUserResults::TYPE_QUALIFIED." 
			   
			   /**
			   Master LOK and Suggetions
			   **/ 
			   LEFT JOIN container_reference as crsr on crsr.target_obj_id = crsolm_crs.obj_id 
               LEFT JOIN object_reference as crsr_ref on crsr_ref.obj_id =  crsr.obj_id
               LEFT JOIN tree on tree.child = crsr_ref.ref_id
               LEFT JOIN object_reference as master_loc_ref on master_loc_ref.ref_id = tree.parent
               LEFT JOIN crs_objectives as master_objective on master_objective.crs_id = master_loc_ref.obj_id
               LEFT JOIN alo_suggestion as sug on sug.objective_id = master_objective.objective_id and sug.user_id = crs_memb.usr_id
			        
		ORDER BY ORDER BY crs_objectives.position, crsolm_crs.title, crs_objectives.title ";

		return $select;
	}


	/**
	 * @param string $table_name
	 */
	protected static function createTemporaryTableTestMaxResult($table_name = 'tmp_test_max_result') {
		global $DIC;
		$ilDB = $DIC->database();
		$sql = "DROP TABLE IF Exists $table_name";
		$ilDB->query($sql);

		$sql = "CREATE Temporary Table $table_name  (INDEX af (active_fi)) (SELECT max(points) as points, active_fi, maxpoints FROM tst_pass_result group by active_fi, maxpoints)";
		$ilDB->query($sql);
	}


	/**
	 * @param array  $arr_usr_ids
	 * @param string $table_name
	 */
	public static function createTemporaryTableLearnObjectFinalTest(array $arr_usr_ids = array(), $table_name = 'tmp_lo_fin_test') {
		global $DIC;
		$ilDB = $DIC->database();
		$sql = "DROP TABLE IF Exists $table_name";
		$ilDB->query($sql);

		$sql = "CREATE Temporary Table $table_name (" . self::getSQL($arr_usr_ids) . ")";
		$ilDB->query($sql);
	}
}

?>