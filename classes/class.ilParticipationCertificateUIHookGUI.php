<?php
require_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateGUI.php';
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateAccess.php";

/**
 * Class ilParticipationCertificateUIHookGUI
 *
 * @author       Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_Calls ilParticipationCertificateUIHookGUI: ilParticipationCertificateGUI
 */
class ilParticipationCertificateUIHookGUI extends ilUIHookPluginGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;


	public function __construct() {
		global $ilCtrl;
		$this->ctrl = $ilCtrl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->groupRefId = (int)$_GET['ref_id'];

		if($this->groupRefId ==! 0 && $this->groupRefId ==! NULL) {
			$this->learnGroup = ilObjectFactory::getInstanceByRefId($this->groupRefId);
			$this->learnGroupTitle = $this->learnGroup->getTitle();
		}

		$config = ilParticipationCertificateConfig::where(array(
			'config_key' => 'keyword',
			'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER
		))->first();
		$this->keyword = $config->getConfigValue();


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
		global $ilUser;


		if ($a_part == 'tabs' && $this->checkGroup()) {

			$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);

			if ($cert_access->hasCurrentUserPrintAccess()) {
				/**
				 * @var ilTabsGUI $tabs
				 */
				$tabs = $a_par["tabs"];
				$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', 'ref_id');
				$tabs->addTab('certificates', $this->pl->txt('plugin'), $this->ctrl->getLinkTargetByClass(array(
					'ilUIPluginRouterGUI',
					'ilParticipationCertificateResultGUI'
				), ilParticipationCertificateResultGUI::CMD_CONTENT));
			}
			else{
				/**
				 * @var ilTabsGUI $tabs
				 */
				$tabs = $a_par["tabs"];
				$this->ctrl->saveParameterByClass('ilParticipationCertificateResultGUI', 'ref_id');
				$tabs->addTab('certificates', $this->pl->txt('pluginreader'), $this->ctrl->getLinkTargetByClass(array(
					'ilUIPluginRouterGUI',
					'ilParticipationCertificateResultGUI'
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
			if ($GUIClassesArray['class'] == 'ilObjGroupGUI') {
				if(stripos($this->learnGroupTitle,$this->keyword) !== false) {
					return true;
				}
			}
		}

		return false;
	}
}
?>