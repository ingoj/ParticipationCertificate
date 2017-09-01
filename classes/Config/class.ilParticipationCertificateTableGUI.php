<?php


require_once './Services/Table/classes/class.ilTable2GUI.php';


class ilParticipationCertificteTableGUI extends ilTable2GUI {

	/**
	 * @var @ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var array
	 */
	protected $filter = array();


	protected static $available_columns = array(
		'title',
		'description',
		'name',

	);



}