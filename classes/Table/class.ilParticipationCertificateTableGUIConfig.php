<?php

require_once './Services/Table/classes/class.ilTable2GUI.php';

/**
 * Class ilParticipationCertificateTableGUI
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilParticipationCertificateTableGUIConfig extends ilTable2GUI{

	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilParticipationCertificateTableGUI
	 */
	protected $parent_obj;
	/**
	 * @var ilParticipationCertificatePlugin
	 */
	protected $pl;
	/**
	 * @var array
	 */
	protected $filter = array();

	/**
	 * ilParticipationCertificateTableGUI constructor.
	 *
	 * @param ilParticipationCertificateTableGUI   $a_parent_obj
	 * @param string $a_parent_cmd
	 */
	public function __construct($a_parent_obj, $a_parent_cmd){
		global $ilCtrl,$tabs;

		$this->ctrl = $ilCtrl;
		$this->tabs = $tabs;
		$this->setId('id');
		parent::__construct($a_parent_obj,$a_parent_cmd);

		$this->pl = ilParticipationCertificatePlugin::getInstance();
		$this->getEnableHeader();
		$this->setTitle('Resultate Übersicht');


		$this->addColumn('UserId',"","10%");
		$this->addColumn('Name',"","10%");
		$this->addColumn('Einstiegstest abgeschlossen',"","10%");
		$this->addColumn('Resultat qualifizierende Tests',"","10%");
		$this->addColumn('Gesamt-Prozentwert aller Lernziele',"","10%");
		$this->addColumn('Anzahl Hausaufgaben und Individual Assessments',"","10%");
		$this->addColumn('Aktionen',"","10%");


		$this->initFilter();
		$this->getItems();
		$this->getDataFromDB();



	}

	public function getItems(){



	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {

		$this->tpl->setVariable('NAME',ilPartCertUsersData::getData(283));


		$button = ilLinkButton::getInstance();
		$button->setCaption('Button',false);
		$button->setUrl($this->ctrl->getLinkTargetByClass(ilParticipationCertificateTableGUI::class,ilParticipationCertificateTableGUI::CMD_CONTENT));

		$this->tpl->setVariable('Aktionen');
		$button->render();
		$this->addActionMenu();


	}


	public function initFilter() {
		$name = new ilTextInputGUI('Name');
		$this->addAndReadFilterItem($name);

		$name->setMaxLength(64);
		$name->setSize(20);
		$name->readFromSession();
		$this->filter['Name'] = $name->getValue();
	}

	public function addAndReadFilterItem(ilFormPropertyGUI $item)
	{
		$this->addFilterItem($item);
		$item->readFromSession();
		if ($item instanceof ilCheckboxInputGUI) {
			$this->filter[$item->getPostVar()] = $item->getChecked();
		} else {
			$this->filter[$item->getPostVar()] = $item->getValue();
		}
		$this->setDisableFilterHiding(false);
	}


	public function addActionMenu(){


		$current_selection_list = new ilAdvancedSelectionListGUI();
		$current_selection_list->setListTitle('Aktionen');
		$current_selection_list->setId('actions');
		$current_selection_list->setUseImages(false);


		$current_selection_list->addItem('Drucken',ilParticipationCertificateTwigParser::class,$this->ctrl->getLinkTargetByClass(ilParticipationCertificateTwigParser::class));

		$this->tpl->setVariable('ACTIONS',$current_selection_list->getHTML());
	}

	public function getDataFromDB(){

		$this->setData(ilPartCertUsersData::getData());

	}

}