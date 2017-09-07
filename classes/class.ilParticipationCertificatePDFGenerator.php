<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php';

/**
 * Class ilParticipationCertificatePDFGenerator
 *
 * @ilCtrl_isCalledBy ilParticipationCertificatePDFGenerator: ilParticipationCertificateGUI
 */

class ilParticipationCertificatePDFGenerator {

	const CMD_PDF = 'generatePDF';



	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;


	public function __construct() {
		global $tpl, $ilCtrl;
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
	}

public function executeCommand()
{
	$this->tpl->getStandardTemplate();
	$cmd = $this->ctrl->getCmd();
	switch ($cmd){
		default:
			$cmd = $this->ctrl->getCmd(self::CMD_PDF);
			$this->{$cmd}();
			break;
	}
}



	public function generatePDF() {
		$mpdf = new mPDF();
		$mpdf->SetHeader('Teilnahmebescheinigung');
		$html = file_get_contents('Templates/Teilnahmebescheinigung.html');
		$css = file_get_contents('/Templates/Teilnahmebescheinigung.css');
		$mpdf->WriteHTML($css, 1);
		$mpdf->WriteHTML($html, 2);
		$mpdf->Output('test.pdf', './ParticipationCertificate');
	}
}