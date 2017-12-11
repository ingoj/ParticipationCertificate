<?php

class getLearnSugg {
	private $sugg_obj_title;

	private $sugg_objective_id;

	private $sugg_for_user;


	/**
	 * @return mixed
	 */
	public function getSuggObjTitle() {
		return $this->sugg_obj_title;
	}


	/**
	 * @param mixed $sugg_obj_title
	 */
	public function setSuggObjTitle($sugg_obj_title) {
		$this->sugg_obj_title = $sugg_obj_title;
	}


	/**
	 * @return mixed
	 */
	public function getSuggObjectiveId() {
		return $this->sugg_objective_id;
	}


	/**
	 * @param mixed $sugg_objective_id
	 */
	public function setSuggObjectiveId($sugg_objective_id) {
		$this->sugg_objective_id = $sugg_objective_id;
	}


	/**
	 * @return mixed
	 */
	public function getSuggForUser() {
		return $this->sugg_for_user;
	}


	/**
	 * @param mixed $sugg_for_user
	 */
	public function setSuggForUser($sugg_for_user) {
		$this->sugg_for_user = $sugg_for_user;
	}




}