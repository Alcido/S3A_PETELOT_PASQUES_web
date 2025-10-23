<?php

namespace src\classes\action;

use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;

class PlaylistUserAction extends Action {

    public function lancerGet() : string {
        $html = "<p>Vos playlists : </p><br><ul>";
        $user = unserialize($_SESSION['user']);
        $playlists = QuoicouRepository::getInstance()->getPlaylistByUser($user->id);
        foreach ($playlists as $playlist) {
            $renderer = new PlaylistRenderer($playlist);
            $affichage = $renderer->render(2);
            $html .= "
                    <li>
                    <a href='?action=select-playlist&id={$playlist->id}' class='playlist-link'>
                        <p>$playlist->id</p>
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