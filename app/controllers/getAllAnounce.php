<?php

namespace App\Controllers\GetAllAnounce;

use App\Models\Annonce;
use App\Models\Annonceur;
use App\Models\Photo;
use Twig\Environment;

class Index
{
    protected $annonce = [];

    public function displayAllAnnonce(Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $this->getAll($chemin);

        $template = $twig->load("index.html.twig");

        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $cat,
            "annonces"   => $this->annonce
        ]);
    }

    public function getAll(string $chemin): void
    {
        $annotatedAnnonces = Annonce::with("Annonceur")
            ->orderBy('id_annonce', 'desc')
            ->take(12)
            ->get()
            ->map(function (Annonce $annonce) use ($chemin) {
                $annonce->nb_photo = Photo::where("id_annonce", "=", $annonce->id_annonce)->count();

                if ($annonce->nb_photo > 0) {
                    $annonce->url_photo = Photo::select("url_photo")
                        ->where("id_annonce", "=", $annonce->id_annonce)
                        ->first()->url_photo;
                } else {
                    $annonce->url_photo = '/img/noimg.png';
                }

                $annonce->nom_annonceur = Annonceur::select("nom_annonceur")
                    ->where("id_annonceur", "=", $annonce->id_annonceur)
                    ->first()->nom_annonceur;

                return $annonce;
            })
        ;

        $this->annonce = $annotatedAnnonces->toArray();
    }
}