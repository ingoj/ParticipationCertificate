<?php
class ilExcerciseState {

	protected string $exc_obj_title;
	protected int $exc_obj_id;
	protected int $exc_ref_id;
	protected int $total;
	protected int $passed;
	protected int $passed_percentage;
	protected int $usr_id;

	public function getExcObjTitle(): string
    {
		return $this->exc_obj_title;
	}

	public function setExcObjTitle(string $exc_obj_title): void
    {
		$this->exc_obj_title = $exc_obj_title;
	}

	public function getExcObjId(): int
    {
		return $this->exc_obj_id;
	}

	public function setExcObjId(int $exc_obj_id): void
    {
		$this->exc_obj_id = $exc_obj_id;
	}

	public function getExcRefId(): int
    {
		return $this->exc_ref_id;
	}

	public function setExcRefId(int $exc_ref_id): void
    {
		$this->exc_ref_id = $exc_ref_id;
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