<?php

class ilCrsInitialTestState {

	/**
	 * @var int
	 */
	protected $crsitest_usr_id;
	/**
	 *
	 * @var int
	 */
	protected $crsitest_crs_obj_id;
	/**
	 *
	 * @var int
	 */
	protected $crsitest_crs_ref_id;
	/**
	 * @var string
	 */
	protected $crsitest_crs_title;
	/**
	 *
	 * @var int
	 */
	protected $crsitest_itest_ref_id;
	/**
	 *
	 * @var int
	 */
	protected $crsitest_itest_obj_id;
	/**
	 *
	 * @var string
	 */
	protected $crsitest_itest_title;
	/**
	*
	* @var int
	*/
	protected $crsitest_itest_tries;
	/**
	 *
	 * @var int
	 */
	protected $crsitest_itest_submitted;


	/**
	 * @return int
	 */
	public function getCrsitestUsrId() {
		return $this->crsitest_usr_id;
	}


	/**
	 * @param int $crsitest_usr_id
	 */
	public function setCrsitestUsrId($crsitest_usr_id) {
		$this->crsitest_usr_id = $crsitest_usr_id;
	}


	/**
	 * @return int
	 */
	public function getCrsitestCrsObjId() {
		return $this->crsitest_crs_obj_id;
	}


	/**
	 * @param int $crsitest_crs_obj_id
	 */
	public function setCrsitestCrsObjId($crsitest_crs_obj_id) {
		$this->crsitest_crs_obj_id = $crsitest_crs_obj_id;
	}


	/**
	 * @return int
	 */
	public function getCrsitestCrsRefId() {
		return $this->crsitest_crs_ref_id;
	}


	/**
	 * @param int $crsitest_crs_ref_id
	 */
	public function setCrsitestCrsRefId($crsitest_crs_ref_id) {
		$this->crsitest_crs_ref_id = $crsitest_crs_ref_id;
	}


	/**
	 * @return string
	 */
	public function getCrsitestCrsTitle() {
		return $this->crsitest_crs_title;
	}


	/**
	 * @param string $crsitest_crs_title
	 */
	public function setCrsitestCrsTitle($crsitest_crs_title) {
		$this->crsitest_crs_title = $crsitest_crs_title;
	}


	/**
	 * @return int
	 */
	public function getCrsitestItestRefId() {
		return $this->crsitest_itest_ref_id;
	}


	/**
	 * @param int $crsitest_itest_ref_id
	 */
	public function setCrsitestItestRefId($crsitest_itest_ref_id) {
		$this->crsitest_itest_ref_id = $crsitest_itest_ref_id;
	}


	/**
	 * @return int
	 */
	public function getCrsitestItestObjId() {
		return $this->crsitest_itest_obj_id;
	}


	/**
	 * @param int $crsitest_itest_obj_id
	 */
	public function setCrsitestItestObjId($crsitest_itest_obj_id) {
		$this->crsitest_itest_obj_id = $crsitest_itest_obj_id;
	}


	/**
	 * @return string
	 */
	public function getCrsitestItestTitle() {
		return $this->crsitest_itest_title;
	}


	/**
	 * @param string $crsitest_itest_title
	 */
	public function setCrsitestItestTitle($crsitest_itest_title) {
		$this->crsitest_itest_title = $crsitest_itest_title;
	}


	/**
	 * @return int
	 */
	public function getCrsitestItestTries() {
		return $this->crsitest_itest_tries;
	}


	/**
	 * @param int $crsitest_itest_tries
	 */
	public function setCrsitestItestTries($crsitest_itest_tries) {
		$this->crsitest_itest_tries = $crsitest_itest_tries;
	}


	/**
	 * @return int
	 */
	public function getCrsitestItestSubmitted() {
		return $this->crsitest_itest_submitted;
	}


	/**
	 * @param int $crsitest_itest_submitted
	 */
	public function setCrsitestItestSubmitted($crsitest_itest_submitted) {
		$this->crsitest_itest_submitted = $crsitest_itest_submitted;
	}
}
?>