<?php
declare(strict_types = 1);

namespace src\classes\lists;

use TDAppli\deefy\exception\InvalidPropertyNameException;

class Album extends AudioList
{
    private string $artiste;
    private int $date;

    public function __construct(string $nom, array $pistes, ?string $artiste=null, ?int $date=null) {
        parent::__construct($nom, $pistes);
        $this->artiste = $artiste;
        $this->date = $date;
    }

    public function __get(string $s) : mixed {
        if (property_exists($this, $s)) return $this->$s;
        throw new InvalidPropertyNameException("$s: Mauvais nom d'atribut");
    }

    public function setArtiste(string $artiste): void {
        $this->artiste = $artiste;
    }
    public function setDate(int $date): void {
        if ($date > 0) {
            $this->date = $date;
        }
    }

}