<?php

use Mpdf\Mpdf;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use Mpdf\MpdfException;

/**
 * Class ilParticipationCertificatePDFGenerator
 *
 * @ilCtrl_isCalledBy ilParticipationCertificatePDFGenerator: ilParticipationCertificateGUI, ilParticipationCertificateTwigParser
 */
class ilParticipationCertificatePDFGenerator
{
    const CMD_PDF = 'generatePDF';

    protected ilTemplate|ilGlobalTemplateInterface $tpl;

    protected ilCtrlInterface $ctrl;

    public string $temp;

    protected ilParticipationCertificatePlugin $pl;


    public function __construct()
    {
        global $DIC, $tempFile, $tempCount;
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->ctrl = $DIC->ctrl();
        $this->pl = ilParticipationCertificatePlugin::getInstance();

        if ($tempCount == 0) {
            $tempFile = $this->temp = ilFileUtils::ilTempnam();
            $tempCount++;
        }
    }


    public function executeCommand(): void
    {
        $cmd = $this->ctrl->getCmd();
        switch ($cmd) {
            default:
                $cmd = $this->ctrl->getCmd(self::CMD_PDF);
                $this->{$cmd}();
                break;
        }
    }

    /**
     * @throws CrossReferenceException
     * @throws PdfTypeException
     * @throws MpdfException
     * @throws PdfParserException
     */
    public function generatePDF(
        string $rendered,
        int $total_users,
        bool $printIsAsynchronous = false
    ): void {
        global $printCount, $tempFile;

        require_once __DIR__ . '/../../vendor/autoload.php';

        //mPDF Instanz wird erzeugt. Mit Margin-Left-Right:20.
        $mpdf = new Mpdf([
            'tempDir' => '/tmp/mpdf',
            'default_font' => 'dejavusans'
        ]);

        //Css file wird geladen
        $css = file_get_contents('./' . ilParticipationCertificatePlugin::PLUGIN_DIRECTORY . '/templates/report/Teilnahmebescheinigung.css');
        $printCount++;

        //Checkt ob es nur einen User in der Gruppe hat. Wenn True wird das PDf direkt nur für diesen gedruckt
        if ($total_users == 1) {

            if ($printIsAsynchronous) {
                $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->WriteHTML($rendered, \Mpdf\HTMLParserMode::HTML_BODY);

                // return PDF as a base64 string
                $pdfString = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

                echo json_encode([
                    'success' => true,
                    'pdf_base64' => base64_encode($pdfString)
                ]);
                exit;
            }

            $mpdf->WriteHTML($css, 1);

            $mpdf->WriteHTML($rendered, 2);
            $mpdf->Output($this->pl->txt('plugin') . '.pdf', 'D');
            exit;


        }
        //Checkt ob es der erste Durchlauf ist. Wenn True wird das erste PDF erzeugt und auf dem Server abgelegt.
        if ($printCount == 1) {
            $mpdf->WriteHTML($css, 1);
            $mpdf->WriteHTML($rendered, 2);
            $mpdf->Output($tempFile . '.pdf', 'F');
        } elseif ($printCount == $total_users) {
            /* Checkt ob es der letzte Durchlauf ist. Wenn ja wird das letzte PDF erzeugt und das vorhandene PDF auf dem Server
			 wird hinten an das erzeugte PDF angehängt. Anschliessend wird das fertige PDF dem User im Browser als Download angeboten. */
            $mpdf->WriteHTML($css, 1);
            $mpdf->WriteHTML($rendered, 2);
            $page = $mpdf->SetSourceFile($tempFile . '.pdf');
            for ($i = 1; $i <= $page; $i++) {
                $mpdf->AddPage();
                $tplID = $mpdf->ImportPage($i);
                $mpdf->UseTemplate($tplID);
            }
            $mpdf->Output($this->pl->txt("plugin") . '.pdf', 'D');
            exit;
        } else {
            /* Wenn es nicht der erste oder letzte Durchlauf ist, wird ein neues PDF erzeugt. Die bereits erzeugten PDF auf dem Server
            werden hinten angehängt. Danach wird es wieder auf dem Server gespeichert um im nächsten Durchlauf wieder anzuhängen.*/
            $mpdf->WriteHTML($css, 1);
            $mpdf->WriteHTML($rendered, 2);
            $page = $mpdf->SetSourceFile($tempFile . '.pdf');
            for ($i = 1; $i <= $page; $i++) {
                $mpdf->AddPage();
                $tplID = $mpdf->ImportPage($i);
                $mpdf->UseTemplate($tplID);
            }
            $mpdf->Output($tempFile . '.pdf', 'F');
        }
    }
}