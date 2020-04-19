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
			'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL,
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

		$finalTestsStates = ilLearnObjectFinalTestStates::getData([$this->usr_id ]);

		$sorted = $this->sortColumns();
		$i = 0;

		if (count($finalTestsStates[$this->usr_id])) {
			while (count($sorted)) {
				foreach ($finalTestsStates[$this->usr_id] as $finalTestsState) {
					if ($finalTestsState->getLocftestObjectiveId() == key($sorted)) {
						/**
						 * @var ilLearnObjectFinalTestState $finalTestsState
						 */
						$cols[$finalTestsState->getLocftestCrsObjId()] = array(
							'txt' => $finalTestsState->getLocftestLearnObjectiveTitle(),
							'obj_id' => $sorted[key($sorted)]['obj_id'],
							'objective_id' => $sorted[key($sorted)]['objective_id'],
							'default' => true,
							'width' => 'auto',
						);
						unset($sorted[key($sorted)]);
					}
				}
				$i = $i+1;

				if($i == 1000) {
					ilUtil::sendFailure("Der Aufruf ist Fehlgeschlagen");
					break;
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

			/**
			 * @var NewLearningObjectiveScore $score
			 */


			$sorting[$score->getObjectiveId()] = [
				'title' => $score->getTitle(),
				'score' => $score->getScore(),
				'obj_id' => $score->getCourseObjId(),
				'objective_id' => $score->getObjectiveId(),
				'weight' => $newWeights['weight_fine_' . $score->getObjectiveId()]
			];
		}


		//sort the array first for the score. Second argument is the weight.
	/*
		$scored = array();
		$weighting = array();
		foreach ($sorting as $key => $item) {
			$scored[$key] = $item['score'];
			$weighting[$key] = $item['weight'];
		}
		array_multisort($scored, SORT_DESC, $weighting, SORT_DESC, $sorting);
		print_r($sorting);exit;
	*/
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

		$arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData([$this->usr_id]);
		$usr_id = $this->usr_id;
		$rec_array = array();


		if (count($arr_FinalTestsStates[$usr_id])) {

			//build_row_key_s
			foreach ($arr_FinalTestsStates[$usr_id] as $rec) {
				/**
				 * @var ilLearnObjectFinalTestState $rec;
				 */
				$row_key[$rec->getLocftestCrsObjId()] = 0;
			}

			foreach ($arr_FinalTestsStates[$usr_id] as $rec) {
				/**
				 * @var ilLearnObjectFinalTestState $rec;
				 */

				if ($rec->getLocftestCrsObjId()) {
					//first line - title lp
					$rec_array[$row_key[$rec->getLocftestCrsObjId()]][$rec->getLocftestCrsObjId()] = $rec->getLocftestObjectiveTitle();

					$row_key[$rec->getLocftestCrsObjId()] += 1;
					//second line - array progressbar
					$rec_array[$row_key[$rec->getLocftestCrsObjId()]][$rec->getLocftestCrsObjId()][0] = $rec->getLocftestPercentage();
					$rec_array[$row_key[$rec->getLocftestCrsObjId()]][$rec->getLocftestCrsObjId()][1] = $rec->getLocftestQplsRequiredPercentage();
					$rec_array[$row_key[$rec->getLocftestCrsObjId()]][$rec->getLocftestCrsObjId()][2] = 1;

					$row_key[$rec->getLocftestCrsObjId()] += 1;
				}

			}
		}

		$this->setData($rec_array);

		return $rec_array;
	}


	/**
	 * @param object $points
	 * @param int $req_percentage
	 * @param object $tries
	 *
	 * @return string
	 */
	protected function buildProgressBar($points, $required_percent = 0, $tries) {
		$points = $points[0];
		$tooltip_id = "prg_";

		$maximum_possible_amount_of_points = 100;
		$current_amount_of_points = $points;

		if ($maximum_possible_amount_of_points > 0) {
			$current_percent = (int)($current_amount_of_points * 100 / $maximum_possible_amount_of_points);
		} else {
			$current_percent = 0;
		}
		//required to dodge bug in ilContainerObjectiveGUI::renderProgressBar
		if ($required_percent == 0) {
			$required_percent = 0.1;
		}

		if ($current_percent >= $required_percent) {
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
					if ($this->searchForId($v['objective_id'], $this->sugg)) {
						$this->tpl->setVariable('COLOR', $this->color);
					}
					$this->tpl->parseCurrentBlock();
				} else {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('COURSE', '&nbsp;');
					if ($this->searchForId($v['objective_id'], $this->sugg)) {
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
			if ($val->getSuggObjectiveId() == $id) {
				return true;
			}
		}

		return false;
	}
}
