<?php

namespace src\classes\action;

/**
 * Action pour la deconnexion de l'utilisateur
 */
class DisconnectAction extends Action {

    /** Méthode de lancement du GET
     * @return string renvoie le résultat du POST
     */
    public function lancerGet(): string
    {
        return $this->lancerPost();
    }

    /** Méthode de lancement du POST
     * @return string la page de connexion
     */
    public function lancerPost(): string
    {
        // On détruit l'utilisateur en session
        unset($_SESSION['user']);
        // On renvoit la page de connexion
        header("Location: ?action=login");
        return "";
    }
}