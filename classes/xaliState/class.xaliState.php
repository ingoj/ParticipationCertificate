<?php
class xaliState {

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
    protected $usr_id;



    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }


    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }


    /**
     * @return int
     */
    public function getPassed()
    {
        return $this->passed;
    }


    /**
     * @param int $passed
     */
    public function setPassed($passed)
    {
        $this->passed = $passed;
    }


    /**
     * @return int
     */
    public function getPassedPercentage()
    {
        return $this->passed_percentage;
    }


    /**
     * @param int $passed_percentage
     */
    public function setPassedPercentage($passed_percentage)
    {
        $this->passed_percentage = $passed_percentage;
    }


    /**
     * @return int
     */
    public function getUsrId()
    {
        return $this->usr_id;
    }


    /**
     * @param int $usr_id
     */
    public function setUsrId($usr_id)
    {
        $this->usr_id = $usr_id;
    }
}
?>