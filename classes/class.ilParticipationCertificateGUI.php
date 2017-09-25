<?php
//TODO prüfen, ob execute command noch stimmig.
require_once './Services/Form/classes/class.ilPropertyFormGUI.php';
require_once './Services/Object/classes/class.ilObjectListGUIFactory.php';
require_once './Modules/Course/classes/class.ilObjCourseGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfigGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Report/class.ilParticipationCertificatePDFGenerator.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Report/class.ilParticipationCertificateTwigParser.php';
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateAccess.php";
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/Report/class.ilParticipationCertificateTwigParser.php";

/**
 * Class ilParticipationCertificateGUI
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilParticipationCertificateGUI: ilUIPluginRouterGUI, ilParticipationHookGUI ilParticipationCertificatePDFGenerator
 */
class ilParticipationCertificateGUI {

	const CMD_DISPLAY = 'display';
	const CMD_SAVE = 'save';
	const CMD_CANCEL = 'cancel';
	const CMD_LOOP = 'loop';
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


	function __construct() {
		global $ilCtrl, $tpl, $ilTabs, $objDefinition, $ilToolbar, $lng;

		$this->toolbar = $ilToolbar;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->objectDefinition = $objDefinition;
		$this->groupRefId = (int)$_GET['ref_id'];
		$this->groupObjId = ilObject2::_lookupObjectId($this->groupRefId);

		//Access
		$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);
		if (!$cert_access->hasCurrentUserPrintAccess()) {
			ilUtil::sendFailure($lng->txt('no_permission'), true);
			ilUtil::redirect('login.php');
		}


		$this->learnGroup = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);
		$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI', [ 'ref_id', 'group_id' ]);
	}


	function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			case 'ilparticipationcertificatepdfgenerator':
				$ilParticipationCertificatePDFGenerator = new ilParticipationCertificatePDFGenerator();
				$ret = $this->ctrl->forwardCommand($ilParticipationCertificatePDFGenerator);
				break;
			case 'ilparticipationcertificatetwigparser':
				$ilParticipationCertificateTwigParser = new ilParticipationCertificateTwigParser($this->groupRefId);
				$ret1 = $this->ctrl->forwardCommand($ilParticipationCertificateTwigParser);
				break;
			case 'ilparticipationcertificategui':
				$ilParticipationCertificateGUI = new ilParticipationCertificateGUI();

			default:
				$this->{$cmd}();
		}
	}


	protected function display() {
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$form = $this->initform();

		$this->tpl->setContent($form->getHTML());
		$this->tpl->show();
	}


	function initHeader() {
		$this->tpl->setTitle($this->learnGroup->getTitle());
		$this->tpl->setDescription($this->learnGroup->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->learnGroup->getId()));

		$this->ctrl->saveParameterByClass('ilRepositoryGUI', 'ref_id');
		$this->tabs->setBackTarget('Zurück', $this->ctrl->getLinkTargetByClass('ilRepositoryGUI'));
	}


	public function initForm() {
		$form = new ilPropertyFormGUI();

		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption('Bescheinigung Drucken');
		$b_print->setUrl($this->ctrl->getLinkTarget($this, 'printPdf'));
		$this->toolbar->addButtonInstance($b_print);

		$b_print = ilLinkButton::getInstance();
		$b_print->setCaption('Bescheinigung Drucken (exkl. eMentoring)');
		$b_print->setUrl($this->ctrl->getLinkTarget($this, 'printPdfWithoutMentoring'));
		$this->toolbar->addButtonInstance($b_print);


		$button = ilLinkButton::getInstance();
		$button->setCaption('Textwerte für das Formular zurücksetzen');
		$button->setUrl($this->ctrl->getLinkTarget($this, 'resetCertText'));
		$this->toolbar->addButtonInstance($button);

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle('Konfiguration Teilnahmebescheinigung');
		$form->setDescription('Folgende Platzhalter sind verfügbar: <br>
		&lbrace;&lbrace;username&rbrace;&rbrace;: Anrede Vorname Nachname <br>
		&lbrace;&lbrace;date&rbrace;&rbrace;: Datum
		');

		$arr_config = ilParticipationCertificateConfig::where(array("config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP, "group_ref_id" => $this->groupRefId, "config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT))->orderBy('order_by')->get();
		if(count($arr_config) == 0) {
			$arr_config = ilParticipationCertificateConfig::where(array("config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, "config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT))->orderBy('order_by')->get();
		}

		foreach($arr_config as $config) {
			/**
			 * @var ilParticipationCertificateConfig $config
			 */
			$input = new ilTextAreaInputGUI($config->getConfigKey(), $config->getConfigKey());
			$input->setRows(3);
			$input->setValue($config->getConfigValue());
			$form->addItem($input);
		}

		$uploadfield = new ilFileInputGUI('Logo', 'headerpic');
		$uploadfield->setSuffixes(array( 'png' ));

		if(is_file(ilParticipationCertificateConfig::returnPicturePath('absolute',$this->groupRefId))) {
			//group specifig
			$uploadfield->setInfo('<img src="'.ilParticipationCertificateConfig::returnPicturePath('relative',$this->groupRefId).'" />');
		} elseif(is_file(ilParticipationCertificateConfig::returnPicturePath('absolute',0))) {
			//global
			$uploadfield->setInfo('<img src="'.ilParticipationCertificateConfig::returnPicturePath('relative',0).'" />');
		}


		$form->addItem($uploadfield);

		$this->ctrl->saveParameterByClass('ilObjGroup', 'ref_id');
		$form->addCommandButton(ilParticipationCertificateGUI::CMD_SAVE, 'Speichern');

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

		$i = 1;
		foreach($form->getItems() as $item) {
			//todo refactor
			if($item->getPostVar() == 'headerpic') {
				continue;
			}
			/**
			 * @var ilParticipationCertificateConfig $config;
			 */
			$config = ilParticipationCertificateConfig::where(array('config_key' =>  $item->getPostVar(), 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT))->first();

			if(!is_object($config)) {
				$config = new ilParticipationCertificateConfig();
				$config->setGroupRefId($this->groupRefId);
				$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GROUP);
				$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
				$config->setConfigKey( $item->getPostVar());
			}
			$config->setOrderBy($i);
			$config->setConfigValue($form->getInput($item->getPostVar()));
			$config->store();

			$i = $i+1;
		}

		//Picture
		$file_data = $form->getInput('headerpic');
		if($file_data['tmp_name']) {
			ilParticipationCertificateConfig::storePicture($file_data,$this->groupRefId);
		}

		$this->ctrl->redirect($this, 'display');
		return true;
	}

	public function printPdf() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId);
		$twigParser->parseData();
	}

	public function printPdfWithoutMentoring() {
		$twigParser = new ilParticipationCertificateTwigParser($this->groupRefId,array(),false);
		$twigParser->parseData();
	}

	public function resetCertText() {
		global $ilCtrl;
		$arr_config = ilParticipationCertificateConfig::where(array("config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP, "group_ref_id" => $this->groupRefId, "config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT))->get();
		if(count($arr_config)) {
			foreach($arr_config as $config) {
				$config->delete();
			}
		}

		if(is_file(ilParticipationCertificateConfig::returnPicturePath('absolute',$this->groupRefId))) {
			unlink(ilParticipationCertificateConfig::returnPicturePath('absolute',$this->groupRefId));
		}

		$ilCtrl->redirect($this,self::CMD_DISPLAY);
	}

}