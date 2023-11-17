<?php

/**
 * @ilCtrl_isCalledBy ilParticipationCertificateSingleResultGUI: ilUIPluginRouterGUI
 */
class ilParticipationCertificateSingleResultGUI
{

    const CMD_DISPLAY = 'display';
    const IDENTIFIER = 'usr_id';
    public ilTabsGUI $tabs;
    protected ilTemplate|ilGlobalTemplateInterface $tpl;
    protected ilCtrl|ilCtrlInterface $ctrl;
    protected ilParticipationCertificatePlugin $pl;
    protected ilToolbarGUI $toolbar;
    protected ?ilObject $learnGroup;
    protected array $usr_ids;
    protected int $usr_id;
    /**
     * @var ilPartCertUserData[]
     */
    protected array $arr_usr_data;
    /**
     * @var ilCrsInitialTestState[]
     */
    protected array $arr_initial_test_states;
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
    protected ilParticipationCertificateSingleResultTableGUI $table;


    public function __construct()
    {
        global $DIC;

        $this->toolbar = $DIC->toolbar();
        $this->ctrl = $DIC->ctrl();
        $this->tabs = $DIC->tabs();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilParticipationCertificatePlugin::getInstance();
        $this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);

        $this->ctrl->saveParameterByClass(ilParticipationCertificateUIHookGUI::class, ['ref_id', 'group_id']);
        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultModificationGUI::class, ['ref_id', 'group_id']);
        $this->ctrl->saveParameterByClass(ilParticipationCertificateUIHookGUI::class, 'usr_id');

        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        $this->usr_ids = $cert_access->getUserIdsOfGroup();

        $usr_id = $_GET[self::IDENTIFIER];
        $this->usr_id = $usr_id;


    }

    public function executeCommand(): void
    {
        $nextClass = $this->ctrl->getNextClass();
        switch ($nextClass) {
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
        $this->tpl->addCss($this->pl->getDirectory() . '/Templates/css/table.css');
        $this->initHeader();

        $this->initTable();

        $this->tpl->setContent($this->table->getHTML());
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
        $this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_CONTENT));
    }
    public function initTable($override = false): void
    {
        $this->table = new ilParticipationCertificateSingleResultTableGUI($this, ilParticipationCertificateSingleResultGUI::CMD_DISPLAY, $_GET[ilParticipationCertificateSingleResultGUI::IDENTIFIER]);
    }
}