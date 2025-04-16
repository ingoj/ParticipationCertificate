<?php

/**
 * Class ilParticipationCertificateResultGUI
 * @ilCtrl_isCalledBy ilParticipationCertificateResultGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls      ilParticipationCertificateResultGUI: ilParticipationCertificateSingleResultGUI, ilParticipationCertificateGUI, ilParticipationCertificateResultModificationGUI
 */
class ilParticipationCertificateResultGUI
{
    const CMD_CONTENT = 'content';
    const CMD_OVERVIEW = 'overview';
    const CMD_PRINT_PDF = 'printpdf';
    const CMD_PRINT_SELECTED_WITHOUTE_MENTORING = 'printSelectedWithouteMentoring';
    const CMD_PRINT_SELECTED = 'printSelected';
    const CMD_INIT_TABLE = 'initTable';
    protected ilTemplate|ilGlobalTemplateInterface $tpl;
    protected ilCtrl|ilCtrlInterface $ctrl;
    protected ilTabsGUI $tabs;
    protected ilToolbarGUI $toolbar;
    protected ilParticipationCertificatePlugin $pl;
    protected int $groupRefId;
    protected ?ilObject $learnGroup;
    protected ilParticipationCertificateAccess $cert_access;
    protected ilLanguage $lng;

    public function __construct()
    {
        global $DIC;

        $this->toolbar = $DIC->toolbar();
        $this->tabs = $DIC->tabs();
        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilParticipationCertificatePlugin::getInstance();
        $this->groupRefId = (int)$_GET['ref_id'];
        $this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
        $this->lng = $DIC->language();
        $ementoring = ilParticipationCertificateConfig::getConfig('enable_ementoring', $this->groupRefId);
        if ($ementoring === NULL) {
            $ementoring = true;
        } else {
            $ementoring = boolval($ementoring);
        }
        $this->ementoring = $ementoring;
        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, ['ref_id', 'group_id']);
    }

    public function executeCommand(): void
    {
        $nextClass = $this->ctrl->getNextClass();

        switch ($nextClass) {
            case strtolower(ilParticipationCertificateResultModificationGUI::class):
                $ilparticipationcertificateresultmodificationgui = new ilParticipationCertificateResultModificationGUI();
                $ret1 = $this->ctrl->forwardCommand($ilparticipationcertificateresultmodificationgui);
                break;
            case strtolower(ilParticipationCertificateGUI::class):
                $ilParticipationCertificateGUI = new ilParticipationCertificateGUI();
                $ret2 = $this->ctrl->forwardCommand($ilParticipationCertificateGUI);
                $this->tabs->activateTab(self::CMD_OVERVIEW);
                break;
            case strtolower(ilparticipationcertificatesingleresultgui::class):
                $ilparticipationcertificateresultoverviewgui = new ilParticipationCertificateSingleResultGUI();
                $ret3 = $this->ctrl->forwardCommand($ilparticipationcertificateresultoverviewgui);
                break;
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_CONTENT);
                $this->tabs->activateTab(self::CMD_OVERVIEW);

                switch ($cmd) {
                    case ilParticipationCertificateMultipleResultGUI::CMD_SHOW_ALL_RESULTS:
                        $this->ctrl->forwardCommand(new ilParticipationCertificateMultipleResultGUI());
                        break;
                    case self::CMD_PRINT_PDF:
                    case self::CMD_PRINT_SELECTED:
                    case self::CMD_PRINT_SELECTED_WITHOUTE_MENTORING:
                        $this->{$cmd}();
                        break;
                    default:
                        $this->{$cmd}();
                        break;
                }
                break;
        }
    }

    public function content(): void
    {
        global $DIC;
        $renderer = $DIC->ui()->renderer();

        $this->tpl->addCss($this->pl->getDirectory() . '/templates/css/participation-certificate.css');

        if (method_exists($this->tpl, 'loadStandardTemplate')) {
            $this->tpl->loadStandardTemplate();
        } else {
            $this->tpl->getStandardTemplate();
        }
        $this->initHeader();

        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        $toolbar = $DIC->toolbar();
        $ui = $DIC->ui()->factory();

        if ($cert_access->hasCurrentUserPrintAccess()) {
            if ($this->ementoring) {
                $this->ctrl->setParameter($this, 'ementor', true);
                $toolbarButton = $ui->button()->standard(
                    $this->pl->txt('header_btn_print_is_ementoring'),
                    $this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF)
                );
                $this->toolbar->addComponent($toolbarButton);

                $this->ctrl->setParameter($this, 'ementor', false);
                $toolbarButton = $ui->button()->standard(
                    $this->pl->txt('header_btn_print_no_ementoring'),
                    $this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF)
                );
                $this->toolbar->addComponent($toolbarButton);
            } else {
                $this->ctrl->setParameter($this, 'ementor', false);
                $toolbarButton = $ui->button()->standard(
                    $this->pl->txt('header_btn_print'),
                    $this->ctrl->getLinkTarget($this, $this::CMD_PRINT_PDF)
                );
                $this->toolbar->addComponent($toolbarButton);
            }
        }
        $target_ref = 0;
        if ($cert_access->isSelfPrintEnabled() and !$cert_access->hasCurrentUserPrintAccess()) {
			$global_config_sets = ilParticipationCertificateConfig::where(array("config_type"=>3, "global_config_id" => 0 ))->orderBy('order_by')->get();
			foreach ($global_config_sets as $config) {
				if ($config->getConfigKey() == "true_name_helper") {
					$target_ref=$config->getConfigValue();
				}
			}
            $this->tpl->setOnScreenMessage('failure',$this->pl->txt('noname_noprint'), true);
			if (is_numeric($target_ref) and ($target_ref > 0) and (ilObject::_lookupType(ilObject::_lookupObjectId($target_ref),false) == 'xudf')) {
                $msgurl= ' <a href="ilias.php?baseClass=ilDashboardGUI&cmd=jumpToProfile">' .  $this->pl->txt('helper_name') . '</a>';
                $msgadd= $this->pl->txt('helper_action_pre') . $msgurl . $this->pl->txt('helper_action_post');
                $this->tpl->setOnScreenMessage('info',$msgadd, true);
				//Variants sendQuestion, send Info or unified Failure (with some codechange). two same not possible
            }
        }

        $filterHtml = '';
        if ($cert_access->hasCurrentUserWriteAccess()) {
            $filterHtml .= $renderer->render($this->buildFilter());
        }

        $table = $this->initTable();
        $tableHtml = $renderer->render($table->withRequest($DIC->http()->request()));


        $this->tpl->setContent($filterHtml . $tableHtml);
        /*$this->tpl->setContent($this->table->getHTML());*/
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

        $this->ctrl->setParameterByClass(ilRepositoryGUI::class, 'ref_id', (int)$_GET['ref_id']);
        $this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(array(
            ilRepositoryGUI::class//,
            //ilObjGroupGUI::class
        )));
        $this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, ['ref_id', 'group_id']);
        $this->ctrl->saveParameterByClass(ilParticipationCertificateGUI::class, 'ref_id');

        $this->tabs->addTab(self::CMD_OVERVIEW, $this->pl->txt('header_overview'),
            $this->ctrl->getLinkTargetByClass(self::class, self::CMD_CONTENT));
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if ($cert_access->hasCurrentUserAdminAccess()) {
            $this->tabs->addTab(ilParticipationCertificateGUI::TAB_CONFIG, $this->pl->txt('header_config'),
                $this->ctrl->getLinkTargetByClass(ilParticipationCertificateGUI::class,
                    ilParticipationCertificateGUI::CMD_CONFIG));
        }
        $this->tabs->activateTab(self::CMD_OVERVIEW);
    }


    protected function initTable(bool $override = false)
    {
        $repo = new ilParticipationCertificateResultTableNewGUI();
        return $repo->getTableForRepresentation();
    }

    /**
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function action()
    {
        $action = $_GET['config_action'];

        if (!empty($action)) {
            switch ($action) {
                case 'print_with_ementorining':
                case 'print_without_ementorining':
                    $this->printPdf();
                    break;

                case 'show_all_results':
                    $singleResultGui = new ilParticipationCertificateSingleResultGUI();
                    $singleResultGui->display();
                    break;

                case 'adjust_results':
                    $resultModificationGui = new ilParticipationCertificateResultModificationGUI();
                    $resultModificationGui->display();
                    break;
            }
        }
    }

    /**
     * @throws ilCtrlException
     */
    public function printPdf(): void
    {
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if ($cert_access->hasCurrentUserPrintAccess()) {
            $ementor = false;
            $usr_id = [];
            if (!empty($_GET['config_entry'])) {
                $urlParameters = $this->excludeURLParameters($_GET['config_entry'][0]);
                $userId = $urlParameters[0];
                $ementor = (bool) $urlParameters[1];
                $usr_id[] = $userId;
            } else {
                $cert_access = new ilParticipationCertificateAccess($this->groupRefId);
                $userIds = $cert_access->getUserIdsOfGroup();
                if (empty($usr_id)) {
                    $usr_id = $userIds;
                }
            }

            if (!empty($usr_id)) {
                $arr_usr_data = ilPartCertUsersData::getData($this->pl, $usr_id);
                $usr_id = $this->excludeUserIfDataMissing($usr_id, $arr_usr_data);
            }

            // Redirect if selected user's data or all users' data are missing
            if (empty($usr_id)) {
                $this->redirectWithError(self::CMD_CONTENT, $this->pl->txt('user_data_missing'));
            }

            $twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, $ementor,
                false);
            $twigParser->parseData();
        } else {
            $this->tpl->setOnScreenMessage('failure',$this->lng->txt('no_permission'), true);
            ilUtil::redirect('login.php');
        }
    }

    /**
     * @throws arException
     * @throws \Twig\Error\SyntaxError
     * @throws ilCtrlException
     * @throws \Twig\Error\LoaderError
     * @throws ilDateTimeException
     */
    public function printSelected(): void
    {
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if ($cert_access->hasCurrentUserPrintAccess()) {
            if (!isset($_POST['record_ids']) || (isset($_POST['record_ids']) && !count($_POST['record_ids']))) {
                $this->tpl->setOnScreenMessage('failure',$this->lng->txt('no_records_selected'), true);
                $this->ctrl->redirect($this, self::CMD_CONTENT);
            }
            $usr_ids = $_POST['record_ids'];
            if (!is_array($usr_ids)) {
                $usr_id[] = $usr_ids;
            } else {
                $usr_id = $usr_ids;
            }


            $arr_usr_data = ilPartCertUsersData::getData($this->pl, $usr_id);
            $usr_id = $this->excludeUserIfDataMissing($usr_id, $arr_usr_data);

            if(empty($usr_id)) {
                $this->redirectWithError(self::CMD_CONTENT, $this->pl->txt('all_user_data_missing'));
            }

            $twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), (array) $usr_id, true, false);
            $twigParser->parseData();
        } else {
            $this->tpl->setOnScreenMessage('failure',$this->lng->txt('no_permission'), true);
            ilUtil::redirect('login.php');
        }
    }

    /**
     * @throws arException
     * @throws ilCtrlException
     * @throws \Twig\Error\SyntaxError
     * @throws ilDateTimeException
     * @throws \Twig\Error\LoaderError
     */
    public function printSelectedWithouteMentoring(): void
    {
        $cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
        if ($cert_access->hasCurrentUserPrintAccess()) {
            if (!isset($_POST['record_ids']) || (isset($_POST['record_ids']) && !count($_POST['record_ids']))) {
                $this->tpl->setOnScreenMessage('failure',$this->lng->txt('no_records_selected'), true);
                $this->ctrl->redirect($this, self::CMD_CONTENT);
            }

            $usr_ids = $_POST['record_ids'];
            if (!is_array($usr_ids)) {
                $usr_id[] = $usr_ids;
            } else {
                $usr_id = $usr_ids;
            }

            $arr_usr_data = ilPartCertUsersData::getData($this->pl, $usr_id);
            $usr_id = $this->excludeUserIfDataMissing($usr_id, $arr_usr_data);

            if(empty($usr_id)) {
                $this->redirectWithError(self::CMD_CONTENT, $this->pl->txt('all_user_data_missing'));
            }

            $twigParser = new ilParticipationCertificateTwigParser($this->groupRefId, array(), $usr_id, false, false);
            $twigParser->parseData();
        } else {
            $this->tpl->setOnScreenMessage('failure',$this->lng->txt('no_permission'), true);
            ilUtil::redirect('login.php');
        }
    }

    public function applyFilter(): void
    {
        $table = new ilParticipationCertificateResultTableGUI($this, self::CMD_CONTENT);
        $table->writeFilterToSession();
        $table->resetOffset();
        $this->ctrl->redirect($this, self::CMD_CONTENT);
    }

    public function resetFilter(): void
    {
        $table = new ilParticipationCertificateResultTableGUI($this, self::CMD_CONTENT);
        $table->resetOffset();
        $table->resetFilter();
        $this->ctrl->redirect($this, self::CMD_CONTENT);
    }

    /**
     * @param string $cmd
     * @param string $msg
     * @return void
     * @throws ilCtrlException
     */
    private function redirectWithError(string $cmd, string $msg): void
    {
        $this->tpl->setOnScreenMessage('failure', $msg, true);
        $this->ctrl->redirect($this, $cmd);
    }

    /**
     * @param array $usr_id
     * @param array $arr_usr_data
     * @return array
     */
    private function excludeUserIfDataMissing(array $usr_id, array $arr_usr_data): array
    {
        $user_data = new ilPartCertUserData();
        foreach ($usr_id as $key => $id) {
            if(!$user_data->checkIfUserDataFilled(
                $arr_usr_data[$id]->getPartCertSalutation(),
                $arr_usr_data[$id]->getPartCertFirstname(),
                $arr_usr_data[$id]->getPartCertLastname()
            )) {
                unset($usr_id[$key]);
            }
        }
        return array_values($usr_id);
    }

    private function buildFilter()
    {
        global $DIC;
        $ui = $DIC->ui()->factory();
        $renderer = $DIC->ui()->renderer();

        $inputFirstname = $ui->input()->field()->text('Firstname');
        $inputLastname = $ui->input()->field()->text('Lastname');

        $action = $DIC->ctrl()->getLinkTargetByClass(
            self::class,
            'content',
            "",
            true
        );
        $filter = $DIC->uiService()->filter()->standard(
            'filter_ID',
            $action,
            [
                'firstname' => $inputFirstname,
                'lastname' => $inputLastname,
            ],
            [true, true],
            true,
            true,
        );

        return $filter;

       /* //Step 3: Get filter data
        $filter_data = $DIC->uiService()->filter()->getData($filter);

        //Step 4: Render the filter
        return $renderer->render($filter) . "Filter Data: " . print_r($filter_data, true);
    */}

    /**
     * @param string $parameter
     * @return string[]
     */
    private function excludeURLParameters(string $parameter): array
    {
        return explode('_', $parameter);
    }
}
