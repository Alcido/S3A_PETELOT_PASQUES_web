<?php
declare(strict_types=1);

namespace src\classes\auth;

use src\classes\exception\AuthnException;
use src\classes\user\User;

class AuthnProvider
{

    public static function signin(string $mail, string $mdp) : void {
        $user = DeefyRepository::getInstance()->getUser($mail);

        if (!$user || !password_verify($mdp, $user['passwd'])) {
            throw new AuthnException("L'utilisateur n'existe pas ou mot de passe incorrect");
        }

        $_SESSION['user'] = new User($user['email'], $user['role'],$user['id']);
    }

    public static function register(string $mail, string $mdp) : void {

        if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            throw new AuthnException("L'utilisateur n'existe pas");
        }
        $user = DeefyRepository::getInstance()->addUser($mail, $mdp);
        if ($user !== null) {
            $_SESSION['user'] = new User($user['email'], $user['role']);
        } else {
            throw new AuthnException("Identifiant existe déja");
        }
    }

}