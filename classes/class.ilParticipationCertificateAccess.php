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
	public function hasCurrentUserReadAccess(){
		global $ilAccess;

		if($ilAccess->checkAccess("read","",$this->group_ref_id)){
			return true;
		}
		return false;
	}

	public function hasCurrentUserSpecialAccess(){
		global $ilAccess,$ilUser;

		if($ilAccess->checkAccess("read_learning_progress","",$this->group_ref_id)){
			return true;
		}
		return false;
	}



	public function getUserIdsOfGroup() {
		global $ilUser;
		if($this->hasCurrentUserPrintAccess() || $this->hasCurrentUserSpecialAccess()) {
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
		elseif ($this->hasCurrentUserReadAccess())
		{
			$usr_data = array();
			$usr_data[] = $ilUser->getId();
			return $usr_data;
		}



		return array();
	}

}
?>