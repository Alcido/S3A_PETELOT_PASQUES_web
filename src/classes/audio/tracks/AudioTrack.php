<?php

declare(strict_types=1);

namespace src\classes\audio\tracks;

use src\classes\exception\InvalidPropertyNameException;
use src\classes\exception\InvalidPropertyValueException;



class AudioTrack
{
    protected string $titre;
    protected string $nomFichier;
    protected ?string $auteur;
    protected ?string $annee;
    protected ?string $genre;
    protected ?int $duree;

    protected ?int $id;

    /**
     * @throws InvalidPropertyValueException
     */
    public function __construct(string $titre, string $nomFichier, ?string $auteur=null, ?string $annee=null, ?string $genre=null, ?int $duree=null, ?int $id=null) {

        $this->titre = $titre;
        $this->nomFichier = $nomFichier;
        $this->auteur = $auteur;
        $this->annee = $annee;
        $this->genre = $genre;
        $this->setDuree($duree);
        $this->id = $id;
    }

    public function __get(string $at):mixed {
        if (property_exists($this, $at)) return $this->$at;
        throw new InvalidPropertyNameException("$at: Mauvais nom d'atribut");
    }

    public function setDuree(?int $duree) : void{
        if ($duree < 0) {
            throw new InvalidPropertyValueException("$duree: la durée ne peut pas etre négative");
        }
        $this->duree = $duree;
    }

    public function setID(int $id) : void {
        $this->id = $id;
    }

}