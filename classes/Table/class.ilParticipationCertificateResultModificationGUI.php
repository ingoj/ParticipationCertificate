<?php

/**
 * Class ilParticipationCertificateResultModificationGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateResultModificationGUI {

	const CMD_DISPLAY = 'display';

	/**
	 * @var ilTabsGUI
	 */
	public $tabs;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;

	public function __construct() {
		global $ilCtrl, $ilTabs,$tpl;

		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->tpl = $tpl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();
	}


	public function display() {
	$this->tpl->getStandardTemplate();
	$this->initHeader();

	$form = $this->initform();

	$this->tpl->setContent($form->getHTML());
	$this->tpl->show();

	}
}