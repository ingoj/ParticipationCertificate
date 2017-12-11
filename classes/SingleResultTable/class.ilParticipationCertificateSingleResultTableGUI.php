<?php

require_once './Services/Table/classes/class.ilTable2GUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultModificationGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/SingleResultTable/class.ilParticipationCertificateSingleResultGUI.php';
require_once './Customizing/global/plugins/Services/Cron/CronHook/LearningObjectiveSuggestions/src/Score/LearningObjectiveScores.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/getFineWeights/class.getFineWeights.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/TestMark/class.TestMarks.php';

/**
 * Class ilParticipationCertificateResultGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateSingleResultTableGUI extends ilTable2GUI {

	const IDENTIFIER = 'usr_id';
	const SUCCESSFUL_PROGRESS_CSS_CLASS = "ilCourseObjectiveProgressBarCompleted";
	const NON_SUCCESSFUL_PROGRESS_CSS_CLASS = "ilCourseObjectiveProgressBarNeutral";
	const FAILED_PROGRESS_CSS_CLASS = "ilCourseObjectiveProgressBarFailed";
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificateResultGUI
	 */
	protected $parent_obj;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var array
	 */
	protected $filter = array();
	/**
	 * @var
	 */
	protected $usr_id;
	/**
	 * @var array
	 */
	protected $marks = array();


	/**
	 * ilParticipationCertificateResultGUI constructor.
	 *
	 * @param ilParticipationCertificateResultGUI $a_parent_obj
	 * @param string                              $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd) {
		global $ilCtrl, $tabs;

		$this->ctrl = $ilCtrl;
		$this->tabs = $tabs;
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->setPrefix('dhbw_part_cert_res');
		$this->setFormName('dhbw_part_cert_res');
		$this->setId('dhbw_part_cert_res');

		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultModificationGUI', [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', 'usr_id');

		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		$usr_id = $_GET[self::IDENTIFIER];
		$this->usr_id = $usr_id;

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->getEnableHeader();
		$this->setRowTemplate('tpl.default_row_single.html', $this->pl->getDirectory());
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$nameUser = $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' . $arr_usr_data[$usr_id]->getPartCertLastname();
		$this->setTitle($this->pl->txt('result_for ') . ' ' . $nameUser);

		$this->addColumns();
		$this->parseData();
	}


	/**
	 * Get selectable columns
	 *
	 * @return        array    key: column id, val: true/false -> default on/off
	 */
	function getSelectableColumns() {
		$cols = array();

		$finalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);
		$sorted = $this->sortColumns();

		if (count($finalTestsStates[$this->usr_id])) {
			while (count($sorted)) {
				foreach ($finalTestsStates[$this->usr_id] as $finalTestsState) {
					if ($finalTestsState->getLocftestCrsTitle() == key($sorted)) {
						/**
						 * @var ilLearnObjectFinalTestState $finalTestsState
						 */
						$cols[$finalTestsState->getLocftestCrsObjId()] = array(
							'txt' => $finalTestsState->getLocftestCrsTitle(),
							'default' => true,
							'width' => 'auto',
						);
						unset($sorted[key($sorted)]);
					}
				}
			}
		}

		return $cols;
	}


	function sortColumns() {
		//First sort scores
		$scores = LearningObjectiveScores::getData($this->usr_id);
		//if the scores are equal, sort because of the weight value
		$weights = getFineWeights::getData();
		$newWeights = (array)$weights;

		$i = 0;
		//associate the weight with the corresponding obj
		while (count($newWeights)) {
			foreach ($scores as $score) {
				$number = str_split($score->getObjectiveId());
				if ($number[1] == $i) {
					$sorting[$score->getTitle()] = [
						'score' => $score->getScore(),
						'obj_id' => $score->getObjectiveId(),
						'weight' => $newWeights['weight_fine_' . $score->getObjectiveId()]
					];
					unset($newWeights['weight_fine_' . $score->getObjectiveId()]);
					$i ++;
				}
			}
		}
		//sort the array first for the score. Second argument is the weight.
		foreach ($sorting as $key => $item) {
			$scored[$key] = $item['score'];
			$weighting[$key] = $item['weight'];
		}
		array_multisort($scored, SORT_DESC, $weighting, SORT_DESC, $sorting);

		return $sorting;
	}


	private function addColumns() {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if (isset($v['sort_field'])) {
					$sort = $v['sort_field'];
				} else {
					$sort = NULL;
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
	}


	public function parseData() {

		$arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);

		$usr_id = $this->usr_id;

		$rec_array = array();

		if (count($arr_FinalTestsStates[$usr_id])) {
			foreach ($arr_FinalTestsStates[$usr_id] as $rec) {
				if ($rec->getLocftestCrsObjId()) {
					$strng = explode(':',$rec->getLocftestTestTitle());
					$string = $strng[0]."<br>".$strng[1];
					$rec_array[$rec->getLocftestCrsObjId()][] = $string;
				}
				$marks [$rec->getLocftestCrsObjId()][] = $rec->getLocftestTestRefId();
				$this->marks = $marks;
				$scores[$rec->getLocftestCrsObjId()][] = [$rec->getLocftestPercentage() . '%',$rec->getLocftestTestRefId()];

			}
			foreach ($rec_array as $key => $item) {
				$v = 0;
				for ($k = 0; $k <= count($item); $k ++) {
					$new_rec_array[$v][$key] = $item[$k];
					$v = $v + 2;
				}
			}
			foreach ($scores as $key => $score) {
				$v = 1;
				for ($k = 0; $k <= count($score); $k ++) {
					$new_rec_array[$v][$key] = $score[$k];
					$v = $v + 2;
				}
			}
		}
		ksort($new_rec_array);
		$this->setData($new_rec_array);
		return $new_rec_array;
	}


	protected function buildProgressBar($points,$test_obj) {

		$mark = TestMarks::getData($test_obj);

		$points = $points[0];
		$tooltip_id = "prg_";

		if (is_object($mark)) {
			$required_amount_of_points = $mark->getMinimumlvl();
		}
		else{
			$required_amount_of_points = 50;
		}
		$maximum_possible_amount_of_points = 100;
		$current_amount_of_points = $points;

		if ($maximum_possible_amount_of_points > 0) {
			$current_percent = (int)($current_amount_of_points * 100 / $maximum_possible_amount_of_points);
			$required_percent = (int)($required_amount_of_points * 100 / $maximum_possible_amount_of_points);
		}
			else {
				$current_percent = 0;
				$required_percent = 0;
		}


		//required to dodge bug in ilContainerObjectiveGUI::renderProgressBar
		if ($required_percent == 0) {
			$required_percent = 0.1;
		}



		if($points >= $required_amount_of_points) {
			$css_class = self::SUCCESSFUL_PROGRESS_CSS_CLASS;
		}
		else {
			$css_class = self::FAILED_PROGRESS_CSS_CLASS;
		}

		require_once("Services/Container/classes/class.ilContainerObjectiveGUI.php");

		return ilContainerObjectiveGUI::renderProgressBar($current_percent, $required_percent, $css_class, '', NULL, $tooltip_id, '');
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$this->marks;
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if ($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					if (is_array($a_set[$k])) {
						$this->tpl->setVariable('BAR', $this->buildProgressBar(explode('%',$a_set[$k][0]),$a_set[$k][1]));
					}
					else {
						$this->tpl->setVariable('COURSE', $a_set[$k]);
					}
					$this->tpl->parseCurrentBlock();
				} else {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('COURSE', '&nbsp;');
					$this->tpl->parseCurrentBlock();
				}
			}
		}
	}
}
?>