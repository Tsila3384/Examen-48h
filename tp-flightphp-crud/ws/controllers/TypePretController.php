<?php
<<<<<<< Updated upstream
require_once 'models/TypePret.php';

class TypePretController
{
    private $typePretModel;

    public function __construct()
    {
        $this->typePretModel = new TypePret();
    }

    // Affiche le formulaire de création
    public function create()
    {
        Flight::render('admin/AddTypePret', [
            'base_url' => BASE_URL
        ]);
    }
// Création d'un type de prêt
    public function store()
    {
        $data = Flight::request()->data->getData();
        
        if ($this->typePretModel->create($data)) {
            Flight::redirect('/admin/types-pret?success=Type créé avec succès');
        } else {
            Flight::redirect('/admin/types-pret/create?error=Erreur lors de la création');
        }
    }

    // Mise à jour d'un type de prêt
    public function update($id)
    {
        $data = Flight::request()->data->getData();
        
        if ($this->typePretModel->update($id, $data)) {
            Flight::redirect('/admin/types-pret?success=Type mis à jour');
        } else {
            Flight::redirect("/admin/types-pret/edit/$id?error=Erreur lors de la mise à jour");
        }
    }

    // Suppression d'un type de prêt
    public function destroy($id)
    {
        if ($this->typePretModel->delete($id)) {
            Flight::redirect('/admin/types-pret?success=Type supprimé avec succès');
        } else {
            Flight::redirect('/admin/types-pret?error=Erreur lors de la suppression');
        }
    }


    // Affiche le formulaire d'édition
    public function edit($id) 
    {
        $type = $this->typePretModel->findById($id);
        
        if (!$type) {
            Flight::halt(404, 'Type de prêt non trouvé');
            return;
        }

        Flight::render('admin/UpdateType', [
            'type' => $type,
            'base_url' => BASE_URL
        ]);
    }

    // Affiche la liste complète (admin)
    public function getAllTypes()
    {
        $types = $this->typePretModel->findAll();
        Flight::render('admin/template/template', [
            'page' => 'typePret',
            'types' => $types,
            'base_url' => BASE_URL
        ]);
    }

    // Affiche les types disponibles pour un client
    public function getTypesByUser($userId)
    {
        $types = $this->typePretModel->findAllByUser($userId);
        Flight::render('client/template/template', [
            'page' => 'typePret',
            'types' => $types,
            'base_url' => BASE_URL
        ]);
    }


    // Définit un taux pour un type de prêt
    public function setTaux($type_pret_id, $type_client_id)
    {
        $taux = Flight::request()->data->taux_interet;
        if ($this->typePretModel->setTaux($type_pret_id, $type_client_id, $taux)) {
            Flight::json(['success' => true, 'message' => 'Taux mis à jour']);
        } else {
            Flight::json(['error' => 'Erreur de mise à jour'], 500);
        }
    }
=======

use flight\net\Response;

require 'vendor/autoload.php';
require_once 'models/TypePret.php';

// TypePretController.php
class TypePretController {
    private $typePretModel;

    public function __construct() {
        $this->typePretModel = new TypePret();
    }

    public function getAllTypes() {
        $types = $this->typePretModel->findAll();
        Flight::render('admin/template/template', [
            'page' => 'typePret',
            'types' => $types
        ]);
    }

    public function getTypesByUser($userId) {
        $types = $this->typePretModel->findAllByUser($userId);
        Flight::render('client/template', [
            'page' => 'typesPret',
            'types' => $types
        ]);
    }
>>>>>>> Stashed changes
}