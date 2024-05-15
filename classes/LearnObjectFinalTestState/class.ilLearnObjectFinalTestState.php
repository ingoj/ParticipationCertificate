<?php
class ilLearnObjectFinalTestState {
        protected ?string $locftest_learn_objective_title;
	protected ?int $locftest_usr_id;
	protected ?int $locftest_crs_obj_id = null;
	protected ?int $locftest_master_crs_id = null;
	protected ?string $locftest_master_crs_title;
	protected ?string $locftest_crs_title;
	protected ?int $locftest_objective_id = null;
	protected ?string $locftest_objective_title;
	protected ?int $locftest_master_objective_id = null;
	protected ?int $locftest_test_ref_id = null;
	protected ?int $locftest_test_obj_id = null;
	protected ?string $locftest_test_title;
	protected ?int $locftest_percentage = null;
	protected ?int $locftest_qpls_required_percentage = null;
	protected bool $objectives_all_completed;
	protected bool $objectives_sug_completed;
	protected bool $objectives_suggested;

    public function getLocftestMasterObjectiveId(): ?int
    {
	return $this->locftest_master_objective_id;
    }

    public function setLocftestMasterObjectiveId ( ?int $locftest_master_objective_id ): void
    {
    	$this->locftest_master_objective_id = $locftest_master_objective_id;
    }

    public function getLocftestLearnObjectiveTitle() : ?string
    {
        return $this->locftest_learn_objective_title;
    }

    public function setLocftestLearnObjectiveTitle(?string $locftest_learn_objective_title): void
    {
        $this->locftest_learn_objective_title = $locftest_learn_objective_title;
    }



	public function getLocftestUsrId(): ?int
    {
		return $this->locftest_usr_id;
	}

	public function setLocftestUsrId(?int $locftest_usr_id): void
    {
		$this->locftest_usr_id = $locftest_usr_id;
	}

	public function getLocftestCrsObjId(): ?int
    {
		return $this->locftest_crs_obj_id;
	}

	public function setLocftestCrsObjId(?int $locftest_crs_obj_id): void
    {
		$this->locftest_crs_obj_id = $locftest_crs_obj_id;
	}

	public function getLocftestCrsTitle(): ?string
    {
		return $this->locftest_crs_title;
	}

	public function setLocftestCrsTitle(?string $locftest_crs_title): void
    {
		$this->locftest_crs_title = $locftest_crs_title;
	}

	public function getLocftestObjectiveId(): ?int
    {
		return $this->locftest_objective_id;
	}

	public function setLocftestObjectiveId(?int $locftest_objective_id): void
    {
		$this->locftest_objective_id = $locftest_objective_id;
	}

	public function getLocftestObjectiveTitle(): ?string
    {
		return $this->locftest_objective_title;
	}

	public function setLocftestObjectiveTitle(?string $locftest_objective_title): void
    {
		$this->locftest_objective_title = $locftest_objective_title;
	}

	public function getLocftestMasterCrsId(): ?int
		{
			return $this->locftest_master_crs_id;
		}

	public function setLocftestMasterCrsId(?int $locftest_master_crs_id): void
		{
			$this->locftest_master_crs_id = $locftest_master_crs_id);
		}

	public function getLocftestMasterCrsTitle() : ?string
		{
			return $this->locftest_master_crs_title;
		}

	public function setLocftestMasterCrsTitle(?string $locftest_master_crs_title): void
		{
			$this->locftest_master_crs_title = $locftest_master_crs_title;
		}
	public function getLocftestTestRefId(): ?int
    		{
			return $this->locftest_test_ref_id;
		}

	public function setLocftestTestRefId(?int $locftest_test_ref_id): void
    		{
			$this->locftest_test_ref_id = $locftest_test_ref_id;
		}

	public function getLocftestTestObjId(): ?int
    {
		return $this->locftest_test_obj_id;
	}

	public function setLocftestTestObjId(?int $locftest_test_obj_id): void
    {
		$this->locftest_test_obj_id = $locftest_test_obj_id;
	}

	public function getLocftestTestTitle(): ?string
    {
		return $this->locftest_test_title;
	}

	public function setLocftestTestTitle(?string $locftest_test_title): void
    {
		$this->locftest_test_title = $locftest_test_title;
	}

	public function getLocftestPercentage(): ?int
    {
		return $this->locftest_percentage;
	}

	public function setLocftestPercentage(?int $locftest_percentage): void
    {
		$this->locftest_percentage = $locftest_percentage;
	}

	public function getLocftestQplsRequiredPercentage(): ?int
    {
		return $this->locftest_qpls_required_percentage;
	}

	public function setLocftestQplsRequiredPercentage(?int $locftest_qpls_required_percentage): void
    {
		$this->locftest_qpls_required_percentage = $locftest_qpls_required_percentage;
	}

	public function isObjectivesAllCompleted(): bool
    {
		return $this->objectives_all_completed;
	}

	public function setObjectivesAllCompleted(bool $objectives_all_completed): void
    {
		$this->objectives_all_completed = $objectives_all_completed;
	}

	public function isObjectivesSugCompleted(): bool
    {
		return $this->objectives_sug_completed;
	}

	public function setObjectivesSugCompleted(bool $objectives_sug_completed): void
    {
		$this->objectives_sug_completed = $objectives_sug_completed;
	}

	public function getObjectivesSuggested(): bool
    {
		return $this->objectives_suggested;
	}

	public function setObjectivesSuggested(bool $objectives_suggested): void
    {
		$this->objectives_suggested = $objectives_suggested;
	}
}
