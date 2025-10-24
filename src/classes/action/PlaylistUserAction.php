<?php

namespace src\classes\action;

use src\classes\exception\AuthnException;
use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;

/**
 * Action permettant de récupérer la playlist de l'utilisateur dans la BDD
 */
class PlaylistUserAction extends Action {

    /** Méthode du lancement du GET
     * @return string affichage de la playlist
     */
    public function lancerGet() : string {
        // On récupère l'utilisateur et ses playlists en BDD
        $user = unserialize($_SESSION['user']);
        $playlists = QuoicouRepository::getInstance()->getPlaylistByUser($user->id);

        // Affichage des playlists
        $html = "<p>Vos playlists : </p><br><ul>";
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

    /** Méthode du lancement du POST
     * @return string lancement du GET
     */
    public function lancerPost() : string{
        return $this->lancerGet();
    }

}