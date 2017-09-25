<?php
class ilExcerciseState {

	/**
	 * @var string
	 */
	protected $exc_obj_title;
	/**
	 *
	 * @var int
	 */
	protected $exc_obj_id;
	/**
	 * @var int
	 */
	protected $exc_ref_id;
	/**
	 *
	 * @var int
	 */
	protected $total;
	/**
	 *
	 * @var int
	 */
	protected $passed;
	/**
	 *
	 * @var int
	 */
	protected $passed_percentage;
	/**
	*
	* @var int
	*/
	protected $usr_id;


	/**
	 * @return string
	 */
	public function getExcObjTitle() {
		return $this->exc_obj_title;
	}


	/**
	 * @param string $exc_obj_title
	 */
	public function setExcObjTitle($exc_obj_title) {
		$this->exc_obj_title = $exc_obj_title;
	}


	/**
	 * @return int
	 */
	public function getExcObjId() {
		return $this->exc_obj_id;
	}


	/**
	 * @param int $exc_obj_id
	 */
	public function setExcObjId($exc_obj_id) {
		$this->exc_obj_id = $exc_obj_id;
	}


	/**
	 * @return int
	 */
	public function getExcRefId() {
		return $this->exc_ref_id;
	}


	/**
	 * @param int $exc_ref_id
	 */
	public function setExcRefId($exc_ref_id) {
		$this->exc_ref_id = $exc_ref_id;
	}


	/**
	 * @return int
	 */
	public function getTotal() {
		return $this->total;
	}


	/**
	 * @param int $total
	 */
	public function setTotal($total) {
		$this->total = $total;
	}


	/**
	 * @return int
	 */
	public function getPassed() {
		return $this->passed;
	}


	/**
	 * @param int $passed
	 */
	public function setPassed($passed) {
		$this->passed = $passed;
	}


	/**
	 * @return int
	 */
	public function getPassedPercentage() {
		return $this->passed_percentage;
	}


	/**
	 * @param int $passed_percentage
	 */
	public function setPassedPercentage($passed_percentage) {
		$this->passed_percentage = $passed_percentage;
	}


	/**
	 * @return int
	 */
	public function getUsrId() {
		return $this->usr_id;
	}


	/**
	 * @param int $usr_id
	 */
	public function setUsrId($usr_id) {
		$this->usr_id = $usr_id;
	}
}
?>