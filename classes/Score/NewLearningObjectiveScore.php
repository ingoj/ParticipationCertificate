<?php

class NewLearningObjectiveScore {

	protected $course_obj_id;
	protected $user_id;
	protected $score;
	protected $objectiveId;
	protected $title;


	/**
	 * @return mixed
	 */
	public function getCourseObjId() {
		return $this->course_obj_id;
	}


	/**
	 * @param mixed $course_obj_id
	 */
	public function setCourseObjId($course_obj_id) {
		$this->course_obj_id = $course_obj_id;
	}


	/**
	 * @return mixed
	 */
	public function getUserId() {
		return $this->user_id;
	}


	/**
	 * @param mixed $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}


	/**
	 * @return mixed
	 */
	public function getScore() {
		return $this->score;
	}


	/**
	 * @param mixed $score
	 */
	public function setScore($score) {
		$this->score = $score;
	}


	/**
	 * @return mixed
	 */
	public function getObjectiveId() {
		return $this->objectiveId;
	}


	/**
	 * @param mixed $objectiveId
	 */
	public function setObjectiveId($objectiveId) {
		$this->objectiveId = $objectiveId;
	}


	/**
	 * @return mixed
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param mixed $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
}