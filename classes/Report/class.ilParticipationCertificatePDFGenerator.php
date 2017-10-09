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
	/**
	 * @var string
	 */
	public $temp;


	public function __construct() {
		global $tpl, $ilCtrl, $tempFile, $tempCount;
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;

		if ($tempCount == 0) {
			$tempFile = $this->temp = ilUtil::ilTempnam();
			$tempCount ++;
		}
	}


	public function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		switch ($cmd) {
			default:
				$cmd = $this->ctrl->getCmd(self::CMD_PDF);
				$this->{$cmd}();
				break;
		}
	}


	public function generatePDF($rendered, $total_users) {
		global $printCount, $tempFile;

		//mPDF Instanz wird erzeugt. Mit Margin-Left-Right:20.
		$mpdf = new mPDF('', '', '', '', 20, 20, '', '', 0, 0);
		//Css file wird geladen
		$css = file_get_contents('./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ParticipationCertificate/templates/report/Teilnahmebescheinigung.css');
		$printCount ++;

		//Checkt ob es nur einen User in der Gruppe hat. Wenn True wird das PDf direkt nur für diesen gedruckt
		if ($total_users == 1) {
			$mpdf->WriteHTML($css, 1);
			$mpdf->WriteHTML($rendered, 2);
			$mpdf->Output('Teilnahmebescheinigungen' . '.pdf', 'D');
			$this->tpl->getStandardTemplate();
			$this->ctrl->redirectByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_INIT_TABLE);
		}
		//Checkt ob es der erste Durchlauf ist. Wenn True wird das erste PDF erzeugt und auf dem Server abgelegt.
		if ($printCount == 1) {
			$mpdf->WriteHTML($css, 1);
			$mpdf->WriteHTML($rendered, 2);
			$mpdf->Output($tempFile . '.pdf', 'F');
		} /*Checkt ob es der letzte Durchlauf ist. Wenn ja wird das letzte PDF erzeugt und das vorhandene PDF auf dem Server
			 *wird hinten an das erzeugte PDF angehängt. Anschliessend wird das fertige PDF dem User im Browser als Download angeboten.
			*/ elseif ($printCount == $total_users) {
			$mpdf->WriteHTML($css, 1);
			$mpdf->WriteHTML($rendered, 2);
			$mpdf->SetImportUse();
			$page = $mpdf->SetSourceFile($tempFile . '.pdf');
			for ($i = 1; $i <= $page; $i ++) {
				$mpdf->AddPage();
				$tplID = $mpdf->ImportPage($i);
				$mpdf->UseTemplate($tplID);
			}
			$mpdf->Output('Teilnahmebescheinigungen' . '.pdf', 'D');
			$this->tpl->getStandardTemplate();
			$this->ctrl->redirectByClass(ilParticipationCertificateResultGUI::class, ilParticipationCertificateResultGUI::CMD_INIT_TABLE);
		} /*Wenn es nicht der erste oder letzte Durchlauf ist, wird ein neues PDF erzeugt. Die bereits erzeugten PDF auf dem Server
		 *werden hinten angehängt. Danach wird es wieder auf dem Server gespeichert um im nächsten Durchlauf wieder anzuhängen.
		 */ else {
			$mpdf->WriteHTML($css, 1);
			$mpdf->WriteHTML($rendered, 2);
			$mpdf->SetImportUse();
			$page = $mpdf->SetSourceFile($tempFile . '.pdf');
			for ($i = 1; $i <= $page; $i ++) {
				$mpdf->AddPage();
				$tplID = $mpdf->ImportPage($i);
				$mpdf->UseTemplate($tplID);
			}
			$mpdf->Output($tempFile . '.pdf', 'F');
		}
	}
}
?>