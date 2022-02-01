<?php
require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\UserDefaults\UserSearch\usrdefObj;

class ilParticipationCertificateAccess {

	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var int
	 */
	protected $group_ref_id;
	/**
	 * @var ilAccessHandler
	 */
	protected $access;
	/**
	 * @var ilObjUser
	 */
	protected $usr;
	/**
	 * @var ilDB
	 */
	protected $db;


	/**
	 * ilParticipationCertificateAccess constructor.
	 *
	 * @param int $group_ref_id
	 */
	public function __construct($group_ref_id) {
		global $DIC;
		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->group_ref_id = $group_ref_id;
		$this->access = $DIC->access();
		$this->usr = $DIC->user();
		$this->db = $DIC->database();
	}


	public function hasCurrentUserWriteAccess() {
		if ($this->access->checkAccess("write", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}


	public function hasCurrentUserPrintAccess() {
		if ($this->hasCurrentUserWriteAccess()) {
			return true;
		}

		$enable_self_print = boolval(ilParticipationCertificateConfig::getConfig("enable_self_print", $this->group_ref_id));

		if ($enable_self_print) {
			$start = new DateTime(ilParticipationCertificateConfig::getConfig('self_print_start', $this->group_ref_id));
			$end = new DateTime(ilParticipationCertificateConfig::getConfig('self_print_end', $this->group_ref_id));
			$now = new DateTime();

			return ($now >= $start && $now <= $end);
		}

		return false;
	}


	public function hasCurrentUserReadAccess() {
		if ($this->access->checkAccess("read", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}


	public function hasCurrentUserSpecialAccess() {
		if ($this->access->checkAccess("read_learning_progress", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}


	public function getUserIdsOfGroup() {
		if ($this->hasCurrentUserWriteAccess() || $this->hasCurrentUserSpecialAccess()) {
			$select = "select obj_members.usr_id from obj_members
						inner join " . usrdefObj::TABLE_NAME . " as grp_obj on grp_obj.obj_id = obj_members.obj_id and grp_obj.type = 'grp'
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

?>