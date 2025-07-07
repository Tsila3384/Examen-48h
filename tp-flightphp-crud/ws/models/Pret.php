<?php
class Pret {
    protected $db;
    protected $table = 'prets';

    public function __construct(){
        $this->db = getDB();
    }
    public function insererPret($clientId, $montant, $typePretId, $dateDebut, $duree) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (client_id, montant, type_pret_id, date_debut, duree, id_statut) VALUES (?, ?, ?, ?, ?, 2)");
        $stmt->execute([$clientId, $montant, $typePretId, $dateDebut, $duree]);
        $taux = $this->recupererTaux($clientId, $typePretId);
        $montantTotal = $montant + ($montant * $taux / 100);
        $mensualite = $montantTotal / $duree;
            // Get the ID of the newly inserted loan
            $pretId = $this->db->lastInsertId();

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
    public function recupererTaux($idClient, $idTypePret) {
        $stmt = $this->db->prepare("SELECT taux FROM view_taux_pret WHERE client_id = ? AND type_pret_id = ?");
        $stmt->execute([$idClient, $idTypePret]);
        $result = $stmt->fetch();
        return $result ? $result['taux'] : null;
    }

    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validerPret($pretId) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET id_statut = 2 WHERE id = ?");
        $stmt->execute([$pretId]);
        $pret = $this->findById($pretId);
        if ($pret) {
            $clientId = $pret['client_id'];
            $montant = $pret['montant'];
            $typePretId = $pret['type_pret_id'];
            $dateDebut = $pret['date_debut'];
            $duree = $pret['duree'];

            // Calculate payment details
            $taux = $this->recupererTaux($clientId, $typePretId);
            $montantTotal = $montant + ($montant * $taux / 100);
            $mensualite = $montantTotal / $duree;

            // Create monthly payments
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
        return $stmt->rowCount() > 0;
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}