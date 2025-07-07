<?php
require_once('fpdf186/fpdf.php');

class PDFGenerator
{
    private $debugMode = true;
    private $defaultFont = 'Arial';
    private $primaryColor = [52, 73, 94];    // Bleu foncé
    private $secondaryColor = [236, 240, 241]; // Gris clair
    private $accentColor = [41, 128, 185];    // Bleu

    public function generatePretPDF($pretDetails)
    {
        try {
            $this->debug("Debut de la generation PDF");

            // Vérification des dépendances
            $this->checkDependencies();

            // Validation et nettoyage des données
            $this->validateInputData($pretDetails);

            // Initialisation du PDF
            $pdf = new FPDF();
            $pdf->SetAutoPageBreak(true, 30);
            $pdf->AddPage();
            $pdf->SetFont($this->defaultFont, '', 12);

            // Construction du PDF avec boîtes
            $this->addModernHeader($pdf, $pretDetails);
            $this->addClientInfoBox($pdf, $pretDetails);
            $this->addLoanDetailsBox($pdf, $pretDetails);
            $this->addCalculationsBox($pdf, $pretDetails);
            $this->addModernFooter($pdf);

            // Génération du fichier
            $filename = $this->generateFilename($pretDetails['id']);
            $pdf->Output('D', $filename);

            return true;

        } catch (Exception $e) {
            $this->logError($e);
            throw new Exception("Erreur generation PDF: " . $e->getMessage());
        }
    }

    private function checkDependencies()
    {
        if (!class_exists('FPDF')) {
            throw new Exception("La classe FPDF n'est pas disponible");
        }
    }

    private function validateInputData(&$pretDetails)
    {
        $requiredFields = [
            'id', 'client_nom', 'type_pret', 'montant', 
            'statut', 'date_demande', 'duree_mois'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($pretDetails[$field])) {
                throw new Exception("Champ requis manquant: $field");
            }
        }

        // Nettoyage des caractères spéciaux
        $pretDetails = array_map([$this, 'cleanText'], $pretDetails);

