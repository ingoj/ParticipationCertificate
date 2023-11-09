<?php
class xaliState {
    protected int $total;
    protected int $passed;
    protected int $usr_id;

    public function getTotal(): int
    {
        return $this->total;
    }
    public function setTotal(int $total): void
    {
        $this->total = $total;
    }
    public function getPassed(): int
    {
        return $this->passed;
    }
    public function setPassed(int $passed): void
    {
        $this->passed = $passed;
    }
    public function getPassedPercentage(): int
    {
        return $this->passed_percentage;
    }
    public function setPassedPercentage(int $passed_percentage): void
    {
        $this->passed_percentage = $passed_percentage;
    }
    public function getUsrId(): int
    {
        return $this->usr_id;
    }
    public function setUsrId(int $usr_id): void
    {
        $this->usr_id = $usr_id;
    }
}