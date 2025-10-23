<?php
declare(strict_types=1);

namespace src\classes\audio\lists;


use src\classes\audio\tracks\AudioTrack;
use src\classes\exception\InvalidPropertyNameException;

class Playlist extends AudioList
{

    private ?int $id;
    public function __construct($nom, $pistes, ?int $id=null) {
        parent::__construct($nom, $pistes);
        $this->id = $id;
    }

    public function addPiste (?AudioTrack $track) : void{
        if ($track !== null) {
            $this->pistes[] = $track;
            $this->dureeTot += $track->duree;
            $this->nbPiste++;
        }
    }

    public function delPiste (int $i) : void{
        $info = $this->pistes[$i];
        $this->pistes[$i] = null;
        $this->nbPiste--;
        $this->dureeTot -= $info->duree;
    }

    public function merge(array $pistes) : void{
        $temp = [];
        foreach ($pistes as $piste) {
            $temp[] = $piste;
        }
        $this->pistes = array_unique(array_merge($this->pistes, $temp));
    }

    public function setID(int $id) : void{
        $this->id = $id;
    }

    public function __get(string $s) : mixed {
        if (property_exists($this, $s)) return $this->$s;
        throw new InvalidPropertyNameException("$s: Mauvais nom d'atribut");
    }


}