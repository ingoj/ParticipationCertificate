<?php
require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\UserDefaults\UserSearch\usrdefObj;

class ilParticipationCertificateAccess {
	protected ilParticipationCertificatePlugin $pl;
	protected int $group_ref_id;
	protected ilAccessHandler $access;
	protected ilObjUser $usr;
	protected ilDBInterface $db;


	/**
	 * ilParticipationCertificateAccess constructor.
	 */
	public function __construct(int $group_ref_id) {
		global $DIC;
		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->group_ref_id = $group_ref_id;
		$this->access = $DIC->access();
		$this->usr = $DIC->user();
		$this->db = $DIC->database();
	}


	public function hasCurrentUserWriteAccess(): bool
	{
		if ($this->access->checkAccess("write", "", $this->group_ref_id)) {
			return true;
		}
		if ($this->access->checkAccess("read_learning_progress", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}


	public function hasCurrentUserAdminAccess(): bool
	{
		if ($this->access->checkAccess("write", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}

	/**
	 * @throws Exception
	 */
	public function hasCurrentUserPrintAccess(): bool
	{
		if ($this->hasCurrentUserWriteAccess()) {
			return true;
		}
		// find users data for firstname and lastname, return false if length = 0 for at least one
		$usrdata = new ilPartCertUsersData;
		$curr_user[] = $this->usr->getId();
		$first = $usrdata->getData($curr_user)[$curr_user[0]]->getPartCertFirstname();
		$last = $usrdata->getData($curr_user)[$curr_user[0]]->getPartCertLastname();

		if (strlen($first)*strlen($last) == 0) {
			return false;
		}	
		// if user has data, check if selfprint is active (changed to a new function)
		return ($this->isSelfPrintEnabled());
	}

	/**
	 * @throws Exception
	 */
	public function isSelfPrintEnabled(): bool
	{
		// formerly hasCurrentUserPrintAccess
		$enable_self_print = boolval(ilParticipationCertificateConfig::getConfig("enable_self_print", $this->group_ref_id));

		if ($enable_self_print) {
			$start = new DateTime(ilParticipationCertificateConfig::getConfig('self_print_start', $this->group_ref_id));
			$end = new DateTime(ilParticipationCertificateConfig::getConfig('self_print_end', $this->group_ref_id));
			$now = new DateTime();

			return ($now >= $start && $now <= $end);
		}

		return false;
	}


	public function hasCurrentUserReadAccess(): bool
	{
		if ($this->access->checkAccess("read", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}


	public function hasCurrentUserSpecialAccess(): bool
	{
		if ($this->access->checkAccess("read_learning_progress", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}


	public function getUserIdsOfGroup(): array
	{
		if ($this->hasCurrentUserWriteAccess() || $this->hasCurrentUserSpecialAccess()) {
			$objecttype = ilObject::_lookupType($this->group_ref_id, true);
			if ($objecttype != 'grp' and $objecttype != 'crs') {
				$objecttype = 'grp';
			}
			$select = "select obj_members.usr_id from obj_members
						inner join " . usrdefObj::TABLE_NAME . " as grp_obj on grp_obj.obj_id = obj_members.obj_id and grp_obj.type = '". $objecttype ."'
						inner join object_reference as grp_ref on grp_ref.obj_id = obj_members.obj_id
						where grp_ref.ref_id = " . $this->db->quote($this->group_ref_id, "integer") . " and obj_members.member = 1";
			$result = $this->db->query($select);
			$usr_data = array();
			while ($row = $this->db->fetchAssoc($result)) {
				$usr_data[] = $row['usr_id'];
			}

			return $usr_data;
		} elseif ($this->hasCurrentUserReadAccess()) {
			$usr_data = array();
			$usr_data[] = $this->usr->getId();

			return $usr_data;
		}

		return array();
	}
}