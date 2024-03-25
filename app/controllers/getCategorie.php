<?php

namespace App\Controllers\GetCategorie;

use App\Models\Annonce;
use App\Models\Annonceur;
use App\Models\Categorie;
use App\Models\Photo;

class GetCategorie
{
    protected $annonce = [];

    public function getCategories(): array
    {
        return Categorie::orderBy('nom_categorie')->get()->toArray();
    }

    public function getCategorieContent(string $chemin, int $categorieId): void
    {
        $annonceCollection = Annonce::with("Annonceur")
            ->orderBy('id_annonce', 'desc')
            ->where('id_categorie', '=', $categorieId)
            ->get()
        ;

        $this->annonce = $annonceCollection->map(function (Annonce $annonce) use ($chemin) {
            $annonce->nb_photo = Photo::where("id_annonce", "=", $annonce->id_annonce)->count();

            if ($annonce->nb_photo > 0) {
                $annonce->url_photo = Photo::select("url_photo")
                    ->where("id_annonce", "=", $annonce->id_annonce)
                    ->first()->url_photo;
            } else {
                $annonce->url_photo = $chemin . '/img/noimg.png';
            }

            $annonce->nom_annonceur = Annonceur::select("nom_annonceur")
                ->where("id_annonceur", "=", $annonce->id_annonceur)
                ->first()->nom_annonceur;

            return $annonce;
        })->toArray();
    }

    public function displayCategorie($twig, array $menu, string $chemin, array $cat, int $categorieId, string $categorieName): void
    {
        $menu = array(
            array('href' => $chemin, 'text' => 'Acceuil'),
            array('href' => $chemin . "/cat/{$categorieId}", 'text' => $categorieName)
        );

        $this->getCategorieContent($chemin, $categorieId);

        $template = $twig->load("index.html.twig");

        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $cat,
            "annonces"   => $this->annonce
        ]);
    }
}