<?php
class ilLearningObjectiveMasterCrs {

	/**
	 * @var string
	 */
	protected $lo_master_objective_title;
	/**
	 *
	 * @var int
	 */
	protected $lo_master_usr_id;



	/**
	 * @return string
	 */
	public function getLoMasterObjectiveTitle() {
		return $this->lo_master_objective_title;
	}


	/**
	 * @param string $lo_master_objective_title
	 */
	public function setLoMasterObjectiveTitle($lo_master_objective_title) {
		$this->lo_master_objective_title = $lo_master_objective_title;
	}

	/**
	 * @return int
	 */
	public function getLoMasterUsrId() {
		return $this->lo_master_usr_id;
	}


	/**
	 * @param int $lo_master_usr_id
	 */
	public function setLoMasterUsrId($lo_master_usr_id) {
		$this->lo_master_usr_id = $lo_master_usr_id;
	}
}
?>