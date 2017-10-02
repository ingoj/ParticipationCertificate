<?php

class ilParticipationCertificateAccess {

	/**
	 * ilParticipationCertificateAccess constructor.
	 *
	 * @param int $group_ref_id
	 */
	public function __construct($group_ref_id) {
		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->group_ref_id = $group_ref_id;
	}


	public function hasCurrentUserPrintAccess() {
		global $ilAccess, $ilCtrl, $lng;

		if ($ilAccess->checkAccess("write", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}

	public function getUserIdsOfGroup() {
		if($this->hasCurrentUserPrintAccess()) {
			global $ilDB;

			$select = "select obj_members.usr_id from obj_members
						inner join object_data as grp_obj on grp_obj.obj_id = obj_members.obj_id and grp_obj.type = 'grp'
						inner join object_reference as grp_ref on grp_ref.obj_id = obj_members.obj_id
						where grp_ref.ref_id = ".$ilDB->quote($this->group_ref_id, "integer")." and obj_members.member = 1";

			$result = $ilDB->query($select);
			$usr_data = array();
			while ($row = $ilDB->fetchAssoc($result)) {
				$usr_data[] = $row['usr_id'];
			}
			return $usr_data;
		}

		return array();
	}
}