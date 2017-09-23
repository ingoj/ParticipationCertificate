<?php
require_once "class.ilLearningObjectiveSuggestion.php";

class ilLearningObjectiveSuggestions {

	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilLearningObjectivesMasterCrs[]
	 */
	public static function getData(array $arr_usr_ids = array()) {

		global $ilDB;

		$result = $ilDB->query(self::getSQL($arr_usr_ids));
		$sug_data = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$l_o_sugg = new ilLearningObjectivesMasterCrs();
			$l_o_sugg->setMasterCrsTitle($row['sugg_master_crs_title']);
			$l_o_sugg->setMasterCrsObjId($row['sugg_master_crs_obj_id']);
			$l_o_sugg->setSuggObjectiveTitle($row['sugg_objective_title']);
			$l_o_sugg->setSuggObjectiveId($row['sugg_objective_id']);
			$l_o_sugg->setSuggForUserId($row['sugg_for_user']);

			$sug_data[$row['sugg_for_user']] = $l_o_sugg;
		}

		return $sug_data;
	}

	protected static function getSQL(array $arr_usr_ids = array()) {
		global $ilDB;

		$select = "SELECT 
					crs_obj.title as sugg_master_crs_title,
					crs_obj.obj_id as sugg_master_crs_obj_id,
					crso.title as sugg_objective_title,
					sugg.objective_id as sugg_objective_id,
					sugg.user_id as sugg_for_user
					FROM alo_suggestion as sugg
					inner join crs_objectives as crso on crso.crs_id = sugg.course_obj_id and crso.objective_id = sugg.objective_id
					inner join object_data as crs_obj on crs_obj.obj_id = crso.crs_id
	                where ".$ilDB->in('sugg.user_id', $arr_usr_ids, false, 'integer');

		return $select;
	}


	/**
	 * @param array  $arr_usr_ids
	 * @param string $table_name
	 */
	public static function createTemporaryTableLearnObjectSugg(array $arr_usr_ids = array(),$table_name = 'tmp_lo_sugg') {
		global $ilDB;

		$sql = "DROP TABLE IF Exists $table_name";
		$ilDB->query($sql);

		$sql = "CREATE Temporary Table $table_name (".self::getSql($arr_usr_ids).")";

		$ilDB->query($sql);
	}
}
?>