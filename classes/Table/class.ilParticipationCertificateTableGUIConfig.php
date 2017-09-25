<?php

require_once './Services/Table/classes/class.ilTable2GUI.php';

/**
 * Class ilParticipationCertificateTableGUI
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateTableGUIConfig extends ilTable2GUI{

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var
	 */
	protected $parent_obj;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;

	/**
	 * ilParticipationCertificateTableGUI constructor.
	 *
	 * @param     $a_parent_obj
	 * @param string $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd){
		global $ilCtrl;

		$this->ctrl = $ilCtrl;

		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->getEnableHeader();
		$this->setTitle('Resultate Übersicht');


		$this->addColumn('UserId');
		$this->addColumn('Name');
		$this->addColumn('Einstiegstest abgeschlossen');
		$this->addColumn('Resultat qualifizierende Tests');
		$this->addColumn('Gesamt-Prozentwert aller Lernziele');
		$this->addColumn('Anzahl Hausaufgaben und Individual Assessments');






	}

}