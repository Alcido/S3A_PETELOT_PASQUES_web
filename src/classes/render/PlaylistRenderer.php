<?php
declare(strict_types=1);

namespace src\classes\render;

use src\classes\audio\lists\Playlist;
use src\classes\audio\tracks\PodcastTrack;

class PlaylistRenderer extends AudioListRenderer {

    public function __construct($playlist) {
        parent::__construct($playlist);
    }

    public function renderLong() : string {
        $dossier = "files/";
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
            if ($piste instanceof PodcastTrack) {
                $renderer = new PodcastTrackRenderer($piste);
            } else {
                $renderer = new AlbumTrackRenderer($piste);
            }
            $track = $renderer->render(2);
            $pistes .= "<div class='pistes'>
            $track
            </div>";
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