        // Valeurs par défaut
        $pretDetails['taux_interet'] = $pretDetails['taux_interet'] ?? 0;
        $pretDetails['client_email'] = $pretDetails['client_email'] ?? 'Non specifie';
        $pretDetails['client_salaire'] = $pretDetails['client_salaire'] ?? 0;
        $pretDetails['type_client'] = $pretDetails['type_client'] ?? 'Non specifie';
    }

    private function cleanText($text)
    {
        if (!is_string($text)) return $text;
        
        // Conversion des caractères spéciaux
        $replacements = [
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
            'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'í' => 'i',
            'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'ó' => 'o',
            'ç' => 'c', 'ñ' => 'n',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Á' => 'A',
            'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ú' => 'U',
            'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Í' => 'I',
            'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Ó' => 'O',
            'Ç' => 'C', 'Ñ' => 'N',
            "'" => "'", '"' => '"', '"' => '"', '–' => '-', '—' => '-'
        ];

        return strtr($text, $replacements);
    }

    private function addModernHeader($pdf, $pretDetails)
    {
        // Boîte d'en-tête avec couleur
        $pdf->SetFillColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $pdf->Rect(10, 10, 190, 25, 'F');

        // Titre en blanc
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont($this->defaultFont, 'B', 18);
        $pdf->SetXY(10, 20);
        $pdf->Cell(190, 10, 'CONTRAT DE PRET NUMERO ' . $pretDetails['id'], 0, 1, 'C');

        // Retour aux couleurs normales
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(15);
    }

    private function addClientInfoBox($pdf, $pretDetails)
    {
        $startY = $pdf->GetY();
        
        // Titre de la section
        $this->addSectionTitle($pdf, 'INFORMATIONS CLIENT');
        
        // Boîte principale
        $pdf->SetFillColor($this->secondaryColor[0], $this->secondaryColor[1], $this->secondaryColor[2]);
        $pdf->Rect(15, $pdf->GetY(), 180, 50, 'F');
        
        // Bordure
        $pdf->SetDrawColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $pdf->Rect(15, $pdf->GetY(), 180, 50, 'D');
        
        // Contenu avec espacement
        $pdf->SetXY(20, $pdf->GetY() + 5);
        $this->addBoxInfoLine($pdf, 'Nom:', $pretDetails['client_nom']);
        $this->addBoxInfoLine($pdf, 'Email:', $pretDetails['client_email']);
        $this->addBoxInfoLine($pdf, 'Type client:', $pretDetails['type_client']);
        $this->addBoxInfoLine($pdf, 'Salaire:', $this->formatAmount($pretDetails['client_salaire']));
        
        $pdf->Ln(20);
    }

    private function addLoanDetailsBox($pdf, $pretDetails)
    {
        // Titre de la section
        $this->addSectionTitle($pdf, 'DETAILS DU PRET');
        
        // Boîte principale
        $pdf->SetFillColor($this->secondaryColor[0], $this->secondaryColor[1], $this->secondaryColor[2]);
        $pdf->Rect(15, $pdf->GetY(), 180, 60, 'F');
        
        // Bordure
        $pdf->SetDrawColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $pdf->Rect(15, $pdf->GetY(), 180, 60, 'D');
        
        // Contenu
        $pdf->SetXY(20, $pdf->GetY() + 5);
        $this->addBoxInfoLine($pdf, 'Type de pret:', $pretDetails['type_pret']);
        $this->addBoxInfoLine($pdf, 'Montant:', $this->formatAmount($pretDetails['montant']));
        $this->addBoxInfoLine($pdf, 'Taux d\'interet:', $pretDetails['taux_interet'] . '%');
        $this->addBoxInfoLine($pdf, 'Duree (mois):', $pretDetails['duree_mois']);
        $this->addBoxInfoLine($pdf, 'Statut:', $pretDetails['statut']);
        $this->addBoxInfoLine($pdf, 'Date demande:', $this->formatDate($pretDetails['date_demande']));
        
        $pdf->Ln(20);
    }

    private function addCalculationsBox($pdf, $pretDetails)
    {
        // Titre de la section
        $this->addSectionTitle($pdf, 'CALCULS FINANCIERS');
        
        // Calculs
        $montant = floatval($pretDetails['montant']);
        $taux = floatval($pretDetails['taux_interet']);
        $duree = intval($pretDetails['duree_mois']);
        
        $interets = 0;
        $total = $montant;
        $mensualite = 0;
        
        if ($duree > 0) {
            $interets = ($montant * $taux / 100) * ($duree / 12);
            $total = $montant + $interets;
            $mensualite = $total / $duree;
        }
        
        // Boîte de calculs
        $pdf->SetFillColor(248, 249, 250);
        $pdf->Rect(15, $pdf->GetY(), 180, 45, 'F');
        
        // Bordure accent
        $pdf->SetDrawColor($this->accentColor[0], $this->accentColor[1], $this->accentColor[2]);
        $pdf->Rect(15, $pdf->GetY(), 180, 45, 'D');
        
        // Contenu
        $pdf->SetXY(20, $pdf->GetY() + 5);
        $this->addBoxInfoLine($pdf, 'Interets totaux:', $this->formatAmount($interets));
        $this->addBoxInfoLine($pdf, 'Mensualite:', $this->formatAmount($mensualite));
        
        // Ligne de total mise en évidence
        $pdf->SetFont($this->defaultFont, 'B', 12);
        $pdf->SetFillColor(255, 248, 220);
        $pdf->Rect(20, $pdf->GetY(), 170, 10, 'F');
        $this->addBoxInfoLine($pdf, 'TOTAL A REMBOURSER:', $this->formatAmount($total));
        $pdf->SetFont($this->defaultFont, '', 12);
        
        $pdf->Ln(10);
    }

    private function addSectionTitle($pdf, $title)
    {
        $pdf->SetFont($this->defaultFont, 'B', 12);
        $pdf->SetTextColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        
        // Petite barre colorée à gauche
        $pdf->SetFillColor($this->accentColor[0], $this->accentColor[1], $this->accentColor[2]);
        $pdf->Rect(15, $pdf->GetY(), 3, 8, 'F');
        
        $pdf->SetX(22);
        $pdf->Cell(0, 8, $title, 0, 1, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);
    }

    private function addBoxInfoLine($pdf, $label, $value)
    {
        $pdf->SetFont($this->defaultFont, 'B', 10);
        $pdf->Cell(60, 8, $label, 0, 0, 'L');
        
        $pdf->SetFont($this->defaultFont, '', 10);
        $pdf->Cell(0, 8, $value, 0, 1, 'L');
        
        $pdf->SetX(20); // Retour à la marge de la boîte
    }

    private function addModernFooter($pdf)
    {
    
    }

    private function formatAmount($amount)
    {
        return number_format(floatval($amount), 2, ',', ' ') . ' Ar';
    }

    private function formatDate($dateString)
    {
        try {
            $date = new DateTime($dateString);
            return $date->format('d/m/Y');
        } catch (Exception $e) {
            return $dateString;
        }
    }

    private function generateFilename($pretId)
    {
        return 'Contrat_Pret_' . $pretId . '_' . date('Ymd_His') . '.pdf';
    }

    private function debug($message)
    {
        if ($this->debugMode) {
            error_log("PDF DEBUG: " . $message);
        }
    }

    private function logError($exception)
    {
        error_log("PDF ERROR: " . $exception->getMessage());
        error_log("Stack trace: " . $exception->getTraceAsString());
    }
}

// Exemple d'utilisation
/*
$pretDetails = [
    'id' => 'PR2024001',
    'client_nom' => 'Jean Dupont',
    'client_email' => 'jean.dupont@email.com',
    'type_client' => 'Particulier',
    'client_salaire' => 2500000,
    'type_pret' => 'Pret immobilier',
    'montant' => 50000000,
    'taux_interet' => 8.5,
    'duree_mois' => 240,
    'statut' => 'Approuve',
    'date_demande' => '2024-01-15'
];

$generator = new PDFGenerator();
$generator->generatePretPDF($pretDetails);
*/
?>