<?php
class ilLearningObjectiveSuggestion {
	protected string $master_crs_title;
	protected int $master_crs_obj_id;
	protected string $sugg_objective_title;
	protected int $sugg_objective_id;
	protected int $sugg_for_user_id;

	public function getMasterCrsTitle(): string
    {
		return $this->master_crs_title;
	}
	public function setMasterCrsTitle(string $master_crs_title): void
    {
		$this->master_crs_title = $master_crs_title;
	}
	public function getMasterCrsObjId(): int
    {
		return $this->master_crs_obj_id;
	}
	public function setMasterCrsObjId(int $master_crs_obj_id): void
    {
		$this->master_crs_obj_id = $master_crs_obj_id;
	}

	public function getSuggObjectiveTitle(): string
    {
		return $this->sugg_objective_title;
	}
	public function setSuggObjectiveTitle(string $sugg_objective_title): void
    {
		$this->sugg_objective_title = $sugg_objective_title;
	}
	public function getSuggObjectiveId(): int
    {
		return $this->sugg_objective_id;
	}
	public function setSuggObjectiveId(int $sugg_objective_id): void
    {
		$this->sugg_objective_id = $sugg_objective_id;
	}
	public function getSuggForUserId(): int
    {
		return $this->sugg_for_user_id;
	}
	public function setSuggForUserId(int $sugg_for_user_id): void
    {
		$this->sugg_for_user_id = $sugg_for_user_id;
	}
}