<?php
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Learningsugg/class.getLearnSugg.php";

class getLearnSuggs {

	/**
	 * @param array $arr_usr_ids
	 *
	 * @return ilLearningObjectivesMasterCrs[]
	 */
	public static function getData($usr_id) {

		global $ilDB;

		$result = $ilDB->query(self::getSQL($usr_id));
		$learn_sugg = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$suggs = new getLearnSugg();
			$suggs->setSuggObjectiveId($row['sugg_objective_id']);
			$suggs->setSuggForUser($row['sugg_for_user']);
			$suggs->setSuggObjTitle($row['sugg_objective_title']);

			$learn_sugg[] = $suggs;
		}
			return $learn_sugg;
	}

		protected
		static function getSQL($usr_id) {
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
	                where sugg.user_id =" . $ilDB->quote($usr_id, "integer");

			return $select;
		}
}
?>