<?php
class ilLearningObjectiveSuggestion {

	/**
	 * @var string
	 */
	protected $master_crs_title;
	/**
	 *
	 * @var int
	 */
	protected $master_crs_obj_id;
	/**
	 * @var string
	 */
	protected $sugg_objective_title;
	/**
	 *
	 * @var int
	 */
	protected $sugg_objective_id;
	/**
	 *
	 * @var int
	 */
	protected $sugg_for_user_id;


	/**
	 * @return string
	 */
	public function getMasterCrsTitle() {
		return $this->master_crs_title;
	}


	/**
	 * @param string $master_crs_title
	 */
	public function setMasterCrsTitle($master_crs_title) {
		$this->master_crs_title = $master_crs_title;
	}


	/**
	 * @return int
	 */
	public function getMasterCrsObjId() {
		return $this->master_crs_obj_id;
	}


	/**
	 * @param int $master_crs_obj_id
	 */
	public function setMasterCrsObjId($master_crs_obj_id) {
		$this->master_crs_obj_id = $master_crs_obj_id;
	}


	/**
	 * @return string
	 */
	public function getSuggObjectiveTitle() {
		return $this->sugg_objective_title;
	}


	/**
	 * @param string $sugg_objective_title
	 */
	public function setSuggObjectiveTitle($sugg_objective_title) {
		$this->sugg_objective_title = $sugg_objective_title;
	}


	/**
	 * @return int
	 */
	public function getSuggObjectiveId() {
		return $this->sugg_objective_id;
	}


	/**
	 * @param int $sugg_objective_id
	 */
	public function setSuggObjectiveId($sugg_objective_id) {
		$this->sugg_objective_id = $sugg_objective_id;
	}


	/**
	 * @return int
	 */
	public function getSuggForUserId() {
		return $this->sugg_for_user_id;
	}


	/**
	 * @param int $sugg_for_user_id
	 */
	public function setSuggForUserId($sugg_for_user_id) {
		$this->sugg_for_user_id = $sugg_for_user_id;
	}
}
?>