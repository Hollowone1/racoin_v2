<?php

namespace App\controllers\createItem;

use AllowDynamicProperties;
use app\models\Annonce;
use app\models\Annonceur;
use app\models\Departement;
use app\models\Photo;
use app\models\Categorie;

#[AllowDynamicProperties] class item {
    public function __construct(){
    }
    function afficherItem($twig, $menu, $chemin, $n, $cat): void
    {

        $this->annonce = Annonce::find($n);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }

        $menu = array(
            array('href' => $chemin,
                'text' => 'Acceuil'),
            array('href' => $chemin."/cat/".$n,
                'text' => Categorie::find($this->annonce->id_categorie)?->nom_categorie),
            array('href' => $chemin."/item/".$n,
            'text' => $this->annonce->titre)
        );

        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);
        $this->departement = Departement::find($this->annonce->id_departement );
        $this->photo = Photo::where('id_annonce', '=', $n)->get();
        $template = $twig->load("item.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "annonceur" => $this->annonceur,
            "dep" => $this->departement->nom_departement,
            "photo" => $this->photo,
            "categories" => $cat));
    }

    function supprimerItemGet($twig, $menu, $chemin,$n){
        $this->annonce = Annonce::find($n);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }
        $template = $twig->load("delGet.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce));
    }


    function supprimerItemPost($twig, $menu, $chemin, $n, $cat){
        $this->annonce = Annonce::find($n);
        $reponse = false;
        if(password_verify($_POST["pass"],$this->annonce->mdp)){
            $reponse = true;
            photo::where('id_annonce', '=', $n)->delete();
            $this->annonce->delete();

        }

        $template = $twig->load("delPost.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "pass" => $reponse,
            "categories" => $cat));
    }

    function modifyGet($twig, $menu, $chemin, $id){
        $this->annonce = Annonce::find($id);
        if(!isset($this->annonce)){
            echo "404";
            return;
        }
        $template = $twig->load("modifyGet.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce));
    }

    function modifyPost($twig, $menu, $chemin, $n, $cat, $dpt){
        $this->annonce = Annonce::find($n);
        $this->annonceur = Annonceur::find($this->annonce->id_annonceur);
        $this->categItem = Categorie::find($this->annonce->id_categorie)->nom_categorie;
        $this->dptItem = Departement::find($this->annonce->id_departement)->nom_departement;

        $reponse = false;
        if(password_verify($_POST["pass"],$this->annonce->mdp)){
            $reponse = true;

        }

        $template = $twig->load("modifyPost.html.twig");
        echo $template->render(array("breadcrumb" => $menu,
            "chemin" => $chemin,
            "annonce" => $this->annonce,
            "annonceur" => $this->annonceur,
            "pass" => $reponse,
            "categories" => $cat,
            "departements" => $dpt,
            "dptItem" => $this->dptItem,
            "categItem" => $this->categItem));
    }

    function edit($twig, $menu, $chemin, $allPostVars, $id)
{
    date_default_timezone_set('Europe/Paris');

    $errors = $this->validateFormInputs($allPostVars);

    if (empty($errors)) {
        $annonces = Annonce::where('id', $id)->with('annonceur')->get();
        if (count($annonces) > 0) {
            $annonce = $annonces[0];
            $annonceur = $annonce->annonceur;

            $this->updateAnnonceurModel($annonceur, $allPostVars);
            $this->updateAnnonceModel($annonce, $allPostVars);

            $template = $twig->load("modif-confirm.html.twig");
            echo $template->render(array("breadcrumb" => $menu, "chemin" => $chemin));
        } else {
            // Handle the case when the annonce is not found
        }
    } else {
        $template = $twig->load("add-error.html.twig");
        echo $template->render(array(
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "errors" => $errors
        ));
    }
}

/**
 * @param array $inputs
 * @return array
 */
private function validateFormInputs(array $inputs): array
{
    $errors = [];
    $this->validateInput('nameAdvertiser', $inputs['nom'], $errors, 'Veuillez entrer votre nom');
    $errors = $this->validateEmailInput('emailAdvertiser', $inputs['email'], $errors, 'Veuillez entrer une adresse mail correcte');
    $errors = $this->validatePhoneInput('phoneAdvertiser', $inputs['phone'], $errors, 'Veuillez entrer votre numéro de téléphone');
    $this->validateInput('villeAdvertiser', $inputs['ville'], $errors, 'Veuillez entrer votre ville');
    $this->validateInput('departmentAdvertiser', $inputs['departement'], $errors, 'Veuillez choisir un département');
    $this->validateInput('categorieAdvertiser', $inputs['categorie'], $errors, 'Veuillez choisir une catégorie');
    $this->validateInput('titleAdvertiser', $inputs['title'], $errors, 'Veuillez entrer un titre');
    $this->validateInput('descriptionAdvertiser', $inputs['description'], $errors, 'Veuillez entrer une description');
    $this->validatePriceInput('priceAdvertiser', $inputs['price'], $errors, 'Veuillez entrer un prix');

    return $errors;
}

/**
 * @param string $key
 * @param string $value
 * @param array $errors
 * @param string $message
 */
private function validateInput(string $key, string $value, array &$errors, string $message): void
{
    if (empty($value)) {
        $errors[$key] = $message;
    }
}

private function validateEmailInput(string $key, string $value, array &$errors, string $message): array
{
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $errors[$key] = $message;
    }
    return $errors;
}

private function validatePhoneInput(string $key, string $value, array &$errors, string $message): array
{
    if (!empty($value) && !ctype_digit($value)) {
        $errors[$key] = $message;
    }
    return $errors;
}

private function validatePriceInput(string $key, string $value, array &$errors, string $message): array
{
    if (!empty($value) && !is_numeric($value)) {
        $errors[$key] = $message;
    }
    return $errors;
}

/**
 * @param Annonceur $annonceur
 * @param array $inputs
 */
private function updateAnnonceurModel(Annonceur $annonceur, array $inputs): void
{
    $annonceur->email = htmlentities($inputs['email']);
    $annonceur->nom_annonceur = htmlentities($inputs['nom']);
    $annonceur->telephone = htmlentities($inputs['phone']);
    $annonceur->save();
}

/**
 * @param Annonce $annonce
 * @param array $inputs
 */
private function updateAnnonceModel(Annonce $annonce, array $inputs): void
{
    $annonce->ville = htmlentities($inputs['ville']);
    $annonce->id_departement = $inputs['departement'];
    $annonce->prix = htmlentities($inputs['price']);
    $annonce->mdp = password_hash($inputs['psw'], PASSWORD_DEFAULT);
    $annonce->titre = htmlentities($inputs['title']);
    $annonce->description = htmlentities($inputs['description']);
    $annonce->id_categorie = $inputs['categorie'];
    $annonce->date = date('Y-m-d');
    $annonce->save();
}
}
