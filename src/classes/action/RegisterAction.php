<?php
declare(strict_types=1);

namespace src\classes\action;

use src\classes\auth\AuthnProvider;
use src\classes\exception\AuthnException;

class RegisterAction extends Action
{
    public function lancerGet() : string {
        $html = <<<HTML
                <form method="POST" action="?action=register">
                    <fieldset>
                        <legend>Inscription</legend>
                        <label for="mail">Email</label>
                        <input type="email" id="mail" placeholder="exemple@test.fr" name="mail-user" autofocus required>
                        <label for="passwd">Mot de passe</label>
                        <input type="password" id="passwd" name="passwd-user" required minlength="10">
                        <label for="passwd2">Confirmer le mot de passe</label>
                        <input type="password" id="passwd2" name="passwd-user2" required minlength="10">
                
                        <button type="submit">Valider</button>
                    </fieldset>
                </form>
                HTML;
        return $html;
    }

    public function lancerPost() : string {
        $html = "<p>Mauvais format de mail</p>";
        if (filter_var($_POST['mail-user'], FILTER_SANITIZE_EMAIL) === $_POST['mail-user']) {

            $mdp1 = $_POST['passwd-user'];
            $mdp2 = $_POST['passwd-user2'];

            if ($mdp1 !== $mdp2) {
                return $this->lancerGet() . "<script>alert('Erreur : Les mots de passes ne correspondent pas !');</script>";
            }

            try {
                AuthnProvider::register($_POST['mail-user'], $mdp1);
            } catch (AuthnException $e) {
                return $this->lancerGet() . "<script>alert('Erreur : identifiant déjà présent !');</script>";
            }

            $html = <<<HTML
                    <div class="user-info">
                       <p><b>Bonjour {$_SESSION["user"]->email}</p>
                    </div>
                    HTML;

            //modifier pour aller sur la page de connexion
        }
        return $html;
    }

}