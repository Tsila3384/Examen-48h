<?php

class TypeClient extends BaseModel {
    protected $table = 'type_client';

    public function getTauxForPret($typeClientId, $typePretId) {
        $stmt = $this->db->prepare("SELECT taux_interet FROM taux 
                                   WHERE type_client_id = ? AND type_pret_id = ?");
        $stmt->execute([$typeClientId, $typePretId]);
        $result = $stmt->fetch();
        return $result ? $result['taux_interet'] : null;
    }
}
