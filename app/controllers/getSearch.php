<?php

namespace App\Controllers\GetSearch;

use App\Models\Annonce;
use App\Models\Categorie;

class Search
{
    public function show($twig, array $menu, string $chemin, array $cat): void
    {
        $template = $twig->load("search.html.twig");

        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "categories" => $cat
        ]);
    }

    public function research(array $searchData, $twig, array $menu, string $chemin, array $cat): void
    {
        $template = $twig->load("index.html.twig");

        $cleanedSearchData = array_map(function ($value) {
            return trim($value);
        }, $searchData);

        $query = Annonce::select();

        // Applies filters based on the provided search data
        $this->applyFilters($query, $cleanedSearchData);

        $annonces = $query->get();

        echo $template->render([
            "breadcrumb" => $menu,
            "chemin"     => $chemin,
            "annonces"   => $annonces,
            "categories" => $cat
        ]);
    }

    /**
     * Applies filters and conditions based on the search data
     */
    private function applyFilters(
        \Illuminate\Database\Eloquent\Builder $query,
        array $searchData
    ): void {
        foreach ($searchData as $key => $value) {
            switch ($key) {
                case 'motclef':
                    if (!empty($value)) {
                        $query->where('description', 'like', "%{$value}%");
                    }
                    break;

                case 'codepostal':
                    if (!empty($value)) {
                        $query->where('ville', '=', $value);
                    }
                    break;

                case 'categorie':
                    if ($value !== 'Toutes catégories' && $value !== '-----') {
                        $categorie = Categorie::select('id_categorie')->where('id_categorie', '=', $value)->first();
                        $query->where('id_categorie', '=', $categorie->id_categorie);
                    }
                    break;

                case 'prix-min':
                    if ($value !== 'Min') {
                        $query->where('prix', '>=', $value);
                    }
                    break;

                case 'prix-max':
                    if ($value !== 'Max' && $value !== 'nolimit') {
                        $query->where('prix', '<=', $value);
                    }
                    break;
            }
        }
    }
}