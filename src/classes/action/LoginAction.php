<?php

namespace src\classes\action;

use src\classes\auth\AuthnProvider;
use src\classes\exception\AuthnException;

class LoginAction extends Action {

    public function lancerGet(): string
    {
        $html = <<<HTML
                <form method="POST" action="?action=login">
                <fieldset>
                    <legends>Connexion</legends>
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email-user" autofocus required >
                    <label for="mdp">Mot de passe</label>
                    <input type="password" id="mdp" name="mdp-user" required >
                    <button type="submit">Connexion</button>
                </fieldset>
                </form>
                HTML;
        return $html;
    }

    public function lancerPost(): string {
        $mail = $_POST['email-user'];
        $mdp = $_POST['mdp-user'];

        try {
            AuthnProvider::signin($mail, $mdp);
            $html = "Connexion réussie, bienvenue !</p>";
        } catch (AuthnException $e) {
            $html = "<script>alert('Erreur : identifiants incorrects ! Merci de créer un compte ou de vérifier les informations de connexion');</script>" . $this->lancerGet();
        }
        return $html;
    }


}