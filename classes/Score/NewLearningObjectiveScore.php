<?php

class NewLearningObjectiveScore
{

    protected int $course_obj_id;
    protected int $user_id;
    protected int $score;
    protected int $objectiveId;
    protected string $title;

    public function getCourseObjId(): int
    {
        return $this->course_obj_id;
    }

    public function setCourseObjId(int $course_obj_id): void
    {
        $this->course_obj_id = $course_obj_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getObjectiveId(): int
    {
        return $this->objectiveId;
    }

    public function setObjectiveId(int $objectiveId): void
    {
        $this->objectiveId = $objectiveId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}