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
                    <legends>Connexion</legends><br>
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email-user" autofocus required ><br>
                    <label for="mdp">Mot de passe</label>
                    <input type="password" id="mdp" name="mdp-user" required ><br>
                    <button type="submit">Connexion</button>
                </fieldset>
                </form>
                HTML;
        return $html;
    }

    public function lancerPost(): string {
        $mail = $_POST['email-user'];
        $mdp = $_POST['mdp-user'];

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) throw new AuthnException("Adresse mail n'est pas valide");

        try {
            AuthnProvider::signin($mail, $mdp);
            header('Location: ?action=default');
        } catch (AuthnException $e) {
            $html = "<script>alert('Erreur : identifiants incorrects ! Merci de créer un compte ou de vérifier les informations de connexion');</script>" . $this->lancerGet();
        }
        return $html;
    }


}