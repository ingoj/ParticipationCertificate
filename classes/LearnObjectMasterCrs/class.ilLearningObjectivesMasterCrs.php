<?php
use srag\Plugins\UserDefaults\UserSearch\usrdefObj;

class ilLearningObjectivesMasterCrs {

	/**
	 * @param array $arr_usr_ids
	 *
	 *
	 * @return ilLearningObjectiveMasterCrs[]
	 */
	public static function getData(array $arr_usr_ids = array()) {
		global $DIC;
		$ilDB = $DIC->database();
		$result = $ilDB->query(self::getSQL($arr_usr_ids));

		$lo_master_data = array();
		while ($row = $ilDB->fetchAssoc($result)) {
			$lo_master = new ilLearningObjectiveMasterCrs();
			$lo_master->setLoMasterObjectiveTitle($row['lo_master_objective_title']);
			$lo_master->setLoMasterUsrId($row['lo_master_usr_id']);

			$lo_master_data[$row['lo_master_usr_id']][] = $lo_master;
		}

		return $lo_master_data;
	}


	protected static function getSQL(array $arr_usr_ids = array()) {
		global $DIC;
		$ilDB = $DIC->database();
		$select = "SELECT 
					DISTINCT 
					crso.title as lo_master_objective_title,
					crs_memb.usr_id as lo_master_usr_id
					from crs_objectives as crso
					inner join " . usrdefObj::TABLE_NAME . " as crs_obj on crs_obj.obj_id = crso.crs_id
					inner join object_reference as crs_ref on crs_ref.obj_id = crs_obj.obj_id
					inner join loc_settings on loc_settings.obj_id = crs_obj.obj_id and loc_settings.itest > 0
					inner join obj_members as crs_memb on crs_memb.obj_id = crs_obj.obj_id
					WHERE " . $ilDB->in('crs_memb.usr_id', $arr_usr_ids, false, 'integer');

		return $select;
	}
}

?>