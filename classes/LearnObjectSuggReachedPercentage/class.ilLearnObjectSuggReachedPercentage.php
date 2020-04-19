<?php
class ilLearnObjectSuggReachedPercentage {

    const CALC_TYPE_BY_POINTS = 1;
    const CALC_TYPE_BY_COMPLETED_OBJECTIVE = 2;
    const CALC_TYPE_HIGHEST_VALUE = 3;

	/**
	 * @var int
	 */
	protected $usr_id;
	/**
	 *
	 * @var int
	 */
	protected $points_average_percentage;
    /**
     * @var int
     */
    protected $objective_average_percentage;
	/**
	 * @var int $limit_percentage;
	 */
	protected $limit_percentage;



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


	/**
	 * @return int
	 */
	public function getPointsAveragePercentage() {
		return $this->points_average_percentage;
	}


	/**
	 * @param int $points_average_percentage
	 */
	public function setPointsAveragePercentage($points_average_percentage) {
		$this->points_average_percentage = $points_average_percentage;
	}


	/**
	 * @return int
	 */
	public function getLimitPercentage() {
		return $this->limit_percentage;
	}


	/**
	 * @param int $limit_percentage
	 */
	public function setLimitPercentage($limit_percentage) {
		$this->limit_percentage = $limit_percentage;
	}


    /**
     * @return int
     */
    public function getObjectiveAveragePercentage() : int
    {
        return $this->objective_average_percentage;
    }


    /**
     * @param int $objective_average_percentage
     */
    public function setObjectiveAveragePercentage(int $objective_average_percentage)
    {
        $this->objective_average_percentage = $objective_average_percentage;
    }

    public function getAveragePercentage($average_type = self::CALC_TYPE_BY_POINTS) {

        switch($average_type) {
            case self::CALC_TYPE_BY_POINTS:
                return $this->points_average_percentage;
                break;
            case self::CALC_TYPE_BY_COMPLETED_OBJECTIVE:
                return $this->objective_average_percentage;
                break;
            case self::CALC_TYPE_HIGHEST_VALUE:
                return $this->points_average_percentage > $this->objective_average_percentage ? $this->points_average_percentage : $this->objective_average_percentage;
                break;
            default:
                return $this->points_average_percentage;
                break;
        }
    }



}
?>