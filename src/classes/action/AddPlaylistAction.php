<?php
declare(strict_types=1);

namespace src\classes\action;


use src\classes\audio\lists\Playlist;
use src\classes\exception\AuthnException;
use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;

class AddPlaylistAction extends Action {

    public function lancerGet() : string {

            $html = <<<HTML
                <form method="post" action="?action=add-playlist">
                    <label for="nomPlaylist"> Nom de la Playlist </label>
                    <input type="text" id = "nomPlaylist" name="nomPlaylist" placeholder="Nom de la Playlist" required autofocus><br>
                    <button type="submit" name="validerPlaylist">Valider</button>
                </form>
            HTML;
        return $html;
    }

    public function lancerPost() : string {
        if ($_POST["nomPlaylist"] == filter_var($_POST["nomPlaylist"], FILTER_SANITIZE_SPECIAL_CHARS)) {
            $name = $_POST['nomPlaylist'];
        } else {
            return "<script>alert(\"Nom de playliste invalide\")</script>";
        }
        $pl = QuoicouRepository::getInstance()->saveEmptyPlaylist(new Playlist($name, []));
        $_SESSION['playlist'] = serialize($pl);
        $html = "<br><b>Playliste créée avec succès</b><br>";
        $user = unserialize($_SESSION['user']);
        $html .= QuoicouRepository::getInstance()->addUserToPlaylist($user->id, $pl->id);
        $renderer = new PlaylistRenderer($pl);
        $affichage = $renderer->render(1);
        return $html . $affichage . "<br>" . "<a href=\"?action=add-track\">Ajouter une piste</a><br>";
    }

}
