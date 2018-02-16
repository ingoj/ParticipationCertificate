<?php
require_once './Services/Form/classes/class.ilPropertyFormGUI.php';
require_once './Services/Object/classes/class.ilObjectListGUIFactory.php';
require_once './Modules/Course/classes/class.ilObjCourseGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfigGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Report/class.ilParticipationCertificatePDFGenerator.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Report/class.ilParticipationCertificateTwigParser.php';
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateAccess.php";
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Report/class.ilParticipationCertificateTwigParser.php";
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Table/class.ilParticipationCertificateResultGUI.php';
require_once 'Services/Form/classes/class.ilDateDurationInputGUI.php';

/**
 * Class ilParticipationCertificateGUI
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_Calls      ilParticipationCertificateGUI: ilParticipationCertificateResultGUI
 */
class ilParticipationCertificateGUI {

	const CMD_SAVE = 'save';
	const CMD_CANCEL = 'cancel';
	const CMD_LOOP = 'loop';
	const CMD_CONFIG = 'config';
	const CMD_PERIOD = 'period';
	const CMD_PERIOD_SAVE = 'savePeriod';
	const CMD_SELF_PRINT = 'selfPrint';
	const CMD_SELF_PRINT_SAVE = 'saveSelfPrint';
	const CMD_DISPLAY = 'display';
	const CMD_RESET_CERT_TEXT = 'resetCertText';
	/*const CMD_PRINT_PDF = 'printPdf';
	const CMD_PRINT_PDF_WITHOUT_MENTORING = 'printPdfWithoutMentoring';*/
	const TAB_CONFIG = 'config';
	const TAB_CONFIG_DISPLAY = 'config_display';
	const TAB_CONFIG_PERIOD = 'config_period';
	const TAB_CONFIG_SELF_PRINT = 'config_self_print';
	/**
	 * @var ilTemplate
	 */
	public $tpl;
	/**
	 * @var ilCtrl
	 */
	public $ctrl;
	/**
	 * @var ilTabsGUI
	 */
	public $tabs;
	/**
	 * @var ilGroupParticipants
	 */
	public $learnGroupParticipants;
	/**
	 * @var ilObjGroup
	 */
	public $learningGroup;
	/**
	 * @var ilParticipationCertificateConfig
	 */
	public $object;
	/**
	 * @var ilToolbarGUI
	 */
	public $toolbar;
	/**
	 * @var int
	 */
	public $groupRefId;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;


	function __construct() {
		global $ilCtrl, $tpl, $ilTabs, $objDefinition, $ilToolbar, $lng;

		$this->toolbar = $ilToolbar;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->objectDefinition = $objDefinition;
		$this->groupRefId = (int)$_GET['ref_id'];

		//Access
		$cert_access = new ilParticipationCertificateAccess($this->groupRefId);
		if (!$cert_access->hasCurrentUserWriteAccess()) {
			ilUtil::sendFailure($lng->txt('no_permission'), true);
			ilUtil::redirect('login.php');
		}

		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->learnGroup = ilObjectFactory::getInstanceByRefId($this->groupRefId);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', [ 'ref_id', 'group_id' ]);
	}


	function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		$nextClass = $this->ctrl->getNextClass();

