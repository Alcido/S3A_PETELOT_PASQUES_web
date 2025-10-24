<?php

namespace src\classes\action;

use src\classes\audio\lists\Playlist;
use src\classes\auth\Authz;
use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;

/**
 * Action d'affichage de la playlist en session
 */
class ShowCurrPlaylistAction extends Action {

    /** Méthode de lancement du GET
     * @return string affichage de la playlist en session
     */
    public function lancerGet(): string
    {
        // Si la playlist existe en session
        if (isset($_SESSION['playlist'])) {
            $playlist = unserialize($_SESSION['playlist']);
            $user = unserialize($_SESSION['user']);

            // On vérifie que c'est bien la playlist de l'utilisateur
            if (!Authz::checkPlaylistOfUser($user->id, $playlist->id)) return "<p>Vous n'êtes pas le propriétaire de la playlist</p>";

            // On affiche la playlist
            $renderer = new PlaylistRenderer($playlist);
            $html = $renderer->render(2);
            $html .= "<br><a href=\"?action=add-track\">Ajouter une track dans la playlist</a>";
        }
        else {
            $html = "<p>Pas de playlist en session</p>";
        }
        return $html;
    }

    /** Méthode de lancement du POST
     * @return string pas de POST
     */
    public function lancerPost(): string {
        return "<p>pas censé arriver ici</p>";
    }

}