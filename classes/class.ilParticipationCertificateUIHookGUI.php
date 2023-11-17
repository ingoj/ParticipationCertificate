<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilParticipationCertificateUIHookGUI
 *
 * @ilCtrl_Calls ilParticipationCertificateUIHookGUI: ilParticipationCertificateGUI
 */
class ilParticipationCertificateUIHookGUI extends ilUIHookPluginGUI {

	const TAB_CERTIFICATES = "certificates";
	protected ilCtrl|ilCtrlInterface $ctrl;
	protected ilParticipationCertificatePlugin $pl;
	protected int $groupRefId;
	protected ?ilObject $learnGroup;
	protected string $learnGroupTitle;
	protected array $keywords;


	public function __construct() {
		global $DIC;

        $this->keywords = [];
		if($DIC->offsetExists('tpl')) {
			$this->ctrl = $DIC->ctrl();
			$this->pl = ilParticipationCertificatePlugin::getInstance();
			$this->groupRefId = (int)$_GET['ref_id'];
			$this->objecttype = ilObject::_lookupType($this->groupRefId, true);

		if ($this->groupRefId === 0 || ( $this->objecttype !== 'crs' and $this->objecttype !== 'grp')) {
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
	 * Modify GUI objects, before they generate output
	 * @throws ilCtrlException
	 */
	function modifyGUI(string $a_comp, string $a_part, array $a_par = array()): void
	{
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
	 * check if tab should be displayed, only displayed in groups!
	 */
	function checkGroup(): bool
	{
		foreach ($this->ctrl->getCallHistory() as $GUIClassesArray) {
			if (($this->objecttype === 'crs') && array_key_exists('class', $GUIClassesArray) && ($GUIClassesArray['class'] == ilObjCourseGUI::class)) {
				if ($this->strposa($this->learnGroupTitle, $this->keywords) !== false) {
					return true;
				}
			}
			if (($this->objecttype === 'grp') and ($GUIClassesArray['class'] == ilObjGroupGUI::class)) {
				if ($this->strposa($this->learnGroupTitle, $this->keywords) !== false) {
					return true;
				}
			}
		}
		return false;
	}

	private function strposa($haystack, $needles=array(), int $offset=0): mixed {
		$chr = array();
		foreach($needles as $needle) {
			$res = strpos($haystack, $needle, $offset);
			if ($res !== false) $chr[$needle] = $res;
		}
		if(empty($chr)) return false;
		return min($chr);
	}
}