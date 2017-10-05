<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateTableGUI.php';

/**
 * Class ilParticipationCertificateResultModificationGUI
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilParticipationCertificateResultModificationGUI: ilParticipationCertificateTableGUI, ilUIPluginRouterGUI
 */
class ilParticipationCertificateResultModificationGUI {

	CONST CMD_DISPLAY = 'display';
	CONST IDENTIFIER = 'usr_id';
	CONST CMD_PRINT = 'printpdf';
	CONST CMD_PRINT_PURE = 'printpdfpure';
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
		$group_ref_id = $this->groupRefId;
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultModificationGUI', [ 'ref_id', 'group_id' ]);

		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI','usr_id');
		$cert_access = new ilParticipationCertificateAccess($group_ref_id);
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
	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_DISPLAY);
				$this->{$cmd}();
				break;
			case 'ilparticipationcertificatetablegui':
				$ilParticipationCertificateTableGUI = new ilParticipationCertificateTableGUI();
				$this->ctrl->forwardCommand($ilParticipationCertificateTableGUI);
				break;
			case 'ilparticipationcertificategui':
				$ilParticipationCertificateGUI = new ilParticipationCertificateGUI();
				$ret2 = $this->ctrl->forwardCommand($ilParticipationCertificateGUI);
				$this->tabs->setTabActive(ilParticipationCertificateGUI::CMD_CONFIG);
				break;
		}
		//$this->tpl->show();
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

		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI', 'ref_id');
		$this->tabs->setBackTarget('Zurück', $this->ctrl->getLinkTargetByClass(ilParticipationCertificateTableGUI::class, ilParticipationCertificateTableGUI::CMD_CONTENT));
	}


	public function initForm() {
		$usr_id = $_GET[self::IDENTIFIER];
		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$nameUser = $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' . $arr_usr_data[$usr_id]->getPartCertLastname();

		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle('Resultate für ' . $nameUser . ' bearbeiten');

		$initialtest = new ilTextInputGUI('Einstiegstest abgeschlossen (Ja(1)/Nein(0) )', 'initial');
		$form->addItem($initialtest);

		$resultstests = new ilTextInputGUI('Resultat qualifizierende Tests (Prozentwert)', 'resultstest');
		$form->addItem($resultstests);

		$conferences = new ilTextInputGUI('Bearbeitung der Aufgaben zu überfachlichen Themen (Ja(1)/Nein(0) )', 'conf');
		$form->addItem($conferences);

		$homeworks = new ilTextInputGUI('Bearbeitung der aufgaben zu überfachlichen Themen (Prozentwert)', 'homework');
		$form->addItem($homeworks);

		$form->addCommandButton(ilParticipationCertificateResultModificationGUI::CMD_PRINT, 'PDF Drucken');

		return $form;
	}


	public function save() {
		$form = $this->initForm();

		if (!$form->checkInput()) {
			return false;
		}
	}


	public function fillForm(&$form) {

		//TODO catch if there is no object(value)
		$usr_id = $_GET[self::IDENTIFIER];

		if (is_object($this->arr_initial_test_states[$usr_id])) {
			$array = array(
				'initial' => $this->arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted(),
				'resultstest' => $this->arr_learn_reached_percentages[$usr_id]->getAveragePercentage(),
				'conf' => $this->arr_iass_states[$usr_id]->getPassed(),
				'homework' => $this->arr_excercise_states[$usr_id]->getPassedPercentage()
			);
		}
		$form->setValuesbyArray($array);
	}


	public function printPDF() {
		$form = $this->initForm();
		$form->setValuesByPost();
		$form->checkInput();

		$array = array($form->getInput('initial'),$form->getInput('resultstest'),$form->getInput('conf'),$form->getInput('homework'));
		$edited = true;
		$usr_id = $this->usr_id;
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), true,true);
		$twigParser->parseDataSolo($edited,$array,$usr_id);
	}

	public function printPDFpure() {
		$form = $this->initForm();
		$form->setValuesByPost();
		$form->checkInput();

		$array = array($form->getInput('initial'),$form->getInput('resultstest'),$form->getInput('conf'),$form->getInput('homework'));
		$edited = false;
		$usr_id = $this->usr_id;
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId,array(),true,false);
		$twigParser->parseDataSolo($edited,$array,$usr_id);
	}



}