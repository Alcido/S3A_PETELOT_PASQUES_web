<?php

namespace src\classes\repository;

use src\classes\audio\lists\Playlist;
use src\classes\audio\tracks\AlbumTrack;
use src\classes\audio\tracks\AudioTrack;
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

    public function saveAudioTrack(AudioTrack $at) : AudioTrack {
        $titre = $at->titre;
        $genre = $at->genre;
        $duree = $at->duree;
        $nomFichier = $at->nomFichier;
        $auteur = $at->auteur;
        $date = $at->annee;

        if ($at instanceof PodcastTrack){
            $query = "Insert into track (titre, genre, duree, filename, type, auteur_podcast, date_podcast) values (?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(array($titre, $genre, $duree, $nomFichier, "P", $auteur, $date));
        } else {
            $nomAlbum = $at->nomAlbum;
            $numPiste = $at->numPiste;

            $query = "Insert into track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album) values (?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(array($titre, $genre, $duree, $nomFichier, "A", $auteur, $nomAlbum, $date, $numPiste));
        }
        $at->setID(intval($this->pdo->lastInsertId()));
        return $at;
    }

    public function addTrackToPlaylist(int $pl_id, int $track_id) : void {

        $query = "select max(no_piste_dans_liste) from playlist2track where id_track = ? and id_pl = ? group by id_pl";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($track_id, $pl_id));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row && isset($row['max(no_piste_dans_liste)'])) {
            $res = intval($row['max(no_piste_dans_liste)']) + 1;
        } else {
            $res = 1;
        }

        $query = "Insert into playlist2track (id_pl, id_track, no_piste_dans_liste) values (?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        $insertion = $stmt->execute(array($pl_id, $track_id, $res));
    }

    public function saveEmptyPlaylist(Playlist $pl) : Playlist {
        $query = "Insert into playlist (nom) values (?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($pl->name));
        $pl->setID(intval($this->pdo->lastInsertId()));
        return $pl;
    }

    public function addUserToPlaylist(int $id_user, int $id_pl) : string {
        $query = "Insert into user2playlist (id_user, id_pl) values (?, ?)";
        $stmt = $this->pdo->prepare($query);
        $insertion = $stmt->execute(array($id_user, $id_pl));
        if ($insertion) {
            $html = "<script>alert(\"Playlist linked avec l'utilisateur\")</script>";
        } else {
            $html = "<script>alert(\"Erreur lors de la liaison user-playlist\")</script>";
        }
        return $html;
    }

    public function getUser(string $mail) : ?array {
        $query = "Select id,email,passwd,role from user where email = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($mail));
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function addUser(string $mail, string $mdp) : ?array {

        if ($this->uniqueId($mail)) {
            $query = "Insert into user (email, passwd) values (?, ?)";
            $stmt = $this->pdo->prepare($query);
            $hash = password_hash($mdp, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt->execute(array($mail, $hash));
            $user = ['id' => intval($this->pdo->lastInsertId()), 'email' => $mail, 'role'=> 1];
            return $user;
        }
        return null;
    }

    public function uniqueId(string $mail) : bool {
        $query = "Select count(id) as cmpt from user where email = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($mail));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row['cmpt'] == 1) {
            return false;
        }
        return true;
    }




}