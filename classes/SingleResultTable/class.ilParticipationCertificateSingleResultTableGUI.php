<?php

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
	 * @var string
	 */
	protected $color;
	/**
	 * @var array
	 */
	protected $usr_ids;
	/**
	 * @var ilLearningObjectivesMasterCrs[]
	 */
	protected $sugg;


	/**
	 * ilParticipationCertificateResultGUI constructor.
	 *
	 * @param ilParticipationCertificateResultGUI $a_parent_obj
	 * @param string                              $a_parent_cmd
	 * @param int                                 $usr_id
	 */
	public function __construct($a_parent_obj, $a_parent_cmd, $usr_id) {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->tabs = $DIC->tabs();
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$config = ilParticipationCertificateConfig::where(array(
			'config_key' => 'color',
			'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			"group_ref_id" => 0
		))->first();
		$this->color = $config->getConfigValue();

		$this->setPrefix('dhbw_part_cert_res');
		$this->setFormName('dhbw_part_cert_res');
		$this->setId('dhbw_part_cert_res');

		$this->setLimit(0);
		$this->setShowRowsSelector(false);

		$this->ctrl->saveParameterByClass(ilParticipationCertificateResultModificationGUI::class, [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, 'usr_id');

		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();

		$this->usr_id = $usr_id;

		$this->sugg = getLearnSuggs::getData($usr_id);

		parent::__construct($a_parent_obj, $a_parent_cmd);

		$this->getEnableHeader();
		$this->setRowTemplate('tpl.default_row_single.html', $this->pl->getDirectory());
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$nameUser = $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' . $arr_usr_data[$usr_id]->getPartCertLastname();
		if ($nameUser == ' ') {
			$nameUser = $this->pl->txt('loginname') . ' ' . $arr_usr_data[$usr_id]->getPartCertUserName();
		}

		$this->setTitle($this->pl->txt('result_for ') . ' ' . $nameUser);

		$this->addColumns();
		$this->parseData();
	}


	/**
	 * Get selectable columns
	 *
	 * @return array    key: column id, val: true/false -> default on/off
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
							'obj_id' => $sorted[key($sorted)]['obj_id'],
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


	/**
	 * @return array
	 */
	function sortColumns() {
		//First sort scores
		$scores = NewLearningObjectiveScores::getData($this->usr_id);
		//if the scores are equal, sort because of the weight value
		$weights = getFineWeights::getData();
		$newWeights = (array)$weights;

		$sorting = array();

		foreach ($scores as $score) {
			$sorting[$score->getTitle()] = [
				'score' => $score->getScore(),
				'obj_id' => $score->getObjectiveId(),
				'weight' => $newWeights['weight_fine_' . $score->getObjectiveId()]
			];
		}

		//sort the array first for the score. Second argument is the weight.
		$scored = array();
		$weighting = array();
		foreach ($sorting as $key => $item) {
			$scored[$key] = $item['score'];
			$weighting[$key] = $item['weight'];
		}
		array_multisort($scored, SORT_DESC, $weighting, SORT_DESC, $sorting);

		return $sorting;
	}


	/**
	 *
	 */
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


	/**
	 * @return array
	 */
	public function parseData() {

		$arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);

		$usr_id = $this->usr_id;

		$rec_array = array();
		$new_rec_array = array();

		if (count($arr_FinalTestsStates[$usr_id])) {
			foreach ($arr_FinalTestsStates[$usr_id] as $rec) {
				if ($rec->getLocftestCrsObjId()) {
					$strng = explode(':', $rec->getLocftestTestTitle());
					$string = $strng[0] . "<br>" . $strng[1];
					$rec_array[$rec->getLocftestCrsObjId()][] = $string;
				}
				//create two arrays, one for the mark when a test is fulfilled and one for the scores.
				$marks [$rec->getLocftestCrsObjId()][] = $rec->getLocftestTestRefId();
				$scores[$rec->getLocftestCrsObjId()][] = [
					$rec->getLocftestPercentage() . '%',
					$rec->getLocftestTestObjId(),
					$rec->getLocftestTries()
				];
			}
			//merge the score array and the array with the test titles
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

			ksort($new_rec_array);
		}

		$this->setData($new_rec_array);

		return $new_rec_array;
	}


	/**
	 * @param object $points
	 * @param object $test_obj
	 * @param object $tries
	 *
	 * @return string
	 */
	protected function buildProgressBar($points, $test_obj, $tries) {
		//Holt von allen Tests das minimum um zu bestehen
		$mark = TestMarks::getData($test_obj);

		$points = $points[0];
		$tooltip_id = "prg_";

		if (is_object($mark)) {
			$required_amount_of_points = $mark->getMinimumlvl();
		} else {
			$required_amount_of_points = 50;
		}
		$maximum_possible_amount_of_points = 100;
		$current_amount_of_points = $points;

		if ($maximum_possible_amount_of_points > 0) {
			$current_percent = (int)($current_amount_of_points * 100 / $maximum_possible_amount_of_points);
			$required_percent = (int)($required_amount_of_points * 100 / $maximum_possible_amount_of_points);
		} else {
			$current_percent = 0;
			$required_percent = 0;
		}
		//required to dodge bug in ilContainerObjectiveGUI::renderProgressBar
		if ($required_percent == 0) {
			$required_percent = 0.1;
		}

		if ($points >= $required_amount_of_points) {
			$css_class = self::SUCCESSFUL_PROGRESS_CSS_CLASS;
		} elseif ($tries == NULL) {
			$css_class = self::NON_SUCCESSFUL_PROGRESS_CSS_CLASS;
		} else {
			$css_class = self::FAILED_PROGRESS_CSS_CLASS;
		}

		require_once("Services/Container/classes/class.ilContainerObjectiveGUI.php");

		return ilContainerObjectiveGUI::renderProgressBar($current_percent, $required_percent, $css_class, '', NULL, $tooltip_id, '');
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if ($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					if (is_array($a_set[$k])) {
						$this->tpl->setVariable('COURSE', $this->buildProgressBar(explode('%', $a_set[$k][0]), $a_set[$k][1], $a_set[$k][2]));
					} else {
						$this->tpl->setVariable('COURSE', $a_set[$k]);
					}
					if ($this->searchForId($v['obj_id'], $this->sugg)) {
						$this->tpl->setVariable('COLOR', $this->color);
					}
					$this->tpl->parseCurrentBlock();
				} else {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('COURSE', '&nbsp;');
					if ($this->searchForId($v['obj_id'], $this->sugg)) {
						$this->tpl->setVariable('COLOR', $this->color);
					}
					$this->tpl->parseCurrentBlock();
				}
			}
		}
	}


	/**
	 * @param int $id
	 * @param array $array
	 *
	 * @return bool
	 */
	function searchForId($id, $array) {
		foreach ($array as $key => $val) {
			if ($val->getSuggObjectiveId() === $id) {
				return true;
			}
		}

		return false;
	}
}
