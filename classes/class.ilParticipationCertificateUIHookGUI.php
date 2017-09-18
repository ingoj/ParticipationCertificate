<?php
include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateGUI.php';
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateAccess.php";
/**
 * Class ilParticipationCertificateUIHookGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateUIHookGUI extends ilUIHookPluginGUI{

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;



	public function __construct() {
		global $ilCtrl;
		$this->ctrl = $ilCtrl;
	}

	/**
	 *
	 * Modify GUI objects, before they generate output
	 * @param string $a_comp
	 * @param string $a_part
	 * @param array  $a_par
	 */

	function modifyGUI($a_comp, $a_part, $a_par = array()) {



		if ($a_part == 'tabs' && $this->checkGroup()){

			$cert_access = new ilParticipationCertificateAccess($_GET['ref_id']);

			if($cert_access->hasCurrentUserPrintAccess()) {
				/**
				 * @var ilTabsGUI $tabs
				 */
				$tabs = $a_par["tabs"];
				$this->ctrl->saveParameterByClass('ilParticipationCertificateGUI','ref_id');
				$tabs->addTab('certificates', 'Certificates', $this->ctrl->getLinkTargetByClass(array
				('ilUIPluginRouterGUI', 'ilParticipationCertificateGUI'),ilParticipationCertificateGUI::CMD_DISPLAY));
			}
		}
	}


	/**
	 * @return bool
	 * check if tab should be displayed, only displayed in groups!
	 */
	function checkGroup(){
		foreach ($this->ctrl->getCallHistory() as $GUIClassesArray) {
			if ($GUIClassesArray['class'] == 'ilObjGroupGUI')
				return true;
		}
		return false;
	}
}


?>