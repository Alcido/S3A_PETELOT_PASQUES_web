<?php
declare(strict_types=1);

namespace src\classes\auth;

use src\classes\exception\AuthnException;
use src\classes\repository\QuoicouRepository;
use src\classes\user\User;

class AuthnProvider
{

    public static function signin(string $mail, string $mdp) : void {
        $user = QuoicouRepository::getInstance()->getUser($mail);

        if (!$user) {
            throw new AuthnException("Utilisateur n'existe pas");
        }

        if (!password_verify($mdp, $user['passwd'])) {
            throw new AuthnException("Mot de passe incorrect");
        }

        $value = new User($user['email'], $user['role'], $user['id']);
        $_SESSION['user'] = serialize($value);
    }

    public static function register(string $mail, string $mdp) : void {
        $user = QuoicouRepository::getInstance()->addUser($mail, $mdp);
        if ($user === null) {
            throw new AuthnException("Identifiant existe déja");
        }
    }

}