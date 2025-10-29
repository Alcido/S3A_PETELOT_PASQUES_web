<?php

namespace src\classes\action;

use src\classes\auth\AuthnProvider;
use src\classes\exception\AuthnException;

/**
 * Action de connexion
 */
class LoginAction extends Action {

    /** Méthode de lancement du GET
     * @return string formulaire de connexion
     */
    public function lancerGet(): string
    {
        // Formulaire de connexion
        $html = <<<HTML
                <form method="POST" action="?action=login">
                <fieldset>
                    <legend>Connexion</legend><br>
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email-user" autofocus required ><br>
                    <label for="mdp">Mot de passe</label>
                    <input type="password" id="mdp" name="mdp-user" required ><br>
                    <button type="submit">Connexion</button>
                </fieldset>
                </form>

                <p>Pas de compte ? Créez en un dès maintenant</p>
                            <form method="GET">
                <input type="hidden" name="action" value="register">
                <button type="submit">Créer un compte</button>
                </form>
                HTML;
        return $html;
    }

    /** Méthode de lancement du POST
     * @return string résultat de la connexion
     * @throws AuthnException erreur de connexion
     */
    public function lancerPost(): string {
        // On récupère le mail et le mot de passe dans le POST
        $mail = $_POST['email-user'];
        $mdp = $_POST['mdp-user'];
        // On vérifie la validité des données
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) throw new AuthnException("Adresse mail n'est pas valide");
        // Connexion de l'utilisateur
        try {
            AuthnProvider::signin($mail, $mdp);
            // On renvoit la page d'accueil
            header('Location: ?action=default');
        } catch (AuthnException $e) {
            // Erreur de connexion
            $html = "<script>alert('Erreur : identifiants incorrects ! Merci de créer un compte ou de vérifier les informations de connexion');</script>" . $this->lancerGet();
        }
        return $html;
    }
}