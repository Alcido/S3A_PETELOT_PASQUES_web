<?php
declare(strict_types=1);

namespace src\classes\render;

use src\classes\audio\tracks\AlbumTrack;


class AlbumTrackRenderer extends AudioTrackRenderer
{
    public function __construct(AlbumTrack $albumTrack)
    {
        parent::__construct($albumTrack);
    }

    public function renderCompact() : string
    {
        $s = "
        <p>Piste : {$this->audioTrack->titre}</p></br>
        <p>Album : {$this->audioTrack->nomAlbum}</p></br>
        <p>Numéro de piste : {$this->audioTrack->numPiste}</p></br>
        <audio controls><source src=\"files/{$this->audioTrack->nomFichier}\" type=\"audio/mpeg\"></audio>";
        return $s;
    }

    public function renderLong() : string
    {
        $s = "
        <p>Piste : {$this->audioTrack->titre}</p></br>
        <p>Album : {$this->audioTrack->nomAlbum}</p></br>
        <p>Numéro de piste : {$this->audioTrack->numPiste}</p></br>
        <p>Artiste : {$this->audioTrack->auteur}</p></br>
        <p>Genre : {$this->audioTrack->genre}</p></br>
        <p>Année : {$this->audioTrack->annee}</p></br>
        <p>Durée : {$this->audioTrack->duree}</p></br>
        <audio controls><source src=\"files/{$this->audioTrack->nomFichier}\" type=\"audio/mpeg\"></audio>";
        return $s;
    }
}