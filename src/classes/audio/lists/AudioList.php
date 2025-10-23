<?php
declare(strict_types=1);

namespace src\classes\audio\lists;


use src\classes\exception\InvalidPropertyNameException;

class AudioList
{
    protected string $name;
    protected int $nbPiste;
    protected int $dureeTot;
    protected array $pistes;

    public function __construct(string $nom, array $pistes=[], ?int $nbPistes = null, ?int $dureeTot = null) {
        $this->name = $nom;
        $this->pistes = $pistes;
        $this->nbPiste = count($pistes);
        $this->dureeTot =0;
        if ($this->nbPiste > 0) {
            for ($i = 0; $i < $this->nbPiste; ++$i) {
                $this->dureeTot += $this->pistes[$i]->duree;
            }
        }
    }

    public function __get(string $s) : mixed {
        if (property_exists($this, $s)) return $this->$s;
        throw new InvalidPropertyNameException("$s: Mauvais nom d'atribut");
    }



}