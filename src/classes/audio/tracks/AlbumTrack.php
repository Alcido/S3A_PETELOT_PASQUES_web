<?php
declare(strict_types=1);

namespace src\classes\audio\tracks;


use src\classes\exception\InvalidPropertyNameException;

class AlbumTrack extends AudioTrack {

    private string $nomAlbum;
    private int $numPiste;

    public function __construct(string $t, string $fichier, string $nAlb, int $piste, ?string $artiste=null, ?string $annee=null, ?string $genre=null, ?int $duree=null)
    {
        parent::__construct($t, $fichier, $artiste, $annee, $genre, $duree);
        $this->nomAlbum = $nAlb;
        $this->numPiste = $piste;
    }

    public function __toString() {
        return json_encode(get_object_vars($this));
    }

    public function __get($at):mixed {
        if (property_exists ($this, $at)) return $this->$at;
        throw new InvalidPropertyNameException("$at:  attribut inexistant");
    }

    public function setNomAlbum(string $nAlb) : void {
        $this->nomAlbum = $nAlb;
    }

    public function setNumPiste(int $numPiste) : void {
        if ($numPiste >= 0) $this->numPiste = $numPiste;
    }

}

