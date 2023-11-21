<?php
class ilParticipationCertificateMultipleResultTableGUI extends ilParticipationCertificateSingleResultTableGUI {

	/**
	 * @param int[] $usr_ids
	 */
	public function __construct(ilParticipationCertificateMultipleResultGUI $a_parent_obj, string $a_parent_cmd, int $usr_id, array $usr_ids) {
		parent::__construct($a_parent_obj, $a_parent_cmd, $usr_id);

		// Selected users
		foreach ($usr_ids as $usr_id2) {
			$this->addHiddenInput('record_ids[]', $usr_id2);
		}
	}
}
