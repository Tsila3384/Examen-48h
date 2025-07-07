<?php

class TypePret extends BaseModel {
    protected $table = 'type_pret';

    public function findWithDureeMax() {
        $stmt = $this->db->prepare("SELECT id, nom, duree_max FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
