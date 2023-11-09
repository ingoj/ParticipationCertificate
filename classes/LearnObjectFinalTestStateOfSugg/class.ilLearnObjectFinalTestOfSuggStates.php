<?php

class ilLearnObjectFinalTestOfSuggStates
{

    /**
     * @return ilLearnObjectFinalTestOfSuggState[][]
     */
    public static function getData(array $arr_usr_ids = array()): array
    {
        global $DIC;
        $ilDB = $DIC->database();
        $result = $ilDB->query(self::getSQL($arr_usr_ids));
        $locftst_data = array();
        while ($row = $ilDB->fetchAssoc($result)) {
            $locftst_state = new ilLearnObjectFinalTestOfSuggState();
            $locftst_state->setLocftestUsrId($row['sugg_for_user']);
            $locftst_state->setLocftestCrsObjId($row['locftest_crs_obj_id']);
            $locftst_state->setLocftestCrsTitle($row['locftest_crs_title']);
            $locftst_state->setLocftestObjectiveId($row['sugg_objective_id']);
            $locftst_state->setLocftestObjectiveTitle($row['sugg_objective_title']);
            $locftst_state->setLocftestTestObjId($row['locftest_test_obj_id']);
            $locftst_state->setLocftestTestRefId($row['locftest_test_ref_id']);
            $locftst_state->setLocftestTestTitle($row['locftest_test_title']);
            $locftst_state->setObjectivesAllCompleted($row['objectives_all_completed']);
            $locftst_state->setObjectivesSugCompleted($row['objectives_sug_completed']);
            $locftst_state->setObjectivesSuggested($row['objectives_suggested']);
            //$locftst_state->setLocftestTries($row['locftest_tries']);
            //$locftst_state->setLocftestPoints($row['locftest_points']);
            //$locftst_state->setLocftestMaxpoints($row['locftest_maxpoints']);
            $locftst_state->setLocftestPercentage($row['locftest_percentage']);

            $locftst_data[$row['sugg_for_user']][] = $locftst_state;
        }

        return $locftst_data;
    }


    /**
     * @return string
     */
    protected static function getSQL(array $arr_usr_ids = array()): string
    {
        ilLearningObjectiveSuggestions::createTemporaryTableLearnObjectSugg($arr_usr_ids, 'tmp_lo_sugg');
        ilLearnObjectFinalTestStates::createTemporaryTableLearnObjectFinalTest($arr_usr_ids, 'tmp_lo_fin_test');

        $select = "select 	
					sugg_master_crs_title,
					sugg_master_crs_obj_id,
					sugg_objective_title,
					sugg_objective_id,
					sugg_for_user,
					locftest_master_crs_objective_id,
					locftest_usr_id,
					locftest_crs_obj_id,
					locftest_crs_title,
					locftest_objective_id,
					locftest_objective_title,
					0 as locftest_test_ref_id,
					0 as locftest_test_obj_id,
					0 as locftest_test_title,
					/*locftest_tries,
					locftest_points,
					locftest_maxpoints,*/
					locftest_percentage
					from tmp_lo_sugg
				   LEFT JOIN tmp_lo_fin_test on tmp_lo_fin_test.locftest_master_crs_objective_id = tmp_lo_sugg.sugg_objective_id 
				   and tmp_lo_fin_test.locftest_usr_id = tmp_lo_sugg.sugg_for_user";


        //echo $select;exit;

        return $select;
    }
}