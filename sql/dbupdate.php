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