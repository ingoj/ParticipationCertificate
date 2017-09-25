<?php
class ilIassState {

	/**
	 * @var string
	 */
	protected $iass_obj_title;
	/**
	 *
	 * @var int
	 */
	protected $iass_obj_id;
	/**
	 * @var int
	 */
	protected $iass_ref_id;
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
	public function getIassObjTitle() {
		return $this->iass_obj_title;
	}


	/**
	 * @param string $iass_obj_title
	 */
	public function setIassObjTitle($iass_obj_title) {
		$this->iass_obj_title = $iass_obj_title;
	}


	/**
	 * @return int
	 */
	public function getIassObjId() {
		return $this->iass_obj_id;
	}


	/**
	 * @param int $iass_obj_id
	 */
	public function setIassObjId($iass_obj_id) {
		$this->iass_obj_id = $iass_obj_id;
	}


	/**
	 * @return int
	 */
	public function getIassRefId() {
		return $this->iass_ref_id;
	}


	/**
	 * @param int $iass_ref_id
	 */
	public function setIassRefId($iass_ref_id) {
		$this->iass_ref_id = $iass_ref_id;
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