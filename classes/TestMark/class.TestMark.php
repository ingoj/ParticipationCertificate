<?php
class TestMark{
	private int $mark_id;
	private bool $passed;
	private float $minimumlvl;
	public function getMarkId(): int {
		return $this->mark_id;
	}
	public function setMarkId(int $mark_id): void
    {
		$this->mark_id = $mark_id;
	}
	public function getPassed(): bool {
		return $this->passed;
	}
	public function setPassed(bool $passed) {
		$this->passed = $passed;
	}
	public function getMinimumlvl(): float {
		return $this->minimumlvl;
	}
	public function setMinimumlvl(float $minimumlvl) {
		$this->minimumlvl = $minimumlvl;
	}
}