<?php
declare(strict_types=1);

namespace src\classes\audio\tracks;


class PodcastTrack extends AudioTrack
{
    public function __construct(string $titre, string $nomFichier, ?string $auteur = null, ?string $annee = null, ?string $genre = null, ?int $duree = null, ?int $id = null)
    {
        parent::__construct($titre, $nomFichier, $auteur, $annee, $genre, $duree, $id);
    }

    public function __toString() : string {
        return $this->titre . " - " . $this->nomFichier . " - " . $this->auteur . " - " . $this->annee . " - " . $this->genre;
    }
}
