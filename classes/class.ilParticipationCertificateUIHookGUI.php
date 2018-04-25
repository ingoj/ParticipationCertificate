<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilParticipationCertificateUIHookGUI
 *
 * @author       Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_Calls ilParticipationCertificateUIHookGUI: ilParticipationCertificateGUI
 */
class ilParticipationCertificateUIHookGUI extends ilUIHookPluginGUI {

	const TAB_CERTIFICATES = "certificates";
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var int
	 */
	protected $groupRefId;
	/**
	 * @var
	 */
	protected $learnGroup;
	/**
	 * @var string
	 */
	protected $learnGroupTitle;
	/**
	 * @var string
	 */
	protected $keyword;


	public function __construct() {
		global $DIC;


		if($DIC->offsetExists('tpl')) {
			$this->ctrl = $DIC->ctrl();
			$this->pl = ilParticipationCertificatePlugin::getInstance();
			$this->groupRefId = (int)$_GET['ref_id'];

			if ($this->groupRefId == !0 && $this->groupRefId == !NULL) {
				$this->learnGroup = ilObjectFactory::getInstanceByRefId($this->groupRefId);
				$this->learnGroupTitle = $this->learnGroup->getTitle();
			}

			try {
				$config = ilParticipationCertificateConfig::where(array(
					'config_key' => 'keyword',
					'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
					'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
					"group_ref_id" => 0
				))->first();
				$this->keyword = $config->getConfigValue();
			} catch (Exception $ex) {
				// Fix uninstall (Table not found)
			}
		}

	}


	/**
	 *
	 * Modify GUI objects, before they generate output
	 *
	 * @param string $a_comp
	 * @param string $a_part
	 * @param array  $a_par
	 */

	function modifyGUI($a_comp, $a_part, $a_par = array()) {
		if ($a_part == 'tabs' && $this->checkGroup()) {

			$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);

			if ($cert_access->hasCurrentUserWriteAccess()) {
				/**
				 * @var ilTabsGUI $tabs
				 */
				$tabs = $a_par["tabs"];
				$this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, 'ref_id');
				$tabs->addTab(self::TAB_CERTIFICATES, $this->pl->txt('plugin'), $this->ctrl->getLinkTargetByClass(array(
					ilUIPluginRouterGUI::class,
					ilParticipationCertificateResultGUI::class
				), ilParticipationCertificateResultGUI::CMD_CONTENT));
			} else {
				/**
				 * @var ilTabsGUI $tabs
				 */
				$tabs = $a_par["tabs"];
				$this->ctrl->saveParameterByClass(ilParticipationCertificateResultGUI::class, 'ref_id');
				$tabs->addTab(self::TAB_CERTIFICATES, $this->pl->txt('pluginreader'), $this->ctrl->getLinkTargetByClass(array(
					ilUIPluginRouterGUI::class,
					ilParticipationCertificateResultGUI::class
				), ilParticipationCertificateResultGUI::CMD_CONTENT));
			}
		}
	}


	/**
	 * @return bool
	 * check if tab should be displayed, only displayed in groups!
	 */
	function checkGroup() {
		foreach ($this->ctrl->getCallHistory() as $GUIClassesArray) {
			if ($GUIClassesArray['class'] == ilObjGroupGUI::class) {
				if (stripos($this->learnGroupTitle, $this->keyword) !== false) {
					return true;
				}
			}
		}

		return false;
	}
}

?>