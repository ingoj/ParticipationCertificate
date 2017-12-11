<?php


class TestMark{

	/**
	 * @var int
	 */
	private $mark_id;
	/**
	 * @var int
	 */
	private $passed;
	/**
	 * @var double
	 */
	private $minimumlvl;


	/**
	 * @return mixed
	 */
	public function getMarkId() {
		return $this->mark_id;
	}


	/**
	 * @param mixed $mark_id
	 */
	public function setMarkId($mark_id) {
		$this->mark_id = $mark_id;
	}


	/**
	 * @return mixed
	 */
	public function getPassed() {
		return $this->passed;
	}


	/**
	 * @param mixed $passed
	 */
	public function setPassed($passed) {
		$this->passed = $passed;
	}


	/**
	 * @return mixed
	 */
	public function getMinimumlvl() {
		return $this->minimumlvl;
	}


	/**
	 * @param mixed $minimumlvl
	 */
	public function setMinimumlvl($minimumlvl) {
		$this->minimumlvl = $minimumlvl;
	}




}