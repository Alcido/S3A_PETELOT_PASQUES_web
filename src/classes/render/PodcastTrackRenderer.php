<?php
declare(strict_types=1);

namespace src\classes\render;

use src\classes\audio\tracks\PodcastTrack;

class PodcastTrackRenderer extends AudioTrackRenderer
{
    public function __construct(PodcastTrack $podcastTrack)
    {
        parent::__construct($podcastTrack);
    }

    public function renderCompact() : string
    {
        $s = "
        <p>Piste : {$this->audioTrack->titre}</p></br>
        <p>Auteur : {$this->audioTrack->auteur}</p></br>
        <audio controls><source src=\"files/{$this->audioTrack->nomFichier}\" type=\"audio/mpeg\"></audio>";
        return $s;
    }

    public function renderLong() : string
    {
        $s = "
        <p>Piste : {$this->audioTrack->titre}</p></br>
        <p>Auteur : {$this->audioTrack->auteur}</p></br>
        <p>Genre : {$this->audioTrack->genre}</p></br>
        <p>Année : {$this->audioTrack->annee}</p></br>
        <p>Durée : {$this->audioTrack->duree}</p></br>
        <audio controls><source src=\"files/{$this->audioTrack->nomFichier}\" type=\"audio/mpeg\"></audio>";
        return $s;
    }
}