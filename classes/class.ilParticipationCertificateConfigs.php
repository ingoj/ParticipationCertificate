<?php

/**
 * Class ilParticipationCertificateConfigs
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilParticipationCertificateConfigs {

	public function __construct() {

	}


	/**
	 * @param int $global_config_id
	 *
	 * @return ilParticipationCertificateConfig[]
	 */
	public function getGlobalConfigSet($global_config_id = 0) {
		return ilParticipationCertificateConfig::where(array( "global_config_id" => $global_config_id ))->orderBy('order_by')->get();
	}

	/**
	 * @param int group_ref_id
	 * @param $config_type
	 */
	public function returnTextValues($group_ref_id = 0, $config_type = self::CONFIG_SET_TYPE_TEMPLATE) {
		$arr_config = ilParticipationCertificateConfig::where(array(
			"config_type" => $config_type,
			"group_ref_id" => $group_ref_id,
			'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
		))->orderBy('order_by')->getArray('config_key', 'config_value');
		if (count($arr_config) == 0) {
			$arr_config = ilParticipationCertificateConfig::where(array(
				"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE,
				'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT,
				"group_ref_id" => 0
			))->orderBy('order_by')->getArray('config_key', 'config_value');
		}

		return $arr_config;
	}

	/**
	 * @param int $group_ref_id
	 *
	 * @return bool|string
	 */
	public function returnPercentValue($group_ref_id = 0) {
		/**
		 * @var $config ilParticipationCertificateConfig
		 */
		$config = ilParticipationCertificateConfig::where(array(
			'group_ref_id' => $group_ref_id,
			'config_key' => 'percent_value',
			'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP
		))->first();
		if(is_object($config)) {
			return $config->getConfigValue();
		}

		$config_set_templates = new ilParticipationCertificateGlobalConfigSets();
		return $config_set_templates->getDefaultConfigSetValue('percent_value');
	}


	/**
	 * @param int $obj_ref_id
	 *
	 * @return array|ilParticipationCertificateConfig[]
	 * @throws arException
	 */
	public function getObjConfigSetIfNoneCreateDefaultAndCreateNewObjConfigValues($obj_ref_id) {

		$cert_obj_config = ilParticipationCertificateObjectConfigSet::where([ 'obj_ref_id' => $obj_ref_id ])->first();

		if (!is_object($cert_obj_config)) {
			$global_configs = new ilParticipationCertificateGlobalConfigSets();

			$cert_obj_config = new ilParticipationCertificateObjectConfigSet();
			$cert_obj_config->setConfigType(ilParticipationCertificateObjectConfigSet::CONFIG_TYPE_TEMPLATE);
			$cert_obj_config->setObjRefId($obj_ref_id);
			$cert_obj_config->setGlConfTemplateId($global_configs->getDefaultConfig()->getId());
			$cert_obj_config->store();
		}

		switch ($cert_obj_config->getConfigType()) {
			case ilParticipationCertificateObjectConfigSet::CONFIG_TYPE_TEMPLATE:
				return $this->getGlobalConfigSet($cert_obj_config->getGlConfTemplateId());
				break;
			case ilParticipationCertificateObjectConfigSet::CONFIG_TYPE_OWN:
				foreach ($this->getGlobalConfigSet($cert_obj_config->getGlConfTemplateId()) as $global_config_value) {
					if (!$this->getParticipationObjConfigValueByKey($obj_ref_id, $global_config_value->getConfigKey())) {
						$this->createParticipationObjConfigValueByGlobalConfigValue($obj_ref_id, $global_config_value);
					}
				}



				return
					array_merge(ilParticipationCertificateConfig::where(array(
						'group_ref_id' => $obj_ref_id,
						'config_key' => 'percent_value',
						'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP
					))->get(),
					ilParticipationCertificateConfig::where([
					"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP,
					"group_ref_id" => $obj_ref_id,
					"config_value_type" => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT
				])->orderBy('order_by')->get());

				break;
		}
	}


	/**
	 * @param int    $obj_ref_id
	 * @param string $config_key
	 *
	 * @return ilParticipationCertificateConfig|bool
	 */
	private function getParticipationObjConfigValueByKey($obj_ref_id, $config_key) {
		if (ilParticipationCertificateConfig::where([
			"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP,
			"config_key" => $config_key,
			"group_ref_id" => $obj_ref_id
		])->count()) {
			return ilParticipationCertificateConfig::where([
				"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP,
				"config_key" => $config_key,
				"group_ref_id" => $obj_ref_id
			])->first();
		};

		return false;
	}

	/**
	 * @param int    $global_config_id
	 * @param string $config_key
	 *
	 * @return ilParticipationCertificateConfig|bool
	 */
	public function getParticipationTemplateConfigValueByKey($global_config_id, $config_key) {
		if (ilParticipationCertificateConfig::where([
			"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE,
			"config_key" => $config_key,
			"global_config_id" => $global_config_id
		])->count()) {
			return ilParticipationCertificateConfig::where([
				"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE,
				"config_key" => $config_key,
				"global_config_id" => $global_config_id
			])->first();
		};

		return false;
	}

	/**
	 * @param int    $global_config_id
	 * @param string $config_key
	 *
	 * @return ilParticipationCertificateConfig|bool
	 */
	public function getParticipationGlobalConfigValueByKey($config_key) {
		if (ilParticipationCertificateConfig::where([
			"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL,
			"config_key" => $config_key,
		])->count()) {
			return ilParticipationCertificateConfig::where([
				"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL,
				"config_key" => $config_key,
			])->first();
		};

		return false;
	}


	/**
	 * @param int                              $obj_ref_id
	 * @param ilParticipationCertificateConfig $global_config_value
	 */
	private function createParticipationObjConfigValueByGlobalConfigValue($obj_ref_id, ilParticipationCertificateConfig $global_config_value) {
		$part_cert_obj_config_value = $global_config_value;
		$part_cert_obj_config_value->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP);
		$part_cert_obj_config_value->setGroupRefId($obj_ref_id);
		$part_cert_obj_config_value->setGlobalConfigId(0);
		$part_cert_obj_config_value->create();
	}


	/**
	 * @param int $obj_ref_id
	 * @param int $global_template_id
	 */
	public function setObjToUseCertTemplate($obj_ref_id, $global_template_id) {

		$this->deleteObjConfigSet($obj_ref_id);
		/**
		 * @var ilParticipationCertificateObjectConfigSet $part_cert_config
		 */
		$part_cert_config = ilParticipationCertificateObjectConfigSet::where([ 'obj_ref_id' => $obj_ref_id ])->first();
		if (!is_object($part_cert_config)) {
			$part_cert_config = new ilParticipationCertificateObjectConfigSet();
		}
		$part_cert_config->setObjRefId($obj_ref_id);
		$part_cert_config->setConfigType(ilParticipationCertificateObjectConfigSet::CONFIG_TYPE_TEMPLATE);
		$part_cert_config->setGlConfTemplateId($global_template_id);
		$part_cert_config->store();
	}


	/**
	 * @param int $obj_ref_id
	 * @param int $global_template_id
	 */
	public function setOwnCertConfigFromTemplate($obj_ref_id, $global_template_id) {
		$part_cert_config = ilParticipationCertificateObjectConfigSet::where([ 'obj_ref_id' => $obj_ref_id ])->first();
		if (!is_object($part_cert_config)) {
			$part_cert_config = new ilParticipationCertificateObjectConfigSet();
		}
		$part_cert_config->setObjRefId($obj_ref_id);
		$part_cert_config->setConfigType(ilParticipationCertificateObjectConfigSet::CONFIG_TYPE_OWN);
		$part_cert_config->setGlConfTemplateId($global_template_id);
		$part_cert_config->store();

		$this->createOrUpdateObjConfigSetFromTemplate($obj_ref_id, $global_template_id);
	}


	/**
	 * @param int $obj_ref_id
	 * @param int $global_template_id $
	 */
	private function createOrUpdateObjConfigSetFromTemplate($obj_ref_id, $global_template_id) {
		$this->deleteObjConfigSet($obj_ref_id);

		foreach (ilParticipationCertificateConfig::where([
			'config_type' => ilParticipationCertificateObjectConfigSet::CONFIG_TYPE_TEMPLATE,
			"global_config_id" => $global_template_id
		])->get() as $part_cert_template_config_value) {
			/**
			 * @var ilParticipationCertificateConfig $part_cert_template_config_value
			 */
			$part_cert_config_value = $part_cert_template_config_value;
			$part_cert_config_value->setGroupRefId($obj_ref_id);
			$part_cert_config_value->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP);
			$part_cert_config_value->setGlobalConfigId(0);
			$part_cert_config_value->create();
		}
	}

	/**
	 * @param int $obj_ref_id
	 */
	private function deleteObjConfigSet($obj_ref_id) {
		$arr_config = ilParticipationCertificateConfig::where([ "group_ref_id" => $obj_ref_id ])->get();
		if (count($arr_config)) {
			foreach ($arr_config as $config) {
				/**
				 * @var ilParticipationCertificateConfig $config
				 */
				switch ($config->getConfigKey()) {
					case "page1_issuer_signature":
						ilParticipationCertificateConfig::deletePicture($config->getGroupRefId(), $config->getConfigKey() . ".png");
						break;
					default:
						break;
				}

				$config->delete();
			}
		}
	}


	/**
	 * @return ilParticipationCertificateConfig[]
	 */
	public function returnCertTextDefaultValues() {

		$arr_configs = [];

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('percent_value');
		$cert_config->setConfigValue('50');
		$cert_config->setOrderBy(1);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_title');
		$cert_config->setConfigValue('Teilnahmebescheinigung');
		$cert_config->setOrderBy(2);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_introduction1');
		$cert_config->setConfigValue('{{username}}, hat am Studienvorbereitungsprogramm mit Schwerpunkt „Mathematik“ auf der Lernplattform studienvorbereitung.dhbw.de teilgenommen.');
		$cert_config->setOrderBy(3);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_introduction2');
		$cert_config->setConfigValue('Die Teilnahme vor Studienbeginn an der DHBW Karlsruhe umfasste:');
		$cert_config->setOrderBy(4);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_box1_title');
		$cert_config->setConfigValue('Studienvorbereitung - Mathematik:');
		$cert_config->setOrderBy(5);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_box1_row1');
		$cert_config->setConfigValue('Abschluss Diagnostischer Einstiegstest Mathematik');
		$cert_config->setOrderBy(6);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_box1_row2');
		$cert_config->setConfigValue('Bearbeitung von empfohlenen Mathematik - Lernmodulen');
		$cert_config->setOrderBy(7);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_box2_title');
		$cert_config->setConfigValue('Studienvorbereitung - eMentoring:');
		$cert_config->setOrderBy(8);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_box2_row1');
		$cert_config->setConfigValue('Aktive Teilnahme an Videokonferenzen');
		$cert_config->setOrderBy(9);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_box2_row2');
		$cert_config->setConfigValue('Bearbeitung der Aufgaben zu überfachlichen Themen:');
		$cert_config->setOrderBy(10);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_location_date');
		$cert_config->setConfigValue('Karlsruhe, den {{date}}');
		$cert_config->setOrderBy(11);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_issuer_name');
		$cert_config->setConfigValue('Max Mustermann');
		$cert_config->setOrderBy(12);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_issuer_title');
		$cert_config->setConfigValue('(Education Support Center)');
		$cert_config->setOrderBy(13);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_issuer_signature');
		$cert_config->setConfigValue('');
		$cert_config->setOrderBy(14);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page1_disclaimer');
		$cert_config->setConfigValue('');
		$cert_config->setOrderBy(15);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_title');
		$cert_config->setConfigValue('Erläuterungen zur Bescheinigung');
		$cert_config->setOrderBy(16);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_introduction1');
		$cert_config->setConfigValue('Das  Studienvorbereitungsprogramm  mit  Schwerpunkt  Mathematik  auf  der  Lernplattform studienstart.dhbw.de,  richtet  sich  an  Studienanfänger/-innen der  Wirtschaftsinformatik  der DHBW Karlsruhe. Die Teilnehmer/-innen des Programms erhalten die Möglichkeit sich bereits vor  Studienbeginn,  Studientechniken anzueignen  sowie  das  fehlende  Vorwissen  im  Fach  „Mathematik“  aufzuarbeiten.  Dadurch  haben Studierende  mehr  Zeit  ihre  Wissenslücken  in  Mathematik zu schließen und sich mit dem neuen Lernen auseinanderzusetzen.');
		$cert_config->setOrderBy(17);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_introduction2');
		$cert_config->setConfigValue('Ziel des Programms ist es,  Studienanfänger/-innen vor Studienbeginn auf das Fach Mathematik im Studium vorzubereiten. Neben der Vermittlung von mathematischen Inhalten, fördert der Online-Vorkurs  überfachliche  Kompetenzen  wie  Zeitmanagement  und  Lerntechniken  sowie  die Fähigkeit zum Selbststudium.');
		$cert_config->setOrderBy(18);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_introduction3');
		$cert_config->setConfigValue('{{username}} hat im Rahmen des Studienvorbereitungsprogramms mit Schwerpunkt Mathematik mit folgenden Aufgabenstellungen teilgenommen:');
		$cert_config->setOrderBy(19);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_box1_title');
		$cert_config->setConfigValue('Studienvorbereitung – Mathematik');
		$cert_config->setOrderBy(20);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_box1_row1');
		$cert_config->setConfigValue('Abschluss Diagnostischer Einstiegstest Mathematik');
		$cert_config->setOrderBy(21);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_box1_row2');
		$cert_config->setConfigValue('Bearbeitung von empfohlenen Mathematik - Lernmodulen');
		$cert_config->setOrderBy(22);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_box2_title');
		$cert_config->setConfigValue('Studienvorbereitung - eMentoring');
		$cert_config->setOrderBy(23);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_box2_row1');
		$cert_config->setConfigValue('Aktive Teilnahme an Videokonferenzen');
		$cert_config->setOrderBy(24);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('page2_box2_row2');
		$cert_config->setConfigValue('Bearbeitung der Aufgaben zu überfachlichen Themen:');
		$cert_config->setOrderBy(25);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		$cert_config = new ilParticipationCertificateConfig();
		$cert_config->setConfigKey('footer_config');
		$cert_config->setConfigValue('Die Resultate dieser Bescheinigung wurden manuell berechnet.');
		$cert_config->setOrderBy(26);
		$cert_config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
		$cert_config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
		$arr_configs[] = $cert_config;

		return $arr_configs;
	}
}
