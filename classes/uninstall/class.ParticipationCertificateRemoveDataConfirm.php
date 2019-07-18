<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class ParticipationCertificateRemoveDataConfirm
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ParticipationCertificateRemoveDataConfirm: ilUIPluginRouterGUI
 */
class ParticipationCertificateRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	const PLUGIN_CLASS_NAME = ilParticipationCertificatePlugin::class;
}
