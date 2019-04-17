<?php

class ilLearnObjectFinalTestState {
	/**
	 * @var int
	 */
	protected $locftest_usr_id;
	/**
	 *
	 * @var int
	 */
	protected $locftest_crs_obj_id;
	/**
	 * @var string
	 */
	protected $locftest_crs_title;
	/**
	 *
	 * @var int
	 */
	protected $locftest_objective_id;
	/**
	 *
	 * @var string
	 */
	protected $locftest_objective_title;
	/**
	 *
	 * @var int
	 */
	protected $locftest_test_ref_id;
	/**
	*
	* @var int
	*/
	protected $locftest_test_obj_id;
	/**
	 *
	 * @var string
	 */
	protected $locftest_test_title;
	/**
	 *
	 * @var int
	 */
	protected $locftest_percentage;


	/**
	 * @return int
	 */
	public function getLocftestUsrId() {
		return $this->locftest_usr_id;
	}


	/**
	 * @param int $locftest_usr_id
	 */
	public function setLocftestUsrId($locftest_usr_id) {
		$this->locftest_usr_id = $locftest_usr_id;
	}

	/**
	 * @return int
	 */
	public function getLocftestCrsObjId() {
		return $this->locftest_crs_obj_id;
	}


	/**
	 * @param int $locftest_crs_obj_id
	 */
	public function setLocftestCrsObjId($locftest_crs_obj_id) {
		$this->locftest_crs_obj_id = $locftest_crs_obj_id;
	}


	/**
	 * @return string
	 */
	public function getLocftestCrsTitle() {
		return $this->locftest_crs_title;
	}


	/**
	 * @param string $locftest_crs_title
	 */
	public function setLocftestCrsTitle($locftest_crs_title) {
		$this->locftest_crs_title = $locftest_crs_title;
	}


	/**
	 * @return int
	 */
	public function getLocftestObjectiveId() {
		return $this->locftest_objective_id;
	}


	/**
	 * @param int $locftest_objective_id
	 */
	public function setLocftestObjectiveId($locftest_objective_id) {
		$this->locftest_objective_id = $locftest_objective_id;
	}


	/**
	 * @return string
	 */
	public function getLocftestObjectiveTitle() {
		return $this->locftest_objective_title;
	}


	/**
	 * @param string $locftest_objective_title
	 */
	public function setLocftestObjectiveTitle($locftest_objective_title) {
		$this->locftest_objective_title = $locftest_objective_title;
	}


	/**
	 * @return int
	 */
	public function getLocftestTestRefId() {
		return $this->locftest_test_ref_id;
	}


	/**
	 * @param int $locftest_test_ref_id
	 */
	public function setLocftestTestRefId($locftest_test_ref_id) {
		$this->locftest_test_ref_id = $locftest_test_ref_id;
	}


	/**
	 * @return int
	 */
	public function getLocftestTestObjId() {
		return $this->locftest_test_obj_id;
	}


	/**
	 * @param int $locftest_test_obj_id
	 */
	public function setLocftestTestObjId($locftest_test_obj_id) {
		$this->locftest_test_obj_id = $locftest_test_obj_id;
	}


	/**
	 * @return string
	 */
	public function getLocftestTestTitle() {
		return $this->locftest_test_title;
	}


	/**
	 * @param string $locftest_test_title
	 */
	public function setLocftestTestTitle($locftest_test_title) {
		$this->locftest_test_title = $locftest_test_title;
	}

	/**
	 * @return int
	 */
	public function getLocftestPercentage() {
		return $this->locftest_percentage;
	}


	/**
	 * @param int $locftest_percentage
	 */
	public function setLocftestPercentage($locftest_percentage) {
		$this->locftest_percentage = $locftest_percentage;
	}
}
?>