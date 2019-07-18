<#1>
<?php
//
?>
<#2>
<?php
//
?>
<#3>
<?php
//
?>
<#4>
<?php
//
?>
<#5>
<?php
//
?>
<#6>
<?php
//
?>
<#7>
<?php
//
?>
<#8>
<?php
//
?>
<#9>
<?php
global $DIC;
$ilDB = $DIC->database();
if($ilDB->tableExists(ilParticipationCert::TABLE_NAME)) {
$ilDB->dropTable(ilParticipationCert::TABLE_NAME);
}
if($ilDB->tableExists(ilParticipationCert::TABLE_NAME . '_seq')) {
$ilDB->dropTable(ilParticipationCert::TABLE_NAME . '_seq');
}
?>
<#10>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateConfig::updateDB();
?>
<#11>
<?php
//
?>
<#12>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateConfig::updateDB();

$part_cert_configs = new ilParticipationCertificateConfigs();

foreach($part_cert_configs->returnCertTextDefaultValues() as $key => $value) {
	$part_conf = $value;
	$part_conf->store();
}
?>
<#13>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'page2_box1_row2', 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();
if(!is_object($config)) {
$config = new ilParticipationCertificateConfig();
$config->setGroupRefId(0);
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
$config->setConfigKey('page2_box1_row2');
$config->setConfigValue('Bearbeitung von empfohlenen Mathematik- Lernmodulen'); // TODO lang
$config->store();
}
?>
<#14>
<?php
//
?>
<#15>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'footer_config', 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
	$part_conf->setConfigKey('footer_config');
	$part_conf->setConfigValue('Die Resultate dieser Bescheinigung wurden manuell berechnet.'); // TODO lang
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}
?>
<#16>
<?php
?>
<#17>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$config = ilParticipationCertificateConfig::where(['config_key' =>  'percent_value',
	'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE,
	'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
	"group_ref_id" => 0])->first();
if(!is_object($config)) {
$part_conf = new ilParticipationCertificateConfig();
$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$part_conf->setConfigKey('percent_value');
$part_conf->setConfigValue(50);
$part_conf->setGroupRefId(0);
$part_conf->store();
}
?>
<#18>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateConfig::updateDB();

$config = ilParticipationCertificateConfig::where([
	'config_key' =>  'keyword',
	'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE,
	'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER,
	"group_ref_id" => 0])->first();
if(!is_object($config)) {
$part_conf = new ilParticipationCertificateConfig();
$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$part_conf->setConfigKey('keyword');
$part_conf->setConfigValue("Lerngruppe");
$part_conf->setGroupRefId(0);
$part_conf->store();
}
?>
<#19>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'page1_issuer_signature', 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
	$part_conf->setConfigKey('page1_issuer_signature');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}
?>
<#20>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'page1_disclaimer', 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
	$part_conf->setConfigKey('page1_disclaimer');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}
?>
<#21>
<?php
//
?>
<#22>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$configs = ilParticipationCertificateConfig::where(array('config_key' =>  'page1_disclaimer', 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER))->get();

foreach($configs as $config) {
	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
	$config->store();
}
?>
<#23>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";

