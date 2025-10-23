<?php
declare(strict_types=1);

namespace src\classes\render;

class PlaylistRenderer extends AudioListRenderer {

    public function __construct($playlist) {
        parent::__construct($playlist);
    }

    public function renderLong() : string {
        $dossier = "src/classes/file_tracks/";
        $affichage ="
        <div class='playlist'>
        <p>Nom : {$this->audioList->name}</p>
        <p>Nombre de pistes : {$this->audioList->nbPiste}</p>
        <p>Durée totale : {$this->audioList->dureeTot}</p>
        ";
        $pistes = "
        <p>Pistes de la playlist : </p>
        <ul>";
        foreach ($this->audioList->pistes as $piste) {
            $fichier = $dossier.$piste->nomFichier;
            $pistes .= "<div class='pistes'><ul>
            <li>Titre : {$piste->titre}</li>
            <li>Auteur : {$piste->auteur}</li>
            <li>Genre : {$piste->genre}</li>
            <li>Annee : {$piste->annee}</li>
            <li>Duree : {$piste->duree}</li>
            <audio controls><source src=\"{$fichier}\" type=\"audio/mpeg\"></audio>
            </ul></div>";
        }
        $pistes .= "</ul></div>";
        return $affichage . $pistes;
    }

    public function renderCompact() : string {
        $affichage = "
        <div class='playlist'>
        <p>Nom : {$this->audioList->name}</p>
        <p>Nombre de pistes : {$this->audioList->nbPiste}</p>
        <p>Durée totale : {$this->audioList->dureeTot}</p>
        </div>
        ";
        return $affichage;
    }


}
