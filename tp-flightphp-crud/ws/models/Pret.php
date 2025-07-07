<?php
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

            $tauxMensuel = $taux / 1200; // Convertir taux annuel en taux mensuel
            error_log("validerPret: taux=$taux, taux_mensuel=$tauxMensuel");

            // Calcul de la mensualité avec arrondi à 2 décimales
            $mensualite = round($montant * ($tauxMensuel * pow(1 + $tauxMensuel, $duree)) / (pow(1 + $tauxMensuel, $duree) - 1), 2);
            $assuranceMensuelle = round(($montant * ($tauxAssurance / 100)) / 12, 2);
            $mensualiteTotale = $mensualite + $assuranceMensuelle;

            error_log("validerPret: mensualite_base=$mensualite, assurance_mensuelle=$assuranceMensuelle, mensualite_totale=$mensualiteTotale");

            // Créer les mensualités
            $date = new DateTime($dateDebut);
            $date->modify("+$delaiPremierRemboursement month");
            $stmtMensualite = $this->db->prepare("INSERT INTO mensualite (pret_id, client_id, montant, montant_capital, montant_interets, montant_assurance, date_mensualite) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $capitalRestant = $montant;
            for ($i = 0; $i < $duree; $i++) {
                $paymentDate = clone $date;
                $paymentDate->modify("+$i month");

                $interets = round($capitalRestant * $tauxMensuel, 2);
                
                // Pour le dernier mois, ajuster le capital remboursé pour éviter un capital restant négatif
                if ($i == $duree - 1 && $capitalRestant < $mensualite) {
                    $capitalRembourse = $capitalRestant;
                    $mensualiteTotale = round($capitalRembourse + $interets + $assuranceMensuelle, 2);
                } else {
                    $capitalRembourse = round($mensualite - $interets, 2);
                }
                
                $capitalRestant = round($capitalRestant - $capitalRembourse, 2);

                error_log("validerPret: Mois " . ($i + 1) . ", date=" . $paymentDate->format('Y-m-d') . ", capital_restant=$capitalRestant, interets=$interets, capital_rembourse=$capitalRembourse, assurance=$assuranceMensuelle, mensualite_totale=$mensualiteTotale");

                $stmtMensualite->execute([
                    $pretId,
                    $clientId,
                    $mensualiteTotale,
                    $capitalRembourse,
                    $interets,
                    $assuranceMensuelle,
                    $paymentDate->format('Y-m-d')
                ]);
            }

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

    public function rejeterPret($pretId)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET id_statut = 3 WHERE id = ?");
        $stmt->execute([$pretId]);
        return $stmt->rowCount() > 0;
    }

    public function calculerAmortissement($montant, $taux, $duree, $tauxAssurance, $delaiPremierRemboursement)
    {
        error_log("calculerAmortissement: montant=$montant, taux=$taux, duree=$duree, taux_assurance=$tauxAssurance, delai_premier_remboursement=$delaiPremierRemboursement");

        $tauxMensuel = $taux / 100 / 12;
        error_log("calculerAmortissement: taux_mensuel=$tauxMensuel");

        $mensualite = round($montant * ($tauxMensuel * pow(1 + $tauxMensuel, $duree)) / (pow(1 + $tauxMensuel, $duree) - 1), 2);        
        $assuranceMensuelle = round(($montant * ($tauxAssurance / 100)) / 12, 2);
        $mensualiteTotale = $mensualite + $assuranceMensuelle;

        error_log("calculerAmortissement: mensualite_base=$mensualite, assurance_mensuelle=$assuranceMensuelle, mensualite_totale=$mensualiteTotale");

        $amortissement = [];
        $capitalRestant = $montant;
        $date = new DateTime();
        $date->modify("+$delaiPremierRemboursement month");

        for ($i = 0; $i < $duree; $i++) {
            $paymentDate = clone $date;
            $paymentDate->modify("+$i month");
            $interets = $capitalRestant * $tauxMensuel;
            $capitalRembourse = $mensualite - $interets;
            $capitalRestant -= $capitalRembourse;

            error_log("calculerAmortissement: Mois " . ($i + 1) . ", date=" . $paymentDate->format('Y-m-d') . ", capital_restant=$capitalRestant, interets=$interets, capital_rembourse=$capitalRembourse, assurance=$assuranceMensuelle, mensualite_totale=$mensualiteTotale");

            $amortissement[] = [
                'mois' => $i + 1,
                'date' => $paymentDate->format('Y-m-d'),
                'mensualite' => round($mensualiteTotale, 2),
                'capital' => round($capitalRembourse, 2),
                'interets' => round($interets, 2),
                'assurance' => round($assuranceMensuelle, 2),
                'capital_restant' => round(max($capitalRestant, 0), 2)
            ];
        }

        return $amortissement;
    }
}