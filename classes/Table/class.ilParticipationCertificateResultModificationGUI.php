<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultGUI.php';

/**
 * Class ilParticipationCertificateResultModificationGUI
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilParticipationCertificateResultModificationGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls      ilParticipationCertificateResultModificationGUI: ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateResultModificationGUI {

	CONST CMD_DISPLAY = 'display';
	CONST IDENTIFIER = 'usr_id';
	/**
	 * @var ilTabsGUI
	 */
	public $tabs;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;


	public function __construct() {
		global $ilCtrl, $ilTabs, $tpl, $ilToolbar;

		$this->toolbar = $ilToolbar;
		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->tpl = $tpl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();

		$this->groupRefId = (int)$_GET['ref_id'];
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultModificationGUI', [ 'ref_id', 'group_id' ]);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultModificationGUI', 'ementor');
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', 'usr_id');
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();
		$usr_id = $_GET[self::IDENTIFIER];
		$this->usr_id = $usr_id;

		$this->arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$this->arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$this->arr_learn_reached_percentages = ilLearnObjectSuggReachedPercentages::getData($this->usr_ids);
		$this->arr_iass_states = ilIassStates::getData($this->usr_ids);
		$this->arr_excercise_states = ilExcerciseStates::getData($this->usr_ids);
		$this->arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);
		$this->array_obj_ids = ilLearnObjectFinalTestStates::getData($this->usr_ids);

		$this->ctrl->setParameterByClass('ilParticipationCertificateResultModificationGUI', 'edited', true);
		$this->ctrl->setParameterByClass('ilParticipationCertificateResultModificationGUI', 'ementor', true);
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			case 'ilparticipationcertificateresultgui':
				$ilParticipationCertificateresultGUI = new ilParticipationCertificateResultGUI();
				$this->ctrl->forwardCommand($ilParticipationCertificateresultGUI);
				break;
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_DISPLAY);
				$this->{$cmd}();
				break;
		}
	}


	public function display() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();
		$form = $this->initForm();
		$this->fillForm($form);

		$this->tpl->setContent($form->getHTML());
		$this->tpl->show();
	}


	public function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', 'ref_id');
		$this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(array(
			'ilUIPluginRouterGUI',
			ilParticipationCertificateResultGUI::class
		), ilParticipationCertificateResultGUI::CMD_CONTENT));
	}


	public function initForm() {
		$usr_id = $_GET[self::IDENTIFIER];
		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$nameUser = $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' . $arr_usr_data[$usr_id]->getPartCertLastname();

		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle('Resultate für ' . $nameUser . ' bearbeiten');

		$initialtest = new ilTextInputGUI($this->pl->txt('mod_initial'), 'initial');
		$form->addItem($initialtest);

		$resultstests = new ilTextInputGUI($this->pl->txt('mod_resultstest'), 'resultstest');
		$form->addItem($resultstests);

		$conferences = new ilTextInputGUI($this->pl->txt('mod_conf'), 'conf');
		$form->addItem($conferences);

		$homeworks = new ilTextInputGUI($this->pl->txt('mod_homework'), 'homework');
		$form->addItem($homeworks);

		$form->addCommandButton(ilParticipationCertificateResultGUI::CMD_PRINT_PDF, $this->pl->txt('list_print'));

		return $form;
	}


	public function save() {
		$form = $this->initForm();

		if (!$form->checkInput()) {
			return false;
		}
	}


	public function fillForm(&$form) {


		$usr_id = $_GET[self::IDENTIFIER];

		if (is_object($this->arr_initial_test_states[$usr_id])) {
			$array['initial'] = $this->arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted();
		} else {
			$array['initial'] = 0;
		}
		if (is_object($this->arr_learn_reached_percentages[$usr_id])) {
			$array['resultstest'] = $this->arr_learn_reached_percentages[$usr_id]->getAveragePercentage();
		} else {
			$array['resultstest'] = 0;
		}
		if (is_object($this->arr_iass_states[$usr_id])) {
			$array['conf'] = $this->arr_iass_states[$usr_id]->getPassed();
		} else {
			$array['conf'] = 0;
		}
		if (is_object($this->arr_excercise_states[$usr_id])) {
			$array['homework'] = $this->arr_excercise_states[$usr_id]->getPassedPercentage();
		} else {
			$array['homework'] = 0;
		}
		$form->setValuesbyArray($array);
	}


	public function printPDF() {
		$form = $this->initForm();
		$form->setValuesByPost();
		$form->checkInput();

		$array = array( $form->getInput('initial'), $form->getInput('resultstest'), $form->getInput('conf'), $form->getInput('homework') );
		$ementor = $_GET['ementor'];
		$edited = $_GET['edited'];
		$usr_id = $this->usr_id;
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, $ementor, $edited, $array);
		$twigParser->parseData();
	}
}

?>