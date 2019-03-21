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

foreach(ilParticipationCertificateConfig::returnDefaultValues() as $key => $value) {
$part_conf = new ilParticipationCertificateConfig();
$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
$part_conf->setConfigKey($key);
$part_conf->setConfigValue($value);
$part_conf->setGroupRefId(0);
$part_conf->store();
}
?>
<#13>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'page2_box1_row2', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();
if(!is_object($config)) {
$config = new ilParticipationCertificateConfig();
$config->setGroupRefId(0);
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
$config->setConfigKey('page2_box1_row2');
$config->setConfigValue('Bearbeitung von empfohlenen Mathematik- Lernmodulen'); // TODO lang
$config->store();
}
?>
<#14>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateConfig::updateDB();

$i = 1;
foreach(ilParticipationCertificateConfig::returnDefaultValues() as $key => $value) {
$config = ilParticipationCertificateConfig::where(array('config_key' =>  $key, 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();

	if(!is_object($config)) {
		/**
		 * @var ilParticipationCertificateConfig $config
		 */
		$config->setOrderBy($i);
		$config->store();
		$i = $i+1;
	}
}
?>
<#15>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'footer_config', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
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
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'percent_value', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
$part_conf = new ilParticipationCertificateConfig();
$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
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
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'keyword', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
$part_conf = new ilParticipationCertificateConfig();
$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
$part_conf->setConfigKey('keyword');
$part_conf->setConfigValue("Lerngruppe"); // TODO lang
$part_conf->setGroupRefId(0);
$part_conf->store();
}
?>
<#19>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'page1_issuer_signature', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
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
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'page1_disclaimer', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
	$part_conf->setConfigKey('page1_disclaimer');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}
?>
<#21>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$configs = ilParticipationCertificateConfig::where(array('config_key' =>  'page1_issuer_signature', 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER))->get();

foreach($configs as $config) {
	$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
	$config->store();
}
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

$config = ilParticipationCertificateConfig::where(array('config_key' =>  'udf_firstname', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
	$part_conf->setConfigKey('udf_firstname');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}

$config = ilParticipationCertificateConfig::where(array('config_key' =>  'udf_lastname', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
	$part_conf->setConfigKey('udf_lastname');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}

$config = ilParticipationCertificateConfig::where(array('config_key' =>  'udf_gender', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER);
	$part_conf->setConfigKey('udf_gender');
	$part_conf->setConfigValue("");
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}

$config = ilParticipationCertificateConfig::where(array('config_key' =>  'color', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_OTHER, "group_ref_id" => 0))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
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
	foreach (ilParticipationCertificateConfig::returnDefaultValues() as $key => $value) {
		$config = ilParticipationCertificateConfig::where(array('config_key' =>  $key, 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GROUP, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT, "group_ref_id" => $group_ref_id))->first();
		if(!is_object($config)) {
			$config = new ilParticipationCertificateConfig();
			$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GROUP);
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
	"config_type" => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL,
	"group_ref_id" => 0
))->orderBy('order_by')->get() as $config) {
	$config->setGlobalConfigId(1);
	$config->update();
}
?>
<#26>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateGlobalConfig::updateDB();
?>
<#27>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
$global_config = new ilParticipationCertificateGlobalConfig();
$global_config->setId(1);
$global_config->setOrderBy(1);
$global_config->setTitle("Default");
$global_config->store();
?>
<#28>
<?php
require_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php";
ilParticipationCertificateGlobalConfig::updateDB();
$part_cert_global = ilParticipationCertificateGlobalConfig::where(["order_by" => 1])->first();
$part_cert_global->setActive(1);
$part_cert_global->store();
?>
