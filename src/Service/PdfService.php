<?php

//                               ╓──────────────╖
//                               ║ PdfService   ║
//                               ╙──────────────╜

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;


class PdfService
{
  private $domPdf;

// ═════════════════════════════ CONSTRUCTOR ═══════════════════════════
  public function __construct()
  {
    $this->domPdf = new Dompdf();

    $pdfOptions = new Options();
    $pdfOptions->set("defaultFont", "Garamond");

    $this->domPdf->setOptions($pdfOptions);
  }

  // FIXME: Vérifier sortie de la fonction !
// ══════════════════════════════ ShowPdfFile ══════════════════════════════
  public function showPdfFile($html) 
  {
    $this->domPdf->loadHtml($html); 
    $this->domPdf->render();

    $this->domPdf->stream("details.pdf", [ "Attachment" => false]);
  }

// ═══════════════════════════ generateBinaryPDF ═══════════════════════════
  public function generateBinaryPDF($html) : string
  {
    $this->domPdf->loadHtml($html); 
    $this->domPdf->render();
    $this->domPdf->output();
  }
}
