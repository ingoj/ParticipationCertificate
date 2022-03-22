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
	 * @var array
	 */
	protected $keywords;


	public function __construct() {
		global $DIC;


		if($DIC->offsetExists('tpl')) {
			$this->ctrl = $DIC->ctrl();
			$this->pl = ilParticipationCertificatePlugin::getInstance();
			$this->groupRefId = (int)$_GET['ref_id'];

            if ($this->groupRefId === 0 || ilObject::_lookupType($this->groupRefId, true) !== 'grp') {
                return;
            }

            $this->learnGroup = ilObjectFactory::getInstanceByRefId($this->groupRefId);
            $this->learnGroupTitle = $this->learnGroup->getTitle();
            
			$config = ilParticipationCertificateConfig::where(array(
					'config_key' => 'keyword',
					'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL,
					'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
					"group_ref_id" => 0
			))->first();

			if(is_object($config)) {
				$this->keywords = explode(",",$config->getConfigValue());
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
				if ($this->strposa($this->learnGroupTitle, $this->keywords) !== false) {
					return true;
				}
			}
		}

		return false;
	}

	private function strposa($haystack, $needles=array(), $offset=0) {
		$chr = array();
		foreach($needles as $needle) {
			$res = strpos($haystack, $needle, $offset);
			if ($res !== false) $chr[$needle] = $res;
		}
		if(empty($chr)) return false;
		return min($chr);
	}
}

?>