<?php
require_once('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/twig/twig/lib/Twig/Autoloader.php');
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/vendor/autoload.php';

/**
 * Class ilParticipationCertificatePDFGenerator
 *
 * @author            Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilParticipationCertificatePDFGenerator: ilParticipationCertificateGUI, ilParticipationCertificateTwigParser
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


	public function executeCommand() {
		//$this->tpl->getStandardTemplate();
		$cmd = $this->ctrl->getCmd();
		switch ($cmd) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_PDF);
				$this->{$cmd}();
				break;
		}
	}


	public function generatePDF($rendered) {
		global $printCount,$pdfCount;


		$parsins = new ilParticipationCertificateTwigParser();
		$membercount = $parsins->membercount;

		$mpdf = new mPDF('', '', '', '', 20, 20, '', '', 0, 0);
		//$mpdf->SetHeader('');
		//$mpdf->showImageErrors = true;
		//$html = file_get_contents($rendered);
		$css = file_get_contents('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/Teilnahmebescheinigung.css');
		$printCount++;
		$pdfCount++;


		if($printCount == 1) {
			$mpdf->WriteHTML($css, 1);
			$mpdf->WriteHTML($rendered, 2);
			$mpdf->Output('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/' . $pdfCount . '.pdf', 'F');
		}
		elseif ($printCount == $membercount){
			$mpdf->WriteHTML($css, 1);
			$mpdf->WriteHTML($rendered, 2);
			$mpdf->SetImportUse();
			$page = $mpdf->SetSourceFile('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/'.--$pdfCount.'.pdf');
			for($i = 1; $i <= $page; $i++) {
				$mpdf->AddPage();
				$tplID = $mpdf->ImportPage($i);
				$mpdf->UseTemplate($tplID);
			}
			$mpdf->Output('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/' . $pdfCount . '.pdf', 'D');
			$this->tpl->getStandardTemplate();
			$this->ctrl->redirectByClass(ilParticipationCertificateGUI::class, ilParticipationCertificateGUI::CMD_DISPLAY);
		}
		else{
			$mpdf->WriteHTML($css, 1);
			$mpdf->WriteHTML($rendered, 2);
			$mpdf->SetImportUse();
			$page = $mpdf->SetSourceFile('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/'.--$pdfCount.'.pdf');
			for($i = 1; $i <= $page; $i++) {
				$mpdf->AddPage();
				$tplID = $mpdf->ImportPage($i);
				$mpdf->UseTemplate($tplID);
			}
			$mpdf->Output('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/' . $pdfCount . '.pdf', 'F');
		}
	}

	public function mergePDF($rendered){

		//Functioning save to the server
		global $printCount,$mpdf;
		$parsins = new ilParticipationCertificateTwigParser();
		$membercount = $parsins->membercount;

		$mpdf = new mPDF('', '', '', '', 20, 20, '', '', 0, 0);
		//$mpdf->SetHeader('');
		//$mpdf->showImageErrors = true;
		//$html = file_get_contents($rendered);
		$css = file_get_contents('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/Templates/Teilnahmebescheinigung.css');
		$mpdf->WriteHTML($css, 1);
		$mpdf->WriteHTML($rendered, 2);

		if ($printCount == 0) {
			$printCount = 1;
		}
		if ($printCount == $membercount) {
			$mpdf->Output('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/' . $printCount . '.pdf', 'F');
			$this->tpl->getStandardTemplate();
			$this->ctrl->redirectByClass(ilParticipationCertificateGUI::class, ilParticipationCertificateGUI::CMD_DISPLAY);
		}
		else {
			$mpdf->Output('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/' . $printCount . '.pdf', 'F');
			$printCount ++;
		}











		}


	}

