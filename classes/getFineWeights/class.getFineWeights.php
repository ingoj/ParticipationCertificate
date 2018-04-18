<?php

use SRAG\ILIAS\Plugins\LearningObjectiveSuggestions\Config\CourseConfig;

class getFineWeights {

	public static function getData() {
		global $DIC;
		$ilDB = $DIC->database();
		$result = $ilDB->query(self::getSQL());
		$weight = array();
		$weights = new getFineWeight();

		while ($row = $ilDB->fetchAssoc($result)) {
			switch ($row['cfg_key']) {
				case 'weight_fine_90':
					$weights->setWeightFine90($row['value']);
					break;
				case 'weight_fine_91':
					$weights->setWeightFine91($row['value']);
					break;
				case 'weight_fine_92':
					$weights->setWeightFine92($row['value']);
					break;
				case 'weight_fine_93':
					$weights->setWeightFine93($row['value']);
					break;
				case 'weight_fine_94':
					$weights->setWeightFine94($row['value']);
					break;
				case 'weight_fine_95':
					$weights->setWeightFine95($row['value']);
					break;
				case 'weight_fine_96':
					$weights->setWeightFine96($row['value']);
					break;
				case 'weight_fine_97':
					$weights->setWeightFine97($row['value']);
					break;
				case 'weight_fine_98':
					$weights->setWeightFine98($row['value']);
					break;
				case 'weight_fine_99':
					$weights->setWeightFine99($row['value']);
					break;
			}
		}

		return $weights;
	}


	protected static function getSQL() {
		$select = "select * from " . CourseConfig::TABLE_NAME;

		return $select;
	}
}