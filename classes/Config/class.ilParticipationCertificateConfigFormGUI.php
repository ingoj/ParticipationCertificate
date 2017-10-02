<?php
require_once './Services/Form/classes/class.ilPropertyFormGUI.php';
require_once './Services/form/clsses/class.ilMultiSelectInputGUI.php';


class ilParticipationCertificateConfigFormGUI extends ilPropertyFormGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var
	 */
	protected $parent_gui;


	/**
	 * ilParticipationCertificateConfigFormGUI constructor.
	 *
	 * @param $parent_gui
	 */
	public function __construct($parent_gui) {
		global $ilCtrl;

		$this->parent_gui = $parent_gui;
		$this->ctrl = $ilCtrl;
		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initForm();


	}

	protected function initForm(){

		$this->setTitle('PDF Generator');


		$item = new ilTextInputGUI('Name', 'name');
		$item->setRequired(true);

	}

}
?>