<?php
declare(strict_types=1);

namespace src\classes\user;
use src\classes\exception\InvalidPropertyNameException;

class User
{

    private string $email;
    private int $role;
    private int $id;

    const ADMIN_USER = 100;
    const USER = 1;

    public function __construct(string $mail, int $role, int $id) {
        $this->email = $mail;
        $this->role = $role;
        $this->id = $id;
    }

    public function __get(string $atr) {
        if (property_exists($this, $atr)) return $this->$atr;
        throw new InvalidPropertyNameException("$atr: Mauvais nom d'atribut");
    }

}