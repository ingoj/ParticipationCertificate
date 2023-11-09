<?php

use SRAG\ILIAS\Plugins\LearningObjectiveSuggestions\Score\LearningObjectiveScore;

class NewLearningObjectiveScores {

	public static function getData(int $usr_id): array
    {
		global $DIC;
		$ilDB = $DIC->database();
		$result = $ilDB->query(self::getSQL($usr_id));
		$scores = array();

		while ($row = $ilDB->fetchAssoc($result)) {
			$score = new NewLearningObjectiveScore();
			$score->setCourseObjId($row['course_obj_id']);
			$score->setUserId($row['user_id']);
			$score->setScore($row['score']);
			$score->setObjectiveId($row['objective_id']);
			$score->setTitle($row['title']);
			$scores[] = $score;
		}

		return $scores;
	}


	protected static function getSQL(int $usr_id): string
    {
		global $DIC;
		$ilDB = $DIC->database();
		$select = "select * from " . LearningObjectiveScore::TABLE_NAME . "
					inner join crs_objectives on " . LearningObjectiveScore::TABLE_NAME . ".objective_id = crs_objectives.objective_id 
					where user_id =" . $ilDB->quote($usr_id, "integer") . "
					order by score DESC";

		return $select;
	}
}