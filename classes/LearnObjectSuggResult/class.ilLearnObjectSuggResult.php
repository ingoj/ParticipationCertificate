<?php
class ilLearnObjectSuggResult {

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
	protected $points_as_percentage;
    /**
     *
     * @var string
     */
    protected $points_as_percentage_as_string;
    /**
     * @var int
     */
    protected $objective_as_percentage;
    /**
     * @var string
     */
    protected $objective_as_fraction_string;
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
    public function getPointsAsPercentage() : int
    {
        return $this->points_as_percentage;
    }


    /**
     * @param int $points_as_percentage
     */
    public function setPointsAsPercentage(int $points_as_percentage)
    {
        $this->points_as_percentage = $points_as_percentage;
    }


    /**
     * @return string
     */
    public function getPointsAsPercentageAsString() : string
    {
        return $this->points_as_percentage_as_string;
    }


    /**
     * @param string $points_as_percentage_as_string
     */
    public function setPointsAsPercentageAsString(string $points_as_percentage_as_string)
    {
        $this->points_as_percentage_as_string = $points_as_percentage_as_string;
    }


    /**
     * @return int
     */
    public function getObjectiveAsPercentage() : int
    {
        return $this->objective_as_percentage;
    }


    /**
     * @param int $objective_as_percentage
     */
    public function setObjectiveAsPercentage(int $objective_as_percentage)
    {
        $this->objective_as_percentage = $objective_as_percentage;
    }


    /**
     * @return int
     */
    public function getObjectiveAsFractionString() : int
    {
        return $this->objective_as_fraction_string;
    }


    /**
     * @param string $objective_as_fraction_string
     */
    public function setObjectiveAsFractionString(string $objective_as_fraction_string)
    {
        $this->objective_as_fraction_string = $objective_as_fraction_string;
    }


    public function getAveragePercentage($average_type = self::CALC_TYPE_BY_POINTS,$as_string = false) {

        switch($average_type) {
            case self::CALC_TYPE_BY_POINTS:
                if($as_string) {
                    return $this->points_as_percentage_as_string;
                }
                return $this->points_as_percentage;
                break;
            case self::CALC_TYPE_BY_COMPLETED_OBJECTIVE:
                if($as_string) {
                    return $this->objective_as_fraction_string;
                }
                return $this->objective_as_percentage;
                break;
            case self::CALC_TYPE_HIGHEST_VALUE:
                if($as_string) {
                    return $this->points_as_percentage > $this->objective_as_percentage ? $this->points_as_percentage_as_string : $this->objective_as_fraction_string;
                }
                return $this->points_as_percentage > $this->objective_as_percentage ? $this->points_as_percentage : $this->objective_as_percentage;
                break;
            default:
                if($as_string) {
                    return $this->points_as_percentage_as_string;
                }
                return $this->points_as_percentage;
                break;
        }
    }



}
?>