		switch ($nextClass) {
			case 'ilparticipationcertificateresultgui':
				$ilParticipationCertificateTableGUI = new ilParticipationCertificateResultGUI();
				$this->ctrl->forwardCommand($ilParticipationCertificateTableGUI);
				break;
			default:
				switch ($cmd) {
					case self::CMD_CONFIG:
					case self::CMD_PERIOD:
					case self::CMD_DISPLAY:
					case self::CMD_RESET_CERT_TEXT:
					case self::CMD_SAVE:
					case self::CMD_PERIOD_SAVE:
					case self::CMD_SELF_PRINT:
					case self::CMD_SELF_PRINT_SAVE:
						/*case self::CMD_PRINT_PDF:
						case self::CMD_PRINT_PDF_WITHOUT_MENTORING:*/
						$this->{$cmd}();
						break;
					default:
						$this->{$cmd}();
						break;
				}
		}
	}


	protected function config() {
		$this->period();
	}


	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->saveParameterByClass('ilRepositoryGUI', 'ref_id');

		$this->ctrl->setParameterByClass('ilrepositorygui', 'ref_id', $this->groupRefId);
		$this->tabs->setBackTarget($this->pl->txt('header_btn_back'), $this->ctrl->getLinkTargetByClass(array( 'ilrepositorygui', 'ilobjgroupgui' )));

		$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', 'ref_id');
		$this->tabs->addTab(ilParticipationCertificateResultGUI::CMD_OVERVIEW, $this->pl->txt('header_overview'), $this->ctrl->getLinkTargetByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_CONTENT));

		//$this->tabs->addTab(ilParticipationCertificateResultGUI::CMD_OVERVIEW,$this->pl->txt('header_overview'),$this->ctrl->getLinkTargetByClass('ilParticipationCertificateResultGUI',ilParticipationCertificateResultGUI::CMD_CONTENT));
		$this->tabs->addTab(self::TAB_CONFIG, $this->pl->txt('header_config'), $this->ctrl->getLinkTargetByClass(self::class, self::CMD_CONFIG));
		$this->tabs->activateTab(self::TAB_CONFIG);
	}


	protected function initConfTabs() {
		$this->tabs->addSubTab(self::TAB_CONFIG_PERIOD, $this->pl->txt('period'), $this->ctrl->getLinkTarget($this, self::CMD_PERIOD));
		$this->tabs->addSubTab(self::TAB_CONFIG_SELF_PRINT, $this->pl->txt('period_self_print'), $this->ctrl->getLinkTarget($this, self::CMD_SELF_PRINT));
		$this->tabs->addSubTab(self::TAB_CONFIG_DISPLAY, $this->pl->txt('plugin'), $this->ctrl->getLinkTarget($this, self::CMD_DISPLAY));
	}


	protected function display() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$this->initConfTabs();
		$this->tabs->activateSubTab(self::TAB_CONFIG_DISPLAY);

		$form = $this->initform();

		$this->tpl->setContent($form->getHTML());
		$this->tpl->show();
	}


	public function initForm() {
		$form = new ilPropertyFormGUI();

		/*
		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption($this->pl->txt('header_btn_print'), false);
		$b_print->setUrl($this->ctrl->getLinkTarget($this, self::CMD_PRINT_PDF));
		$this->toolbar->addButtonInstance($b_print);

		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption($this->pl->txt('header_btn_print_eMentoring'), false);
		$b_print->setUrl($this->ctrl->getLinkTarget($this, self::CMD_PRINT_PDF_WITHOUT_MENTORING));
		$this->toolbar->addButtonInstance($b_print);
		*/

		$button = ilLinkButton::getInstance();
		$button->setCaption($this->pl->txt('btn_reset'), false);
		$button->setUrl($this->ctrl->getLinkTarget($this, self::CMD_RESET_CERT_TEXT));
		$this->toolbar->addButtonInstance($button);

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->pl->txt('config_plugin'));
		$form->setDescription('Folgende Platzhalter sind verfügbar: <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		&lbrace;&lbrace;date&rbrace;&rbrace;: Datum
		');

		$arr_config = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP,
			"group_ref_id" => $this->groupRefId,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
		))->orderBy('id')->get();
		if (count($arr_config) == 0) {
			$arr_config = ilParticipationCertificateConfig::where(array(
				"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
				"group_ref_id" => 0,
				"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
			))->orderBy('id')->get();
		}

		foreach ($arr_config as $config) {
			/**
			 * @var ilParticipationCertificateConfig $config
			 */
			switch ($config->getConfigKey()) {
				case "page1_issuer_signature":
					$input = new ilFileInputGUI($config->getConfigKey(), $config->getConfigKey());
					$input->setSuffixes(array( 'png' ));
					if (!empty($config->getConfigValue())) {
						$input->setInfo('<img src="' . $config->getConfigValue() . '" />');
					}
					break;

				default:
					$input = new ilTextAreaInputGUI($config->getConfigKey(), $config->getConfigKey());
					$input->setRows(3);
					$input->setValue($config->getConfigValue());
					break;
			}

			$form->addItem($input);
		}

		/*$arr_config_value = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP,
			"group_ref_id" => $this->groupRefId,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			'config_key' => 'percent_value'
		))->first();
		if ($arr_config_value == NULL) {
			$arr_config_value = ilParticipationCertificateConfig::where(array(
				"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
				"group_ref_id" => 0,
				"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
				'config_key' => 'percent_value'
			))->first();
		}
		$percent = new ilNumberInputGUI('Schwellenwert für Videokonferenz (Prozent)','percent_value');
		$percent->setValue($arr_config_value->getConfigValue());
		$form->addItem($percent);*/

		$this->ctrl->saveParameterByClass('ilObjGroup', 'ref_id');
		$form->addCommandButton(self::CMD_SAVE, $this->pl->txt('save'));

		return $form;
	}


	/**
	 * @return bool
	 */
	public function save() {

		$form = $this->initForm();

		if (!$form->checkInput()) {
			//TODO error message plus redirect
			return false;
		}

		//TODO auslagern nach ilParticipationCertificateConfig
		//save Text
		foreach ($form->getItems() as $item) {
			/**
			 * @var ilFormPropertyGUI                $item
			 * @var ilParticipationCertificateConfig $config
			 */
			if ($item->getPostVar() != 'percent_value') {
				$config = ilParticipationCertificateConfig::where(array(
					'config_key' => $item->getPostVar(),
					"group_ref_id" => $this->groupRefId,
					'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP,
					'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
				))->first();
				if (!is_object($config)) {
					$config = new ilParticipationCertificateConfig();
					$config->setGroupRefId($this->groupRefId);
					$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GROUP);
					$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
					$config->setConfigKey($item->getPostVar());
					$config->setConfigValue("");
				}

				$input = $form->getInput($item->getPostVar());

				switch ($config->getConfigKey()) {
					case "page1_issuer_signature":
						if ($input['tmp_name']) {
							/**
							 * @var array $input
							 */
							$input = ilParticipationCertificateConfig::storePicture($input, $this->groupRefId, $config->getConfigKey() . ".png");
						} else {
							// Previous upload
							$input = $config->getConfigValue();
						}
						break;

					default:
						break;
				}

				$config->setConfigValue($input);
				$config->store();
			}
		}

		$config_value = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP,
			"group_ref_id" => $this->groupRefId,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
			'config_key' => 'percent_value'
		))->first();
		if (!is_object($config_value)) {
			$config_value = new ilParticipationCertificateConfig();
			$config_value->setGroupRefId($this->groupRefId);
			$config_value->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GROUP);
			$config_value->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
			$config_value->setConfigKey('percent_value');
		}
		$config_value->setConfigValue($form->getInput('percent_value'));
		$config_value->store();
		ilUtil::sendSuccess($this->pl->txt('successFormSave'), true);

		$this->ctrl->redirect($this, self::CMD_DISPLAY);

		return true;
	}


	/*
	public function printPdf() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId);
		$twigParser->parseData();
	}

	public function printPdfWithoutMentoring() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId,array(),false);
		$twigParser->parseData();
	}
	*/

	public function resetCertText() {
		global $ilCtrl;
		$arr_config = ilParticipationCertificateConfig::where(array(
			"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP,
			"group_ref_id" => $this->groupRefId,
			"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
		))->get();
		if (count($arr_config)) {
			foreach ($arr_config as $config) {
				/**
				 * @var ilParticipationCertificateConfig $config
				 */
				switch ($config->getConfigKey()) {
					case "page1_issuer_signature":
						ilParticipationCertificateConfig::deletePicture($config->getGroupRefId(), $config->getConfigKey() . ".png");
						break;

					default:
						break;
				}

				$config->delete();
			}
		}
		ilUtil::sendSuccess($this->pl->txt('successForm'), true);
		$ilCtrl->redirect($this, self::CMD_DISPLAY);
	}


	protected function period() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$this->initConfTabs();
		$this->tabs->activateSubTab(self::TAB_CONFIG_PERIOD);

		$form = $this->initPeriodForm();

		$this->tpl->setContent($form->getHTML());
		$this->tpl->show();
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function initPeriodForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->pl->txt('period'));

		$period = new ilDateDurationInputGUI($this->pl->txt('period'), 'period');
		$period->setStart(new ilDateTime(ilParticipationCertificateConfig::getConfig('period_start', $this->groupRefId), IL_CAL_DATE));
		$period->setEnd(new ilDateTime(ilParticipationCertificateConfig::getConfig('period_end', $this->groupRefId), IL_CAL_DATE));
		$form->addItem($period);

		$form->addCommandButton(self::CMD_PERIOD_SAVE, $this->pl->txt('save'));

		return $form;
	}


	protected function savePeriod() {
		$form = $this->initPeriodForm();

		if (!$form->checkInput()) {
			//TODO error message plus redirect
			return;
		}

		$period = $form->getInput('period');
		ilParticipationCertificateConfig::setConfig('period_start', $period['start'], $this->groupRefId);
		ilParticipationCertificateConfig::setConfig('period_end', $period['end'], $this->groupRefId);

		ilUtil::sendSuccess($this->pl->txt('successFormSave'), true);

		$this->ctrl->redirect($this, self::CMD_PERIOD);
	}


	protected function selfPrint() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$this->initConfTabs();
		$this->tabs->activateSubTab(self::TAB_CONFIG_SELF_PRINT);

		$form = $this->initSelfPrintForm();

		$this->tpl->setContent($form->getHTML());
		$this->tpl->show();
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function initSelfPrintForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->pl->txt('period_self_print'));

		$enable = new ilCheckboxInputGUI($this->pl->txt('enable_self_print'), 'enable_self_print');
		$enable->setChecked(boolval(ilParticipationCertificateConfig::getConfig('self_print_enable', $this->groupRefId)));
		$form->addItem($enable);

		$period = new ilDateDurationInputGUI($this->pl->txt('period'), 'period_self_print');
		$period->setStart(new ilDateTime(ilParticipationCertificateConfig::getConfig('self_print_start', $this->groupRefId), IL_CAL_DATE));
		$period->setEnd(new ilDateTime(ilParticipationCertificateConfig::getConfig('self_print_end', $this->groupRefId), IL_CAL_DATE));
		$enable->addSubItem($period);

		$form->addCommandButton(self::CMD_SELF_PRINT_SAVE, $this->pl->txt('save'));

		return $form;
	}


	protected function saveSelfPrint() {
		$form = $this->initSelfPrintForm();

		if (!$form->checkInput()) {
			//TODO error message plus redirect
			return;
		}

		$enable = boolval($form->getInput("enable_self_print"));
		ilParticipationCertificateConfig::setConfig('enable_self_print', $enable, $this->groupRefId);

		$period = $form->getInput('period_self_print');
		ilParticipationCertificateConfig::setConfig('self_print_start', $period['start'], $this->groupRefId);
		ilParticipationCertificateConfig::setConfig('self_print_end', $period['end'], $this->groupRefId);

		ilUtil::sendSuccess($this->pl->txt('successFormSave'), true);

		$this->ctrl->redirect($this, self::CMD_SELF_PRINT);
	}
}

?>