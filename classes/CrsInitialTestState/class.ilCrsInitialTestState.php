<?php

class ilCrsInitialTestState {
	protected int $crsitest_usr_id;
	protected int $crsitest_crs_obj_id;
	protected int $crsitest_crs_ref_id;
	protected string $crsitest_crs_title;
	protected int $crsitest_itest_ref_id;
	protected int $crsitest_itest_obj_id;
	protected string $crsitest_itest_title;
	protected int $crsitest_itest_tries;
	protected int $crsitest_itest_submitted;

	public function getCrsitestUsrId(): int
    {
		return $this->crsitest_usr_id;
	}

	public function setCrsitestUsrId(int $crsitest_usr_id): void
    {
		$this->crsitest_usr_id = $crsitest_usr_id;
	}

	public function getCrsitestCrsObjId(): int
    {
		return $this->crsitest_crs_obj_id;
	}

	public function setCrsitestCrsObjId(int $crsitest_crs_obj_id): void
    {
		$this->crsitest_crs_obj_id = $crsitest_crs_obj_id;
	}

	public function getCrsitestCrsRefId():int {
		return $this->crsitest_crs_ref_id;
	}

	public function setCrsitestCrsRefId(int $crsitest_crs_ref_id): void
    {
		$this->crsitest_crs_ref_id = $crsitest_crs_ref_id;
	}

	public function getCrsitestCrsTitle(): string
    {
		return $this->crsitest_crs_title;
	}

	public function setCrsitestCrsTitle(string $crsitest_crs_title): void
    {
		$this->crsitest_crs_title = $crsitest_crs_title;
	}

	public function getCrsitestItestRefId(): int
    {
		return $this->crsitest_itest_ref_id;
	}

	public function setCrsitestItestRefId(int $crsitest_itest_ref_id): void
    {
		$this->crsitest_itest_ref_id = $crsitest_itest_ref_id;
	}

	public function getCrsitestItestObjId(): int
    {
		return $this->crsitest_itest_obj_id;
	}

	public function setCrsitestItestObjId(int $crsitest_itest_obj_id): void
    {
		$this->crsitest_itest_obj_id = $crsitest_itest_obj_id;
	}

	public function getCrsitestItestTitle(): string
    {
		return $this->crsitest_itest_title;
	}

	public function setCrsitestItestTitle(string $crsitest_itest_title): void
    {
		$this->crsitest_itest_title = $crsitest_itest_title;
	}

	public function getCrsitestItestTries(): int
    {
		return $this->crsitest_itest_tries;
	}

	public function setCrsitestItestTries(int $crsitest_itest_tries): void
    {
		$this->crsitest_itest_tries = $crsitest_itest_tries;
	}

	public function getCrsitestItestSubmitted(): int
    {
		return $this->crsitest_itest_submitted;
	}

	public function setCrsitestItestSubmitted(int $crsitest_itest_submitted): void
    {
		$this->crsitest_itest_submitted = $crsitest_itest_submitted;
	}
}
?>