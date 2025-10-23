<?php

namespace src\classes\action;

use src\classes\audio\lists\Playlist;
use src\classes\render\PlaylistRenderer;

class ShowCurrPlaylistAction extends Action {

    public function lancerGet(): string
    {
        if (isset($_SESSION['playlist'])) {
            $playlist = unserialize($_SESSION['playlist']);
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