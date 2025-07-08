<?php
require_once __DIR__ . '/../db.php';

class Pret
{
    protected $db;
    protected $table = 'prets';

    public function __construct()
    {
        $this->db = getDB();
    }

    public function insererPret($clientId, $montant, $typePretId, $dateDebut, $duree, $tauxAssurance, $delaiPremierRemboursement)
    {
        // Vérifier les fonds disponibles de l'établissement avant l'insertion
        $stmtFonds = $this->db->prepare("SELECT fonds_disponibles FROM etablissement WHERE id = 1");
        $stmtFonds->execute();
        $etablissement = $stmtFonds->fetch(PDO::FETCH_ASSOC);
        
        if (!$etablissement) {
            throw new Exception("Établissement introuvable");
        }
        
        $fondsDisponibles = floatval($etablissement['fonds_disponibles']);
        $montantDemande = floatval($montant);
        
        if ($fondsDisponibles < $montantDemande) {
            throw new Exception("Fonds insuffisants dans l'établissement.");
        }
        
        error_log("insererPret: Vérification des fonds OK - Fonds disponibles: $fondsDisponibles €, Montant demandé: $montantDemande €");
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (id_etablissement, client_id, montant, type_pret_id, date_demande, duree_mois, id_statut, taux_assurance, delai_premier_remboursement) VALUES (1, ?, ?, ?, ?, ?, 1, ?, ?)");
        $stmt->execute([$clientId, $montant, $typePretId, $dateDebut, $duree, $tauxAssurance, $delaiPremierRemboursement]);
        return $this->db->lastInsertId();
    }

