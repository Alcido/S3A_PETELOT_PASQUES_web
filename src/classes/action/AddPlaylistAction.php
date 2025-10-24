<?php
declare(strict_types=1);

namespace src\classes\action;


use src\classes\audio\lists\Playlist;
use src\classes\exception\AuthnException;
use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;

/**
 * Action permettant de créer une Playlist dans la BDD et en session
 */
class AddPlaylistAction extends Action {

    /**
     * Méthode du lancement du GET
     * @return string affichage du formulaire de création de la Playlist
     */
    public function lancerGet() : string {
            // Formulaire
            $html = <<<HTML
                <form method="post" action="?action=add-playlist">
                    <label for="nomPlaylist"> Nom de la Playlist </label>
                    <input type="text" id = "nomPlaylist" name="nomPlaylist" placeholder="Nom de la Playlist" required autofocus><br>
                    <button type="submit" name="validerPlaylist">Valider</button>
                </form>
            HTML;
        return $html;
    }

    /**
     * Méthode de lancement du POST
     * @return string résultat de la création de la Playlist
     */
    public function lancerPost() : string {
        // On vérifie le nom de la Playlist
        if ($_POST["nomPlaylist"] == filter_var($_POST["nomPlaylist"], FILTER_SANITIZE_SPECIAL_CHARS)) {
            $name = $_POST['nomPlaylist'];
        } else {
            return "<script>alert(\"Nom de playliste invalide\")</script>";
        }
        // On enregistre la Playlist dans la BDD
        $pl = QuoicouRepository::getInstance()->saveEmptyPlaylist(new Playlist($name, []));
        // On met la playlist en session
        $_SESSION['playlist'] = serialize($pl);
        // On récupère l'utilisateur en session
        $user = unserialize($_SESSION['user']);
        // Affichage
        $renderer = new PlaylistRenderer($pl);
        $affichage = $renderer->render(1);
        // On ajoute la playlist à l'utilisateur dans la BDD et on récupère le résultat
        $html = "<br><b>Playliste créée avec succès</b><br>" . QuoicouRepository::getInstance()->addUserToPlaylist($user->id, $pl->id);
        return $html . $affichage . "<br>" . "<a href=\"?action=add-track\">Ajouter une piste</a><br>";
    }

}
