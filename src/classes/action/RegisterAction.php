<?php
declare(strict_types=1);

namespace src\classes\action;

use src\classes\auth\AuthnProvider;
use src\classes\exception\AuthnException;

/**
 * Action d'inscription de l'utilisateur
 */
class RegisterAction extends Action
{
    /** Méthode de lancement du GET
     * @return string formulaire d'inscription
     */
    public function lancerGet() : string {
        // Formulaire d'inscription
        $html = <<<HTML
                <form method="POST" action="?action=register">
                    <fieldset>
                        <legend>Inscription</legend>
                        <label for="mail">Email</label>
                        <input type="email" id="mail" placeholder="exemple@test.fr" name="mail-user" autofocus required><br>
                        <label for="passwd">Mot de passe</label>
                        <input type="password" id="passwd" name="passwd-user" required minlength="10"><br>
                        <label for="passwd2">Confirmer le mot de passe</label>
                        <input type="password" id="passwd2" name="passwd-user2" required minlength="10"><br>
                
                        <button type="submit">Valider</button>
                    </fieldset>
                </form>
                HTML;
        return $html;
    }

    /** Méthode de lancement du POST
     * @return string résultat de l'inscription en HTML
     */
    public function lancerPost() : string {

        $html = "<p>Mauvais format de mail</p>";

        // Si le mail dans le POST est valide
        if (filter_var($_POST['mail-user'], FILTER_SANITIZE_EMAIL) === $_POST['mail-user']) {

            // On récupère les mots de passes dans le POST
            $mdp1 = $_POST['passwd-user'];
            $mdp2 = $_POST['passwd-user2'];

            // On vérifie si les mots de passe sont égaux
            if ($mdp1 !== $mdp2) {
                return $this->lancerGet() . "<script>alert('Erreur : Les mots de passes ne correspondent pas !');</script>";
            }

            // Inscription de l'utilisateur
            try {
                AuthnProvider::register($_POST['mail-user'], $mdp1);
            } catch (AuthnException $e) {
                return $this->lancerGet() . "<script>alert('Erreur : identifiant déjà présent !');</script>";
            }

            // Si l'utilisateur en session n'existe pas
            if (!isset($_SESSION["user"])) {
                header("Location: ?action=login");
            } else {
                $html = "<p>Utilisateur ajouté avec succès</p>";
            }
        }
        return $html;
    }

}