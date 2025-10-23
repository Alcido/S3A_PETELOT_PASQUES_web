<?php

namespace src\classes\action;

use src\classes\repository\QuoicouRepository;

class SelectPlaylistAction extends Action {

    public function lancerGet(): string
    {
        if (!isset($_GET['id'])) {
            return "<p>Aucune playlist sélectionnée.</p>";
        }

        $id = intval($_GET['id']);
        $playlist = QuoicouRepository::getInstance()->findPlaylistById($id);

        if (!$playlist) {
            return "<p>Erreur : playlist introuvable.</p>";
        }

        $_SESSION['playlist'] = serialize($playlist);

        header("Location: ?action=pl-current");
        exit();
    }

    public function lancerPost(): string
    {
        return $this->lancerGet();
    }

}