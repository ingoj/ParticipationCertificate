<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateTableGUI.php';
/**
 * Class ilParticipationCertificateResultModificationGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilParticipationCertificateResultModificationGUI: ilParticipationCertificateTableGUI, ilUIPluginRouterGUI
 */
class ilParticipationCertificateResultModificationGUI {

	CONST CMD_DISPLAY = 'display';
	CONST IDENTIFIER = 'usr_id';
	CONST CMD_PRINT = 'printpdf';

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

	public function __construct() {
		global $ilCtrl, $ilTabs,$tpl,$usr_id;

		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->tpl = $tpl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();


		$this->groupRefId = (int)$_GET['ref_id'];
		$group_ref_id = $this->groupRefId;
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultModificationGUI', [ 'ref_id', 'group_id' ]);


		$cert_access = new ilParticipationCertificateAccess($group_ref_id);
		$this->usr_ids = $cert_access->getUserIdsOfGroup();
		$usr_id = $_GET[self::IDENTIFIER];


		$this->arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$this->arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
		$this->arr_learn_reached_percentages = ilLearnObjectSuggReachedPercentages::getData($this->usr_ids);
		$this->arr_iass_states = ilIassStates::getData($this->usr_ids);
		$this->arr_excercise_states = ilExcerciseStates::getData($this->usr_ids);
		$this->arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);
		$this->array_obj_ids = ilLearnObjectFinalTestStates::getData($this->usr_ids);
	}




	public function executeCommand(){
		$nextClass = $this->ctrl->getNextClass();
		switch($nextClass){
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

	public function initHeader(){
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI','ref_id');
		$this->tabs->setBackTarget('Zurück', $this->ctrl->getLinkTargetByClass(ilParticipationCertificateTableGUI::class,ilParticipationCertificateTableGUI::CMD_CONTENT));


		$this->ctrl->saveParameterByClass('ilParticipationCertificateTableGUI', 'ref_id');
		$this->tabs->addTab('overview', 'Übersicht', $this->ctrl->getLinkTargetByClass(ilParticipationCertificateTableGUI::class,ilParticipationCertificateTableGUI::CMD_CONTENT));


		$this->tabs->addTab('config', 'Konfigurieren', $this->ctrl->getLinkTargetByClass(ilParticipationCertificateGUI::class,ilParticipationCertificateGUI::CMD_DISPLAY));
		$this->tabs->activateTab('config,overview');

	}

	public function initForm(){
		$usr_id = $_GET[self::IDENTIFIER];
		$arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
		$nameUser = $arr_usr_data[$usr_id]->getPartCertFirstname().' '.$arr_usr_data[$usr_id]->getPartCertLastname();



		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle('Resultate für '. $nameUser .' bearbeiten');

		$initialtest = new ilTextAreaInputGUI('Einstiegstest abgeschlossen (Ja/Nein)','initial');
		$form->addItem($initialtest);

		$resultstests = new ilTextAreaInputGUI('Resultat qualifizierende Tests (Prozentwert)','resultstest');
		$form->addItem($resultstests);

		$conferences = new ilTextAreaInputGUI('Bearbeitung der Aufgaben zu überfachlichen Themen (Ja/Nein)','conf');
		$form->addItem($conferences);

		$homeworks = new ilTextAreaInputGUI('Bearbeitung der aufgaben zu überfachlichen Themen (Prozentwert)','homework');
		$form->addItem($homeworks);

		//TODO Change Link
		$form->addCommandButton(ilParticipationCertificateResultModificationGUI::CMD_PRINT,'PDF Drucken');

		return $form;

	}

	public function save(){
		$form = $this->initForm();

		if (!$form->checkInput()){
			return false;
		}
	}


	public function fillForm(&$form){

		//TODO catch if there is no object(value)
		$usr_id = $_GET[self::IDENTIFIER];

		$array = array('initial' => $this->arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted(),
			'resultstest' => $this->arr_learn_reached_percentages[$usr_id]->getAveragePercentage(),
			'conf' => $this->arr_iass_states[$usr_id]->getPassed(),
			'homework' => $this->arr_excercise_states[$usr_id]->getPassedPercentage());


		$form->setValuesbyArray($array);

	}

	public function printPDF($usr_id){
		//TODO wert usr_id übergeben
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId);
		$solo = true;
		$twigParser->parseData($solo,$usr_id);
	}


}