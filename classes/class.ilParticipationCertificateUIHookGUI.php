<?php
include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");
include_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateGUI.php';

/**
 * Class ilParticipationCertificateUIHookGUI
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
		if ($a_part == 'tabs' && $this->checkCourse()){
			/**
			 * @var ilTabsGUI $tabs
			 */
			$tabs = $a_par["tabs"];
			$this->ctrl->saveParameterByClass('ilUIInscriptionGUI','ref_id');
			$tabs->addTab('certificates', 'Certificates', $this->ctrl->getLinkTargetByClass(array
			('ilUIPluginRouterGUI', 'ilParticipationCertificateGUI'),ilParticipationCertificateGUI::CMD_DISPLAY));
		}
	}


	function checkCourse(){
		//check if tab should be displayed in this course or class or whatever

		return true;
	}
}


?>