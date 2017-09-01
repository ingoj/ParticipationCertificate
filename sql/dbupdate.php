<#1>
<?php
require_once './classes/class.ilParticipationCertificateConfigGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php';
ilParticipationCertificate::installDB();
ilParticipationCertificate::updateDB();


?>