<?php

namespace src\classes\action;

use src\classes\audio\lists\Playlist;
use src\classes\auth\Authz;
use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;

class ShowCurrPlaylistAction extends Action {

    public function lancerGet(): string
    {
        if (isset($_SESSION['playlist'])) {
            $playlist = unserialize($_SESSION['playlist']);
            $user = unserialize($_SESSION['user']);

            if (!Authz::checkPlaylistOfUser($user->id, $playlist->id)) return "<p>Vous n'êtes pas le propriétaire de la playlist</p>";

            $renderer = new PlaylistRenderer($playlist);
            $html = $renderer->render(2);
            $html .= "<br><a href=\"?action=add-track\">Ajouter une track dans la playlist</a>";
        }
        else {
            $html = "<p>Pas de playlist en session</p>";
        }
        return $html;
    }

    public function lancerPost(): string {
        return "<p>pas censé arriver ici</p>";
    }

}