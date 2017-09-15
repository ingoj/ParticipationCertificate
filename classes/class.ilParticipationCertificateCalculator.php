<?php

/**
 * Class ilParticipationCertificateCalculator
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 *
 */
class ilParticipationCertificateCalculator {

	/**
	 * @var LearningObjectiveCourse
	 */
	protected $course;
	/**
	 * @var array
	 */
	protected $filters = array();
	/**
	 * @var array
	 */
	protected $limit = array( 0, 10 );
	/**
	 * @var \ilDB
	 */
	protected $db;


	public function __construct() {
		global $ilDB;
		$this->db = $ilDB;
	}


	public function setFilters(array $filters) {
		$this->filters = $filters;
	}


	public function filter($key, $value) {
		$this->filters[$key] = $value;

		return $this;
	}


	public function getData() {
		$set = $this->db->query($this->getSQL());
		$data = array();
		while ($row = $this->db->fetchAssoc($set)) {
			$data [] = $row;
		}

		return $data;
	}


	protected function getSQL($count = false) {
		global $results;
		$sql = "DROP TABLE IF Exists tmp_test_result;

CREATE Temporary Table tmp_test_result (SELECT max(points) as points, active_fi, maxpoints FROM tst_pass_result group by active_fi, maxpoints);
SELECT 
usr.usr_id,
usr.firstname,
usr.lastname,
usr.login,
crs_obj.obj_id as crs_obj_id,
crs_obj.title as crs_obj_title,
obj.title as learning_group_title,
crso.objective_id as crso_objective_suggestion_id,
crso.title as learning_objective_suggestion,
crsolm_crs.title as learning_objectives_suggestion_crs_title,
crsolm_crs_crso.objective_id as learning_objectives_suggestion_crs_objective_id,
crsolm_crs_crso.title as learning_objectives_suggestion_crs_objective,
test_act.tries,
tmp_test_result.points,
tmp_test_result.maxpoints

FROM ilias.obj_members as memb
inner join usr_data as usr on usr.usr_id = memb.usr_id
inner join object_data as obj on obj.obj_id = memb.obj_id and obj.type = 'grp'
inner join object_reference as grp_ref on grp_ref.obj_id = obj.obj_id
inner join alo_suggestion as sugg on sugg.user_id = memb.usr_id
inner join crs_objectives as crso on crso.objective_id = sugg.objective_id
inner join object_data as crs_obj on crs_obj.obj_id = crso.crs_id
inner join loc_settings as itest on itest.obj_id = sugg.course_obj_id and itest.itest is not null
inner join tst_active as tst on tst.objective_container = sugg.course_obj_id and tst.user_fi = memb.usr_id and tst.submitted = 1
inner join crs_objective_lm as crsolm on crsolm.objective_id = crso.objective_id
inner join object_reference as crsolm_ref on crsolm_ref.ref_id = crsolm.ref_id
inner join container_reference as crsolm_crs_ref on crsolm_crs_ref.obj_id = crsolm_ref.obj_id
inner join object_data as crsolm_crs on crsolm_crs.obj_id = crsolm_crs_ref.target_obj_id and crsolm_crs.type = 'crs'
inner join crs_objectives as crsolm_crs_crso on crsolm_crs_crso.crs_id = crsolm_crs.obj_id
inner join crs_objective_lm as crsolm_crs_objlm on crsolm_crs_objlm.objective_id = crsolm_crs_crso.objective_id and crsolm_crs_objlm.type = 'tst''
inner join tst_tests as test on test.obj_fi = crsolm_crs_objlm.obj_id
left join tst_active as test_act on test_act.test_fi = test.test_id and test_act.user_fi = memb.usr_id
left join tmp_test_result on tmp_test_result.active_fi = test_act.active_id 
where grp_ref.ref_id = 73;";

		$results = $this->db->query($sql);

		while ($set = $this->db->fetchAssoc($results)) {
			
		}
		/*
		if (true) {
			//Resultat in Prozent des jeweiligen Lernziels. z.B. LO1
			foreach ($results->fetchRow() as $row) {
				$lowResult [] = $row['result.points'] / $row['result.maxpoints'] * 100;
			}
			foreach (array_count_values($lowResult) as $array_count_value) {
				//Resultat aller Lernziele. Zum Beispiel Quadratische Gleichungen
				$midResult [] = (array_sum($lowResult)) / count($lowResult);
			}
			$finalResult = (array_sum($midResult)) / count($midResult) . + '%';
		}
		//wenn Einstiegstest nicht abgeschlossen automatisch 0%
		else{
			$finalResult = '0%';
		}
		*/
		return $results;
	}
	/*
		public function calculateFinal() {
			global $results;
			//Resultat in Prozent des jeweiligen Lernziels. z.B. LO1
			foreach ($row = $as $count) {
				$lowResult [] = $row['result.points'] / $row['result.maxpoints'] * 100;
				foreach () {
					//Resultat aller Lernziele. Zum Beispiel Quadratische Gleichungen
					$midResult [] = (array_sum($lowResult)) / count($lowResult);
					foreach () {
						$finalResult = (array_sum($midResult)) / count($midResult);
					}
				}
			}

			return $finalResult;
		}
	*/
}