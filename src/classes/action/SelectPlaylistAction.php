<?php

namespace src\classes\action;

use src\classes\repository\QuoicouRepository;

/**
 * Action de selection de playlist de la BDD à mettre en session
 */
class SelectPlaylistAction extends Action {

    /** Méthode de lancement du GET
     * @return string affichage de la playlist
     */
    public function lancerGet(): string
    {
        // Si l'ID de la playlist n'est pas dans le GET
        if (!isset($_GET['id'])) {
            return "<p>Aucune playlist sélectionnée.</p>";
        }

        // On récupère la playlist en BDD
        $id = intval($_GET['id']);
        $playlist = QuoicouRepository::getInstance()->findPlaylistById($id);


        if (!$playlist) {
            return "<p>Erreur : playlist introuvable.</p>";
        }

        // Si la playlist existe on la met en session
        $_SESSION['playlist'] = serialize($playlist);

        // On affiche la playlist en session
        header("Location: ?action=pl-current");
        exit();
    }

    /** Méthode de lancement du POST
     * @return string pas de POST
     */
    public function lancerPost(): string
    {
        return $this->lancerGet();
    }

}