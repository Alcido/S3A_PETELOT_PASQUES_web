<?php

namespace src\classes\action;

use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;

class PlaylistUserAction extends Action {

    public function lancerGet() : string {
        $html = "<p>Vos playlists : </p><br><ul>";
        $playlists = QuoicouRepository::getInstance()->getPlaylistByUser($_SESSION['user']->id);
        foreach ($playlists as $playlist) {
            $renderer = new PlaylistRenderer($playlist);
            $affichage = $renderer->render(2);
            $html .= "
                    <li>
                    <a href='?action=select-playlist&id={$playlist->id}' class='playlist-link'>
                        $affichage
                    </a>
                    </li><br>";;
        }
        $html .= "</ul>";
        return $html;
    }

    public function lancerPost() : string{
        return "<p>Pas censé arriver ici</p>";
    }

}