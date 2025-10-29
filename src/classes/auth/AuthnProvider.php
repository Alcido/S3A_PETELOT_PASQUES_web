<?php
declare(strict_types=1);

namespace src\classes\auth;

use src\classes\exception\AuthnException;
use src\classes\repository\QuoicouRepository;
use src\classes\user\User;

/**
 * Classe d'authentification
 */
class AuthnProvider
{

    /** Méthode de connexion
     * @param string $mail mail de connexion
     * @param string $mdp mot de passe de connexion
     * @return void
     * @throws AuthnException erreur de connexion
     */
    public static function signin(string $mail, string $mdp) : void {
        // On récupère l'utilisateur depuis la BDD
        $user = QuoicouRepository::getInstance()->getUser($mail);
        // Si l'utilisateur n'existe pas
        if (!$user) {
            throw new AuthnException("Utilisateur n'existe pas");
        }
        // Si le mot de passe est invalide
        if (!password_verify($mdp, $user['passwd'])) {
            throw new AuthnException("Mot de passe incorrect");
        }
        // On ajoute l'utilisateur trouvé en session
        $value = new User($user['email'], intval($user['role']), intval($user['id']));
        $_SESSION['user'] = serialize($value);
    }

    /** Méthode d'inscription d'un utilisateur
     * @param string $mail mail d'inscription
     * @param string $mdp mot de passe d'inscription
     * @return void
     * @throws AuthnException erreur d'inscription
     */
    public static function register(string $mail, string $mdp) : void {
        // On ajoute l'utilisateur à la BDD
        $user = QuoicouRepository::getInstance()->addUser($mail, $mdp);
        // Si l'inscription s'est mal passée
        if ($user === null) {
            throw new AuthnException("Identifiant existe déja");
        }
    }

}