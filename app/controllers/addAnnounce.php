<?php

namespace App\Controllers\AddAnnounce;

use App\Models\Annonce;
use App\Models\Annonceur;
use Twig\Environment;

class AddItemController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param array $menu
     * @param array $chemin
     * @param array $cat
     * @param array $dpt
     * @return void
     */
    public function addItemView(array $menu, array $chemin, array $cat, array $dpt): void
    {
        $template = $this->twig->load("add.html.twig");
        echo $template->render([
            "breadcrumb"   => $menu,
            "chemin"       => $chemin,
            "categories"   => $cat,
            "departements" => $dpt
        ]);
    }

    /**
     * @param array $allPostVars
     * @param array $menu
     * @param array $chemin
     * @return void
     */
    public function addNewItem(array $allPostVars, array $menu, array $chemin): void
    {
        $errors = $this->validateFormInputs($allPostVars);

        if (empty($errors)) {
            $annonce = $this->createAnnonceModel($allPostVars);
            $annonceur = $this->createAnnonceurModel($allPostVars);

            $annonceur->save();
            $annonceur->annonce()->save($annonce);

            $this->renderConfirmationPage($menu, $chemin);
        } else {
            $this->renderErrorPage($menu, $chemin, $errors);
        }
    }

    /**
     * @param array $inputs
     * @return array
     */
    private function validateFormInputs(array $inputs): array
    {
        $errors = [];

        foreach ($inputs as $key => $input) {
            switch ($key) {
                case 'email':
                    $errors[$key . 'Advertiser'] = $this->validateEmail($input);
                    break;
                case 'phone':
                    $errors[$key . 'Advertiser'] = $this->validatePhoneNumber($input);
                    break;
                case 'departement':
                case 'categorie':
                    $errors[$key . 'Advertiser'] = $this->validateNumericInput($input);
                    break;
                default:
                    $errors[$key . 'Advertiser'] = $this->validateNonEmptyInput($input);
            }
        }

        return array_values(array_filter($errors));
    }

    /**
     * @param string $email
     * @return string
     */
    private function validateEmail(string $email): string
    {
        return (!filter_var($email, FILTER_VALIDATE_EMAIL)) ? 'Veuillez entrer une adresse mail correcte' : '';
    }

    /**
     * @param string $phone
     * @return string
     */
    private function validatePhoneNumber(string $phone): string
    {
        return (!is_numeric($phone) || empty($phone)) ? 'Veuillez entrer votre numéro de téléphone' : '';
    }

    /**
     * @param string $input
     * @return string
     */
    private function validateNonEmptyInput(string $input): string
    {
        return (empty($input)) ? 'Veuillez entrer une valeur' : '';
    }

    /**
     * @param string $input
     * @return string
     */
    private function validateNumericInput(string $input): string
    {
        return (!is_numeric($input)) ? 'Veuillez entrer un nombre' : '';
    }
    /**
     * @param array $inputs
     * @return Annonceur
     */
    private function createAnnonceurModel(array $inputs): Annonceur
    {
        $annonceur = new Annonceur();
        $annonceur->email = htmlentities($inputs['email']);
        $annonceur->nom_annonceur = htmlentities($inputs['nom']);
        $annonceur->telephone = htmlentities($inputs['phone']);

        return $annonceur;
    }

    /**
     * @param array $inputs
     * @return Annonce
     */
    private function createAnnonceModel(array $inputs): Annonce
    {
        $annonce = new Annonce();
        $annonce->ville = htmlentities($inputs['ville']);
        $annonce->id_departement = $inputs['departement'];
        $annonce->prix = htmlentities($inputs['price']);
        $annonce->mdp = password_hash($inputs['psw'], PASSWORD_DEFAULT);
        $annonce->titre = htmlentities($inputs['title']);
        $annonce->description = htmlentities($inputs['description']);
        $annonce->id_categorie = $inputs['categorie'];
        $annonce->date = date('Y-m-d');

        return $annonce;
    }

    /**
     * @param array $menu
     * @param array $chemin
     * @return void
     */
    private function renderConfirmationPage(array $menu, array $chemin): void
    {
        $template = $this->twig->load("add-confirm.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin
        ]);
    }

    /**
     * @param array $menu
     * @param array $chemin
     * @param array $errors
     * @return void
     */
    private function renderErrorPage(array $menu, array $chemin, array $errors): void
    {
        $template = $this->twig->load("add-error.html.twig");
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "errors" => $errors
        ]);
    }
}