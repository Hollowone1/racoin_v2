<?php

namespace App\Controllers\GetApiKey;

use App\Models\ApiKey;
use Twig\Environment;

class KeyGenerator
{
    public function show(Environment $twig, array $menu, string $chemin, array $cat): void
    {
        $template = $twig->load("key-generator.html.twig");

        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $cat
        ]);
    }

    public function generateKey(Environment $twig, array $menu, string $chemin, array $cat, string $nom): void
    {
        $nospace_nom = str_replace(' ', '', $nom);

        if ($nospace_nom === '') {
            $template = $twig->load("key-generator-error.html.twig");

            echo $template->render([
                "breadcrumb" => $menu,
                "chemin"     => $chemin,
                "categories" => $cat
            ]);
        } else {
            $template = $twig->load("key-generator-result.html.twig");

            // Génere clé unique de 13 caractères
            $key = uniqid();

            // Ajouter clé dans la base
            $apiKey = new ApiKey();
            $apiKey->id_apikey = $key;
            $apiKey->name_key = htmlentities($nom);
            $apiKey->save();

            echo $template->render([
                "breadcrumb" => $menu,
                "chemin"     => $chemin,
                "categories" => $cat,
                "key"        => $key
            ]);
        }
    }
}