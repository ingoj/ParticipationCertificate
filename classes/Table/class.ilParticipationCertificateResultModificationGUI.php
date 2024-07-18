<?php

/**
 * Class ilParticipationCertificateResultModificationGUI
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateResultModificationGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls      ilParticipationCertificateResultModificationGUI: ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateResultModificationGUI
{

    const CMD_DISPLAY = 'display';
    const IDENTIFIER = 'usr_id';
    public ilTabsGUI $tabs;
    protected ilTemplate|ilGlobalTemplateInterface $tpl;
    protected ilCtrl|ilCtrlInterface $ctrl;
    protected ilParticipationCertificatePlugin $pl;
    protected ilToolbarGUI $toolbar;
    protected int $groupRefId;
    protected ?ilObject $learnGroup;
    protected array $usr_ids;
    protected mixed $usr_id;
    /**
     * @var ilPartCertUserData[]
     */
    protected array $arr_usr_data;
    /**
     * @var ilCrsInitialTestState[]
     */
    protected array $arr_initial_test_states;
    /**
     * @var ilLearnObjectSuggResult[]
     */
    protected array $arr_learn_reached_percentages;
    /**
     * @var ilIassState[]
     */
    protected array $arr_iass_states;
    /**
     * @var ilExcerciseState[]
     */
    protected array $arr_excercise_states;
    /**
     * @var ilLearnObjectFinalTestState[][]
     */
    protected array $arr_FinalTestsStates;
    /**
     * @var ilLearnObjectFinalTestState[][]
     */
    protected array $array_obj_ids;


    public function __construct()
    {
        global $DIC;

        $this->toolbar = $DIC->toolbar();
        $this->ctrl = $DIC->ctrl();
        $this->tabs = $DIC->tabs();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilParticipationCertificatePlugin::getInstance();

        $this->groupRefId = (int)$_GET['ref_id'];
        $this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultModificationGUI::class, ['ref_id', 'group_id']);
        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultModificationGUI::class, 'ementor');
        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, 'usr_id');
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        $this->usr_ids = $cert_access->getUserIdsOfGroup();
        $usr_id = $_GET[self::IDENTIFIER];
        $this->usr_id = $usr_id;

        $this->arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
        $this->arr_initial_test_states = ilCrsInitialTestStates::getData($this->usr_ids);
        $this->arr_learn_reached_percentages = ilLearnObjectSuggResults::getData($this->usr_ids);
        $this->arr_iass_states = ilIassStates::getData($this->usr_ids);
        $this->arr_excercise_states = ilExcerciseStates::getData($this->usr_ids, $_GET['ref_id']);
        $this->arr_FinalTestsStates = ilLearnObjectFinalTestStates::getData($this->usr_ids);
        $this->array_obj_ids = ilLearnObjectFinalTestStates::getData($this->usr_ids);

        $this->ctrl->setParameterByClass(ilParticipationCertificateResultModificationGUI::class, 'edited', true);
        $this->ctrl->setParameterByClass(ilParticipationCertificateResultModificationGUI::class, 'ementor', true);
    }


    public function executeCommand(): void
    {
        $nextClass = $this->ctrl->getNextClass();
        switch ($nextClass) {
            case strtolower(ilParticipationCertificateResultGUI::class):
                $ilParticipationCertificateresultGUI = new ilParticipationCertificateResultGUI();
                $this->ctrl->forwardCommand($ilParticipationCertificateresultGUI);
                break;
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_DISPLAY);
                $this->{$cmd}();
                break;
        }
    }

    public function display(): void
    {
        if (method_exists($this->tpl, 'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
            $this->tpl->getStandardTemplate();
        }
        $this->initHeader();
        $form = $this->initForm();
        $this->fillForm($form);

        $this->tpl->setContent($form->getHTML());
        if (method_exists($this->tpl, 'printToStdout')) {
            $this->tpl->printToStdout();
        } else {
            $this->tpl->show();
        }
    }

    public function initHeader(): void
    {
        $this->tpl->setTitle($this->learnGroup->getTitle());
        $this->tpl->setDescription($this->learnGroup->getDescription());
        $this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, 'ref_id');
        $this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(array(
            ilUIPluginRouterGUI::class,
            ilParticipationCertificateResultGUI::class
        ), ilParticipationCertificateResultGUI::CMD_CONTENT));
    }

    public function initForm(): ilPropertyFormGUI
    {
        $usr_id = $_GET[self::IDENTIFIER];
        $arr_usr_data = ilPartCertUsersData::getData($this->usr_ids);
        $nameUser = $arr_usr_data[$usr_id]->getPartCertFirstname() . ' ' . $arr_usr_data[$usr_id]->getPartCertLastname();

        $form = new ilPropertyFormGUI();
        $form->setPreventDoubleSubmission(false);
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if ($cert_access->hasCurrentUserWriteAccess()) {
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
        } else {
            $this->tpl->setOnScreenMessage('failure','No Access Permissions', true);
        }
        return $form;
    }

    public function save(): void
    {
        $form = $this->initForm();

        if (!$form->checkInput()) {
            //TODO error message plus redirect
            return;
        }
    }

    public function fillForm(&$form): void
    {
        $usr_id = $_GET[self::IDENTIFIER];

        if (key_exists($usr_id, $this->arr_initial_test_states) && is_object($this->arr_initial_test_states[$usr_id])) {
            $array['initial'] = $this->arr_initial_test_states[$usr_id]->getCrsitestItestSubmitted();
        } else {
            $array['initial'] = 0;
        }
        if (key_exists($usr_id, $this->arr_learn_reached_percentages) && is_object($this->arr_learn_reached_percentages[$usr_id])) {
            $array['resultstest'] = $this->arr_learn_reached_percentages[$usr_id]->getAveragePercentage(ilParticipationCertificateConfig::getConfig('calculation_type_processing_state_suggested_objectives', $_GET['ref_id']));
        } else {
            $array['resultstest'] = 0;
        }
        if (key_exists($usr_id, $this->arr_iass_states) && is_object($this->arr_iass_states[$usr_id])) {
            $array['conf'] = $this->arr_iass_states[$usr_id]->getPassed();
        } else {
            $array['conf'] = 0;
        }
        if (key_exists($usr_id, $this->arr_excercise_states) && is_object($this->arr_excercise_states[$usr_id])) {
            $array['homework'] = $this->arr_excercise_states[$usr_id]->getPassedPercentage();
        } else {
            $array['homework'] = 0;
        }
        $form->setValuesbyArray($array);
    }

    public function printPDF(): void
    {
        $form = $this->initForm();
        $form->setValuesByPost();
        $form->checkInput();

        $array = array($form->getInput('initial'), $form->getInput('resultstest'), $form->getInput('conf'), $form->getInput('homework'));
        $ementor = $_GET['ementor'];
        $edited = $_GET['edited'];
        $usr_id[] = $this->usr_id;
        $twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, $ementor, $edited, $array);
        $twigParser->parseData();
    }
}