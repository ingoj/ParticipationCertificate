<?php

class getLearnSugg
{
    private string $sugg_obj_title;

    private int $sugg_objective_id;

    private int $sugg_for_user;


    public function getSuggObjTitle(): string
    {
        return $this->sugg_obj_title;
    }

    public function setSuggObjTitle(string $sugg_obj_title): void
    {
        $this->sugg_obj_title = $sugg_obj_title;
    }

    public function getSuggObjectiveId(): int
    {
        return $this->sugg_objective_id;
    }

    public function setSuggObjectiveId(int $sugg_objective_id)
    {
        $this->sugg_objective_id = $sugg_objective_id;
    }

    public function getSuggForUser(): int
    {
        return $this->sugg_for_user;
    }

    public function setSuggForUser(int $sugg_for_user)
    {
        $this->sugg_for_user = $sugg_for_user;
    }
}