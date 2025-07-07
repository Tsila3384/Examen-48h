<?php
class Pret
{
    protected $db;
    protected $table = 'prets';

    public function __construct()
    {
        $this->db = getDB();
    }
    
    public function insererPret($clientId, $montant, $typePretId, $dateDebut, $duree)
    {
        // Utiliser les bonnes colonnes selon votre schéma
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (id_etablissement, client_id, montant, type_pret_id, date_demande, duree_mois, id_statut) VALUES (1, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$clientId, $montant, $typePretId, $dateDebut, $duree]);
        
        return $this->db->lastInsertId();
    }
    
    public function recupererTaux($idClient, $idTypePret)
    {
        $stmt = $this->db->prepare("SELECT taux FROM view_taux_pret WHERE client_id = ? AND type_pret_id = ?");
        $stmt->execute([$idClient, $idTypePret]);
        $result = $stmt->fetch();
        return $result ? $result['taux'] : null;
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
        $stmt = $this->db->prepare("UPDATE {$this->table} SET id_statut = 2 WHERE id = ?");
        $stmt->execute([$pretId]);
        
        $pret = $this->findById($pretId);
        if ($pret) {
            $clientId = $pret['client_id'];
            $montant = $pret['montant'];
            $typePretId = $pret['type_pret_id'];
            $dateDebut = $pret['date_debut'];
            $duree = $pret['duree'];

            // Calculer les détails de paiement
            $taux = $this->recupererTaux($clientId, $typePretId);
            if ($taux) {
                $montantTotal = $montant + ($montant * $taux / 100);
                $mensualite = $montantTotal / $duree;

                // Créer les mensualités
                $date = new DateTime($dateDebut);
                $stmtMensualite = $this->db->prepare("INSERT INTO mensualite (pret_id, client_id, montant, date_mensualite) VALUES (?, ?, ?, ?)");
                
                for ($i = 0; $i < $duree; $i++) {
                    $paymentDate = clone $date;
                    $paymentDate->modify("+$i month");
                    $stmtMensualite->execute([
                        $pretId,
                        $clientId,
                        $mensualite,
                        $paymentDate->format('Y-m-d')
                    ]);
                }
            }
        }
        return $stmt->rowCount() > 0;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function rejeterPret($pretId) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET id_statut = 3 WHERE id = ?");
        $stmt->execute([$pretId]);
        return $stmt->rowCount() > 0;
    }

}