    public function recupererTaux($idClient, $idTypePret)
    {
        $stmt = $this->db->prepare("SELECT taux FROM view_taux_pret WHERE client_id = ? AND type_pret_id = ?");
        $stmt->execute([$idClient, $idTypePret]);
        $result = $stmt->fetch();
        $taux = $result ? $result['taux'] : 3.0;
        error_log("recupererTaux: client_id=$idClient, type_pret_id=$idTypePret, taux=$taux");
        return $taux;
    }

    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT p.*, c.nom as client_nom, tp.nom as type_pret_nom, s.libelle as statut_libelle 
                                   FROM {$this->table} p 
                                   LEFT JOIN clients c ON p.client_id = c.id 
                                   LEFT JOIN type_pret tp ON p.type_pret_id = tp.id 
                                   LEFT JOIN statut s ON p.id_statut = s.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validerPret($pretId)
    {
        $this->db->beginTransaction();

        try {
            error_log("validerPret: Starting validation for pret_id=$pretId");

            // Mettre à jour le statut du prêt
            $stmt = $this->db->prepare("UPDATE {$this->table} SET id_statut = 2 WHERE id = ?");
            $stmt->execute([$pretId]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Impossible de mettre à jour le statut du prêt");
            }

            // Récupérer les détails du prêt
            $pret = $this->findById($pretId);
            if (!$pret) {
                throw new Exception("Prêt introuvable");
            }

            $clientId = $pret['client_id'];
            $montant = $pret['montant'];
            $typePretId = $pret['type_pret_id'];
            $dateDebut = $pret['date_demande'];
            $duree = $pret['duree_mois'];
            $tauxAssurance = $pret['taux_assurance'];
            $delaiPremierRemboursement = $pret['delai_premier_remboursement'];

            error_log("validerPret: pret_id=$pretId, client_id=$clientId, montant=$montant, type_pret_id=$typePretId, date_demande=$dateDebut, duree_mois=$duree, taux_assurance=$tauxAssurance, delai_premier_remboursement=$delaiPremierRemboursement");

            // Calculer les détails de paiement (annuité constante)
            $taux = $this->recupererTaux($clientId, $typePretId);
            if (!$taux) {
                throw new Exception("Impossible de récupérer le taux pour ce prêt");
            }

            // Conversion du taux annuel en taux mensuel (taux / 100 / 12)
            $tauxMensuel = $taux / 1200;
            error_log("validerPret: taux=$taux%, taux_mensuel=$tauxMensuel");

            // Calcul de la mensualité selon la formule d'annuité constante
            if ($tauxMensuel > 0) {
                // Formule classique pour taux > 0
                $mensualiteBase = $montant * ($tauxMensuel * pow(1 + $tauxMensuel, $duree)) / (pow(1 + $tauxMensuel, $duree) - 1);
            } else {
                // Si taux = 0, mensualité = montant / durée
                $mensualiteBase = $montant / $duree;
            }
            
            // Calcul de l'assurance répartie sur toute la durée
            $assuranceMensuelle = ($montant * ($tauxAssurance / 100)) / 12;
            
            // Arrondis avec précision
            $mensualiteBase = round($mensualiteBase, 2);
            $assuranceMensuelle = round($assuranceMensuelle, 2);
            $mensualiteTotale = $mensualiteBase + $assuranceMensuelle;

            error_log("validerPret: mensualite_base=$mensualiteBase, assurance_mensuelle=$assuranceMensuelle, mensualite_totale=$mensualiteTotale");

            // Créer les mensualités avec calcul précis du capital et des intérêts
            $date = new DateTime($dateDebut);
            if ($delaiPremierRemboursement > 0) {
                $date->modify("+$delaiPremierRemboursement month");
            }
            
            $stmtMensualite = $this->db->prepare("INSERT INTO mensualite (pret_id, client_id, montant, montant_capital, montant_interets, montant_assurance, date_mensualite) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $capitalRestant = floatval($montant);
            $totalCapitalRembourse = 0;
            $totalInteretsPayes = 0;
            
            for ($i = 0; $i < $duree; $i++) {
                $paymentDate = clone $date;
                $paymentDate->modify("+$i month");

                // Calcul des intérêts sur le capital restant dû
                $interetsMois = $capitalRestant * $tauxMensuel;
                
                // Calcul du capital remboursé
                if ($i == $duree - 1) {
                    // Dernière mensualité : on solde exactement le capital restant
                    $capitalRembourse = $capitalRestant;
                    $mensualiteActuelle = $capitalRembourse + $interetsMois + $assuranceMensuelle;
                } else {
                    // Mensualités normales
                    $capitalRembourse = $mensualiteBase - $interetsMois;
                    $mensualiteActuelle = $mensualiteTotale;
                }
                
                // Mise à jour du capital restant
                $capitalRestant = $capitalRestant - $capitalRembourse;
                
                // Suivi des totaux pour vérification
                $totalCapitalRembourse += $capitalRembourse;
                $totalInteretsPayes += $interetsMois;
                
                // Arrondis finaux pour la base de données
                $interetsMoisArrondi = round($interetsMois, 2);
                $capitalRemburseArrondi = round($capitalRembourse, 2);
                $assuranceMensuelleArrondie = round($assuranceMensuelle, 2);
                $mensualiteActuelleArrondie = round($mensualiteActuelle, 2);
                $capitalRestantArrondi = round(max($capitalRestant, 0), 2);

                error_log("validerPret: Mois " . ($i + 1) . ", date=" . $paymentDate->format('Y-m-d') . 
                         ", capital_restant_avant=$capitalRestantArrondi, interets=$interetsMoisArrondi" . 
                         ", capital_rembourse=$capitalRemburseArrondi, assurance=$assuranceMensuelleArrondie" . 
                         ", mensualite=$mensualiteActuelleArrondie");

                $stmtMensualite->execute([
                    $pretId,
                    $clientId,
                    $mensualiteActuelleArrondie,
                    $capitalRemburseArrondi,
                    $interetsMoisArrondi,
                    $assuranceMensuelleArrondie,
                    $paymentDate->format('Y-m-d')
                ]);
            }
            
            error_log("validerPret: Fin calculs - Capital total remboursé: " . round($totalCapitalRembourse, 2) . 
                     ", Intérêts totaux: " . round($totalInteretsPayes, 2) . 
                     ", Montant initial: $montant");

            // Mettre à jour les fonds disponibles de l'établissement
            $stmtEtablissement = $this->db->prepare("UPDATE etablissement SET fonds_disponibles = fonds_disponibles - ? WHERE id = ?");
            $stmtEtablissement->execute([$montant, $pret['id_etablissement']]);

            // Enregistrer l'opération dans l'historique
            $stmtHistorique = $this->db->prepare("INSERT INTO historique_fonds (id_etablissement, montant, id_type_operation, date_operation) VALUES (?, ?, 2, NOW())");
            $stmtHistorique->execute([$pret['id_etablissement'], $montant]);

            // Valider la transaction
            $this->db->commit();
            error_log("validerPret: Validation successful for pret_id=$pretId");
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("validerPret: Erreur lors de la validation du prêt {$pretId}: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT p.*, tp.nom as type_pret_nom, c.nom as client_nom 
                                   FROM {$this->table} p 
                                   LEFT JOIN type_pret tp ON p.type_pret_id = tp.id 
                                   LEFT JOIN clients c ON p.client_id = c.id 
                                   WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByClientId($clientId)
    {
        $stmt = $this->db->prepare("SELECT p.*, c.nom as client_nom, tp.nom as type_pret_nom, s.libelle as statut_libelle 
                                   FROM {$this->table} p 
                                   LEFT JOIN clients c ON p.client_id = c.id 
                                   LEFT JOIN type_pret tp ON p.type_pret_id = tp.id 
                                   LEFT JOIN statut s ON p.id_statut = s.id 
                                   WHERE p.client_id = ?");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function rejeterPret($pretId)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET id_statut = 3 WHERE id = ?");
        $stmt->execute([$pretId]);
        return $stmt->rowCount() > 0;
    }
    public function getPretDetailsForPDF($pretId)
    {
        $query = "
        SELECT 
            p.*,
            c.nom AS client_nom,
            c.email AS client_email,
            c.salaire AS client_salaire,
            tc.libelle AS type_client,
            tp.nom AS type_pret,
            s.libelle AS statut,
            COALESCE(t.taux_interet, 0) AS taux_interet
        FROM prets p
        JOIN clients c ON p.client_id = c.id
        JOIN type_client tc ON c.type_client_id = tc.id
        JOIN type_pret tp ON p.type_pret_id = tp.id
        JOIN statut s ON p.id_statut = s.id
        LEFT JOIN taux t ON t.type_client_id = tc.id AND t.type_pret_id = tp.id
        WHERE p.id = :pretId
    ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':pretId', $pretId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("Aucun résultat trouvé pour le prêt ID: $pretId");
        }

        return $result;
        }
    public function InteretsParMois() {
        $stmt = $this->db->prepare("SELECT * FROM view_interet_par_mois");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function InteretsParMoisAnnee($dateDebut, $dateFin) {
        // Convertir les dates en format YYYY-MM pour la comparaison
        $debutFormate = date('Y-m', strtotime($dateDebut . '-01'));
        $finFormate = date('Y-m', strtotime($dateFin . '-01'));
        
        $stmt = $this->db->prepare("
            SELECT * FROM view_interet_par_mois 
            WHERE AnneeMois BETWEEN ? AND ? 
            ORDER BY AnneeMois
        ");
        $stmt->execute([$debutFormate, $finFormate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function calculerAmortissement($montant, $taux, $duree, $tauxAssurance, $delaiPremierRemboursement)
    {
        error_log("calculerAmortissement: montant=$montant, taux=$taux, duree=$duree, taux_assurance=$tauxAssurance, delai_premier_remboursement=$delaiPremierRemboursement");

        // Conversion du taux annuel en taux mensuel
        $tauxMensuel = $taux / 100 / 12;
        error_log("calculerAmortissement: taux_mensuel=$tauxMensuel");

        $mensualite = round($montant * ($tauxMensuel * pow(1 + $tauxMensuel, $duree)) / (pow(1 + $tauxMensuel, $duree) - 1), 2);        
        $assuranceMensuelle = round(($montant * ($tauxAssurance / 100)) / 12, 2);
        $mensualiteTotale = $mensualite + $assuranceMensuelle;

        error_log("calculerAmortissement: mensualite_base=$mensualite, assurance_mensuelle=$assuranceMensuelle, mensualite_totale=$mensualiteTotale");

        $amortissement = [];
        $capitalRestant = floatval($montant);
        $date = new DateTime();
        if ($delaiPremierRemboursement > 0) {
            $date->modify("+$delaiPremierRemboursement month");
        }

        for ($i = 0; $i < $duree; $i++) {
            $paymentDate = clone $date;
            $paymentDate->modify("+$i month");
            
            // Calcul des intérêts sur le capital restant
            $interets = $capitalRestant * $tauxMensuel;
            
            // Calcul du capital remboursé
            if ($i == $duree - 1) {
                // Dernière mensualité : solder exactement le capital restant
                $capitalRembourse = $capitalRestant;
                $mensualiteActuelle = $capitalRembourse + $interets + $assuranceMensuelle;
            } else {
                $capitalRembourse = $mensualite - $interets;
                $mensualiteActuelle = $mensualiteTotale;
            }
            
            $capitalRestant = $capitalRestant - $capitalRembourse;

            error_log("calculerAmortissement: Mois " . ($i + 1) . ", date=" . $paymentDate->format('Y-m-d') . 
                     ", capital_restant=" . round(max($capitalRestant, 0), 2) . 
                     ", interets=" . round($interets, 2) . 
                     ", capital_rembourse=" . round($capitalRembourse, 2) . 
                     ", assurance=" . round($assuranceMensuelle, 2) . 
                     ", mensualite=" . round($mensualiteActuelle, 2));

            $amortissement[] = [
                'mois' => $i + 1,
                'date' => $paymentDate->format('Y-m-d'),
                'mensualite' => round($mensualiteActuelle, 2),
                'capital' => round($capitalRembourse, 2),
                'interets' => round($interets, 2),
                'assurance' => round($assuranceMensuelle, 2),
                'capital_restant' => round(max($capitalRestant, 0), 2)
            ];
        }

        return $amortissement;
    }

    public function detailPret($pretId)
    {
        $stmt = $this->db->prepare("select * from DetailPret where pret_id = ? LIMIT 1");
        $stmt->execute([$pretId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}