$config = ilParticipationCertificateConfig::where(array('config_key' =>  'udf_firstname', 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
	$part_conf->setConfigKey('udf_firstname');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}

$config = ilParticipationCertificateConfig::where(array('config_key' =>  'udf_lastname', 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
	$part_conf->setConfigKey('udf_lastname');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}

$config = ilParticipationCertificateConfig::where(array('config_key' =>  'udf_gender', 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
	$part_conf->setConfigKey('udf_gender');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}

$config = ilParticipationCertificateConfig::where(array('config_key' =>  'color', 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
	$part_conf->setConfigKey('color');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}
?>
<#24>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";

global $DIC;
$ilDB = $DIC->database();
$result = $ilDB->query('SELECT DISTINCT group_ref_id FROM ' . ilParticipationCertificateConfig::TABLE_NAME . ' WHERE group_ref_id != 0');
while ($row = $this->db->fetchAssoc($result)) {
	$group_ref_id = $row["group_ref_id"];
	$part_cert_configs = new ilParticipationCertificateConfigs();
	foreach($part_cert_configs->returnDefaultValues() as $key => $value) {
		$config = ilParticipationCertificateConfig::where(array('config_key' =>  $key, 'config_type' => ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => $group_ref_id))->first();
		if(!is_object($config)) {
			$config = new ilParticipationCertificateConfig();
			$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GROUP);
			$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
			$config->setConfigKey($key);
			$config->setConfigValue($value);
			$config->setGroupRefId($group_ref_id);
			$config->store();
		}
	}
}
?>
<#25>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateConfig::updateDB();

//Set Config ID of Default Config to 1.
foreach (ilParticipationCertificateConfig::where(array(
	"config_type" => ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE,
	"group_ref_id" => 0
))->orderBy('order_by')->get() as $config) {
	$config->setGlobalConfigId(1);
	$config->update();
}
?>
<#26>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateGlobalConfigSet::updateDB();
?>
<#27>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$global_config = new ilParticipationCertificateGlobalConfigSet();
$global_config->setId(1);
$global_config->setOrderBy(1);
$global_config->setTitle("Default");
$global_config->store();
?>
<#28>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateGlobalConfigSet::updateDB();
$part_cert_global = ilParticipationCertificateGlobalConfigSet::where(["order_by" => 1])->first();
$part_cert_global->setActive(1);
$part_cert_global->store();
?>
<#29>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateObjectConfigSet::updateDB();
?>
<#30>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";


//set global plugin configurations
$config = ilParticipationCertificateConfig::where(["config_key" => 'udf_firstname'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigKey('udf_firstname');
$config->setGlobalConfigId(0);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->store();

$config = ilParticipationCertificateConfig::where(["config_key" => 'udf_lastname'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('udf_lastname');
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();


$config = ilParticipationCertificateConfig::where(["config_key" => 'udf_gender'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('udf_gender');
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();


$config = ilParticipationCertificateConfig::where(["config_key" => 'keyword'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('keyword');
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();


$config = ilParticipationCertificateConfig::where(["config_key" => 'color'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('color');
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();


$config = ilParticipationCertificateConfig::where(["config_key" => 'logo'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('logo');
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();
?>
<#31>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";

$part_cert_global_config_sets = new ilParticipationCertificateGlobalConfigSets();
$part_cert_default_config_set = $part_cert_global_config_sets->getDefaultConfig();


$part_cert_configs = new ilParticipationCertificateConfigs();
foreach($part_cert_configs->returnCertTextDefaultValues() as $key => $value) {
	/**
	 * @var $value ilParticipationCertificateConfig
	 */
	$config = ilParticipationCertificateConfig::where(['config_key' => $value->getConfigKey(),
		"global_config_id" => $part_cert_default_config_set->getId()])->first();

	if(!is_object($config)) {
		$value->setGlobalConfigId($part_cert_default_config_set->getId());
		$config = $value;
	} else {
		$config->setOrderBy($value->getOrderBy());
	}
	$config->store();
}
?>
<#32>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";

$config = ilParticipationCertificateConfig::where(["config_key" => 'logo'])->first();
$config->delete();

$part_cert_global_config_sets = new ilParticipationCertificateGlobalConfigSets();
$part_cert_default_config_set = $part_cert_global_config_sets->getDefaultConfig();

$part_cert_configs = new ilParticipationCertificateConfigs();
foreach($part_cert_configs->returnCertTextDefaultValues() as $key => $value) {
	/**
	 * @var $value ilParticipationCertificateConfig
	 */
	$config = ilParticipationCertificateConfig::where(['config_key' => $value->getConfigKey(),
		"global_config_id" => $part_cert_default_config_set->getId()])->first();

	if(!is_object($config)) {
		$value->setGlobalConfigId($part_cert_default_config_set->getId());
		$config = $value;
	} else {
		$config->setOrderBy($value->getOrderBy());
	}
	$config->store();
}
?>
<#33>
<?php

require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
global $DIC;

//FIX cert_config
$select = "UPDATE dhbw_part_cert_conf SET config_type = 2, config_value_type = 2  where config_key in('period_start','period_end','enable_self_print','self_print_start','self_print_end') and group_ref_id is not null";
$DIC->database()->query($select);

//Fix global config if necessary
$config = ilParticipationCertificateConfig::where(["config_key" => 'udf_firstname'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigKey('udf_firstname');
$config->setGlobalConfigId(0);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->store();

$config = ilParticipationCertificateConfig::where(["config_key" => 'udf_lastname'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('udf_lastname');
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();


$config = ilParticipationCertificateConfig::where(["config_key" => 'udf_gender'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('udf_gender');
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();


$config = ilParticipationCertificateConfig::where(["config_key" => 'keyword'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('keyword');
$config->setConfigValue("Lerngruppe");
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();


$config = ilParticipationCertificateConfig::where(["config_key" => 'color'])->first();
if(!is_object($config)) {
	$config = new ilParticipationCertificateConfig();
}
$config->setConfigKey('color');
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$config->setGlobalConfigId(0);
$config->store();


//TEMPLATE
//Config Template
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";

$part_cert_global_config_sets = new ilParticipationCertificateGlobalConfigSets();
$part_cert_default_config_set = $part_cert_global_config_sets->getDefaultConfig();

$part_cert_configs = new ilParticipationCertificateConfigs();
foreach($part_cert_configs->returnCertTextDefaultValues() as $key => $value) {
	/**
	 * @var $value ilParticipationCertificateConfig
	 */
	$config = new ilParticipationCertificateConfig();
	$config->setConfigKey($value->getConfigKey());
	$config->setGlobalConfigId($part_cert_default_config_set->getId());
	$config->setConfigType(ilParticipationCertificateConfig::CONFIG_SET_TYPE_TEMPLATE);


	$config->setConfigKey($value->getConfigKey());
	$config->setOrderBy($value->getOrderBy());
	$config->setConfigType($value->getConfigType());
	$config->setConfigValueType($value->getConfigValueType());


	$config->store();

	if(!is_object($config)) {
		$value->setGlobalConfigId($part_cert_default_config_set->getId());
		$config = $value;
	} else {
		$config->setOrderBy($value->getOrderBy());
	}
	$config->store();
}

?>
<#34>
<?php
//
?>