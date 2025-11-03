<?php

namespace src\classes\action;

use src\classes\audio\lists\Playlist;
use src\classes\auth\Authz;
use src\classes\exception\AccessControlException;
use src\classes\exception\AuthnException;
use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;
use src\classes\user\User;

/**
 * Action permettant de récupérer la playlist de l'utilisateur dans la BDD
 */
class PlaylistUserAction extends Action {

    /** Méthode du lancement du GET
     * @return string affichage de la playlist
     */
    public function lancerGet() : string {
        // On récupère l'utilisateur et ses playlists en BDD

        $admin = Authz::checkRole(User::ADMIN_USER);

        if ($admin) {
            $playlists = QuoicouRepository::getInstance()->getAllPlaylist();
        } else {
            $user = unserialize($_SESSION['user']);
            $playlists = QuoicouRepository::getInstance()->getPlaylistByUser($user->id);
        }

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