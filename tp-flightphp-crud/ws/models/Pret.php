<?php

class Pret extends BaseModel {
    protected $table = 'prets';

    public function findWithDetails() {
        $stmt = $this->db->prepare("SELECT p.*, c.nom as client_nom, c.email as client_email, 
                                          tp.nom as type_pret, s.libelle as statut,
                                          e.nom as etablissement_nom
                                   FROM prets p 
                                   JOIN clients c ON p.client_id = c.id 
                                   JOIN type_pret tp ON p.type_pret_id = tp.id 
                                   JOIN statut s ON p.id_statut = s.id 
                                   JOIN etablissement e ON p.id_etablissement = e.id");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByIdWithDetails($id) {
        $stmt = $this->db->prepare("SELECT p.*, c.nom as client_nom, c.email as client_email, 
                                          tp.nom as type_pret, s.libelle as statut,
                                          e.nom as etablissement_nom
                                   FROM prets p 
                                   JOIN clients c ON p.client_id = c.id 
                                   JOIN type_pret tp ON p.type_pret_id = tp.id 
                                   JOIN statut s ON p.id_statut = s.id 
                                   JOIN etablissement e ON p.id_etablissement = e.id
                                   WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByStatus($statutId) {
        $stmt = $this->db->prepare("SELECT p.*, c.nom as client_nom, c.email as client_email, 
                                          tp.nom as type_pret, s.libelle as statut
                                   FROM prets p 
                                   JOIN clients c ON p.client_id = c.id 
                                   JOIN type_pret tp ON p.type_pret_id = tp.id 
                                   JOIN statut s ON p.id_statut = s.id 
                                   WHERE p.id_statut = ?");
        $stmt->execute([$statutId]);
        return $stmt->fetchAll();
    }

    public function calculerMensualite($montant, $tauxAnnuel, $dureeMois) {
        $tauxMensuel = ($tauxAnnuel / 100) / 12;
        if ($tauxMensuel == 0) {
            return $montant / $dureeMois;
        }
        return $montant * ($tauxMensuel * pow(1 + $tauxMensuel, $dureeMois)) / (pow(1 + $tauxMensuel, $dureeMois) - 1);
    }
}
