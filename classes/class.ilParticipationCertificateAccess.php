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

		if($ilAccess->checkAccess("write", "", $this->group_ref_id)) {
			return true;
		}

		return false;
	}

}