<?php

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/commondocgenerator.class.php';
class pdf_calypso_controldocument extends CommonDocGenerator
{
    public $name = 'Calypso';

    public $description = 'Modèle PDF pour les contrôles Digiquali';

    public $type = 'controldocument';

    public $page_largeur;

    public $page_hauteur;

    public $format;

    public $marge_gauche;

    public $marge_droite;

    public $marge_haute;

    public $marge_basse;

    public function __construct($db)
    {
        global $conf;

        $this->db = $db;
        $this->name = "Calypso";
        $this->description = "Modèle PDF personnalisé pour contrôles";
        $this->type = 'controldocument';

        // Page setup
        $this->page_largeur = 210;
        $this->page_hauteur = 297;
        $this->format = array($this->page_largeur, $this->page_hauteur);
        $this->marge_gauche = 10;
        $this->marge_droite = 10;
        $this->marge_haute = 10;
        $this->marge_basse = 10;
    }

    public function write_file($object, $outputlangs)
    {
        global $user, $conf;

        if (!is_object($outputlangs)) {
            require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
            $outputlangs = new Translate('', $conf);
        }

        $outputlangs->load("main");

        $pdf = pdf_getInstance($this->format, 'P');
        $pdf->SetAutoPageBreak(1, 10);
        $pdf->SetFont(pdf_getPDFFont($outputlangs), '', 10);
        $pdf->Open();
        $pdf->AddPage();

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('', 'B', 14);
        $pdf->MultiCell(0, 5, "Contrôle Digiquali : ".$object->ref, 0, 'L');

        $pdf->Ln(5);
        $pdf->SetFont('', '', 10);
        $pdf->MultiCell(0, 5, "Description : ".$object->description, 0, 'L');
        $pdf->MultiCell(0, 5, "Date prévue : ".$object->date_creation, 0, 'L');

        $filename = dol_sanitizeFileName($object->ref).".pdf";
        $filedir = $conf->digiquali->dir_output."/controldocument/".dol_sanitizeFileName($object->ref);
        dol_mkdir($filedir);
        $file = $filedir."/".$filename;



        $pdf->Output($file, 'F');

        $object->result = array('fullpath' => $file);

        return 1;
    }
}
