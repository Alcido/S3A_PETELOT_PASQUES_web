<?php

namespace src\classes\auth;

use src\classes\repository\QuoicouRepository;

class Authz {

    public static function checkPlaylistOfUser($userId, $playlistId) : bool{
        return QuoicouRepository::getInstance()->isPlaylistOfUser($userId, $playlistId);
    }

}