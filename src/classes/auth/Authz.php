<?php

namespace src\classes\auth;

use src\classes\exception\AccessControlException;
use src\classes\repository\QuoicouRepository;

/**
 * Classe permettant de gérer les autorisations
 */
class Authz {

    /** Vérification de la propriété de la playlist
     * @param int $userId id de l'utilisateur
     * @param int $playlistId id de la playlist
     * @return bool si l'utilisateur est propriétaire de la playlist
     */
    public static function checkPlaylistOfUser(int $userId, int $playlistId) : bool{
        return QuoicouRepository::getInstance()->isPlaylistOfUser($userId, $playlistId);
    }

    public static function checkRole(int $required): void
    {
        if (!unserialize($_SESSION['user'])->role >= $required)
            throw new AccessControlException("droits insuffisants");
    }


}