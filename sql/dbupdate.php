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
global $ilDB;
if($ilDB->tableExists('participationcert')) {
$ilDB->dropTable('participationcert');
}
if($ilDB->tableExists('participationcert_seq')) {
$ilDB->dropTable('participationcert_seq');
}
?>
<#10>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfig.php';
ilParticipationCertificateConfig::updateDB();
?>
<#11>
<?php
//
?>
<#12>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfig.php';
ilParticipationCertificateConfig::updateDB();

require_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfig.php';
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
require_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfig.php';
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'page2_box1_row2', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT))->first();
if(!is_object($config)) {
$config = new ilParticipationCertificateConfig();
$config->setGroupRefId(0);
$config->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
$config->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
$config->setConfigKey('page2_box1_row2');
$config->setConfigValue('Bearbeitung von empfohlenen Mathematik- Lernmodulen');
$config->store();
}
?>
<#14>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfig.php';
ilParticipationCertificateConfig::updateDB();

require_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfig.php';
$i = 1;
foreach(ilParticipationCertificateConfig::returnDefaultValues() as $key => $value) {
$config = ilParticipationCertificateConfig::where(array('config_key' =>  $key, 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT))->first();

	if(is_object($config)) {
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
require_once 'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/classes/class.ilParticipationCertificateConfig.php';
$config = ilParticipationCertificateConfig::where(array('config_key' =>  'footer_config', 'config_type' => ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL, 'config_value_type' => ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT))->first();
if(!is_object($config)) {
	$part_conf = new ilParticipationCertificateConfig();
	$part_conf->setConfigType(ilParticipationCertificateConfig::CONFIG_TYPE_GLOBAL);
	$part_conf->setConfigValueType(ilParticipationCertificateConfig::CONFIG_VALUE_TYPE_CERT_TEXT);
	$part_conf->setConfigKey('footer_config');
	$part_conf->setConfigValue('Die Resultate dieser Bescheinigung wurden manuell berechnet.');
	$part_conf->setGroupRefId(0);
	$part_conf->store();
}
?>
