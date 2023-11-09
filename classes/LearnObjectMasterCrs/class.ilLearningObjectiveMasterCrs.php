<?php
class ilLearningObjectiveMasterCrs {
	protected string $lo_master_objective_title;
	protected int $lo_master_usr_id;

	public function getLoMasterObjectiveTitle(): string
    {
		return $this->lo_master_objective_title;
	}

	public function setLoMasterObjectiveTitle(string $lo_master_objective_title): void
    {
		$this->lo_master_objective_title = $lo_master_objective_title;
	}

	public function getLoMasterUsrId(): int
    {
		return $this->lo_master_usr_id;
	}

	public function setLoMasterUsrId(int $lo_master_usr_id): void
    {
		$this->lo_master_usr_id = $lo_master_usr_id;
	}
}