<?php
class ilIassState {

	protected string $iass_obj_title;
	protected int $iass_obj_id;
    protected int $iass_ref_id;
	protected int $total;
	protected int $passed;
	protected int $passed_percentage;
	protected int $usr_id;

	public function getIassObjTitle(): string
    {
		return $this->iass_obj_title;
	}

	public function setIassObjTitle(string $iass_obj_title): void
    {
		$this->iass_obj_title = $iass_obj_title;
	}

	public function getIassObjId(): int
    {
		return $this->iass_obj_id;
	}

	public function setIassObjId(int $iass_obj_id): void
    {
		$this->iass_obj_id = $iass_obj_id;
	}

	public function getIassRefId(): int {
		return $this->iass_ref_id;
	}

	public function setIassRefId(int $iass_ref_id): void
    {
		$this->iass_ref_id = $iass_ref_id;
	}

	public function getTotal(): int
    {
		return $this->total;
	}

	public function setTotal(int $total): void
    {
		$this->total = $total;
	}

	public function getPassed(): int
    {
		return $this->passed;
	}

	public function setPassed(int $passed): void
    {
		$this->passed = $passed;
	}

	public function getPassedPercentage(): int
    {
		return $this->passed_percentage;
	}

	public function setPassedPercentage(int $passed_percentage): void
    {
		$this->passed_percentage = $passed_percentage;
	}

	public function getUsrId(): int
    {
		return $this->usr_id;
	}

	public function setUsrId(int $usr_id): void
    {
		$this->usr_id = $usr_id;
	}
}