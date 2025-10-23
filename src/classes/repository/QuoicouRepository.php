<?php

namespace src\classes\repository;

use src\classes\audio\lists\Playlist;
use src\classes\audio\tracks\AlbumTrack;
use src\classes\tracks\PodcastTrack;

class QuoicouRepository {

    private \PDO $pdo;
    private static ?QuoicouRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $this->pdo = new \PDO($conf["dsn"], $conf["username"], $conf["password"], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_PERSISTENT => true]);
    }

    public static function getInstance() : QuoicouRepository {
        if (is_null(self::$instance)) {
            self::$instance = new QuoicouRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file) {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Erreur de lecture du fichier de configuration");
        }
        $dsn = $conf['driver'].":host=".$conf['host'].";dbname=".$conf['dbname'];
        self::$config = ['dsn' => $dsn, 'username' => $conf['username'], 'password' => $conf['password']];
    }

    public function findPlaylistById(int $pl_id) : ?Playlist {
        $query = "
        select nom from playlist where id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($pl_id));
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pistes = QuoicouRepository::getInstance()->findTrackByPlaylist($pl_id);
            $playlist = new Playlist($row["nom"], $pistes, $pl_id);
        }
        return $playlist ?: null;
    }

    public function findTrackByPlaylist(int $pl_id) : array {
        $query = "select * from track inner join playlist2track on track.id = playlist2track.id_track where id_pl = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($pl_id));
        $res = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if ($row['type'] === 'A') {
                $res[] = new AlbumTrack($row["titre"], $row["filename"], $row['titre_album'], $row['numero_album'], $row['artiste_album'], $row['annee_album'], $row['genre'], $row['duree'], $row['id']);
            } else if ($row['type'] === 'P') {
                $res[] = new PodcastTrack($row["titre"], $row["filename"], $row["auteur_podcast"],$row["date_podcast"],$row["genre"],$row["duree"], $row["id"]);
            }
        }
        return $res;
    }

    public function getPlaylistByUser(int $us_id) : ?array {
        $query = "select id_pl from user2playlist where id_user = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($us_id));
        $res = [];
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $res[] = $this->findPlaylistById($row["id_pl"]);
        }
        return $res;
    }




}