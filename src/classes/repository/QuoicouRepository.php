<?php

namespace src\classes\repository;

use src\classes\audio\lists\Playlist;
use src\classes\audio\tracks\AlbumTrack;
use src\classes\audio\tracks\AudioTrack;
use src\classes\audio\tracks\PodcastTrack;


/**
 * Repository pour communiquer avec la BDD
 */
class QuoicouRepository {

    private \PDO $pdo;
    private static ?QuoicouRepository $instance = null;
    private static array $config = [];

    /** Constructeur
     * @param array $conf tableau de configuration
     */
    private function __construct(array $conf) {
        $this->pdo = new \PDO($conf["dsn"], $conf["username"], $conf["password"], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_PERSISTENT => true]);
    }

    /** Méthode renvoyant l'instance du singleton
     * @return QuoicouRepository instance du singleton
     */
    public static function getInstance() : QuoicouRepository {
        if (is_null(self::$instance)) {
            self::$instance = new QuoicouRepository(self::$config);
        }
        return self::$instance;
    }

    /** Configuration du repository
     * @param string $file fichier de configuration
     * @return void
     * @throws \Exception
     */
    public static function setConfig(string $file) {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Erreur de lecture du fichier de configuration");
        }
        $dsn = $conf['driver'].":host=".$conf['host'].";dbname=".$conf['dbname'];
        self::$config = ['dsn' => $dsn, 'username' => $conf['username'], 'password' => $conf['password']];
    }

    /** Trouver la playlist via son ID
     * @param int $pl_id id de la playlist
     * @return Playlist|null la playlist ou rien
     */
    public function findPlaylistById(int $pl_id) : ?Playlist {
        // On exécute la requête
        $query = "
        select nom from playlist where id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($pl_id));

        // On parcourt le résultat de la requête
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pistes = QuoicouRepository::getInstance()->findTrackByPlaylist($pl_id);
            $playlist = new Playlist($row["nom"], $pistes, $pl_id);
        }

        // On renvoit la playlist
        return $playlist ?: null;
    }

    /** Trouver les pistes d'une playlist dans la BDD
     * @param int $pl_id id de la playlist
     * @return array tableau de pistes
     * @throws \src\classes\exception\InvalidPropertyValueException
     */
    public function findTrackByPlaylist(int $pl_id) : array {
        // On exécute la requête SQL
        $query = "select * from track inner join playlist2track on track.id = playlist2track.id_track where id_pl = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($pl_id));

        // On récupère les pistes
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

    /** Récupérer les playlists d'un utilisateur
     * @param int $us_id id de l'utilisateur
     * @return array|null tableau de playlists
     */
    public function getPlaylistByUser(int $us_id) : ?array {
        // On exécute la requête SQL
        $query = "select id_pl from user2playlist where id_user = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($us_id));

        // On récupère les playlists via son ID dans la BDD
        $res = [];
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $res[] = $this->findPlaylistById($row["id_pl"]);
        }
        return $res;
    }

    /** Récupérer toutes les playlists
     * @return array|null tableau de playlists
     */
    public function getAllPlaylist() : ?array {
        // On exécute la requête SQL
        $query = "select id from playlist";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        // On récupère les playlists via son ID dans la BDD
        $res = [];
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $res[] = $this->findPlaylistById($row["id"]);
        }
        return $res;
    }

    /** Sauvegarder une AudioTrack dans la BDD
     * @param AudioTrack $at track à sauvegarder
     * @return AudioTrack track sauvegardée
     */
    public function saveAudioTrack(AudioTrack $at) : AudioTrack {

        // On récupère les valeurs de la piste
        $titre = $at->titre;
        $genre = $at->genre;
        $duree = $at->duree;
        $nomFichier = $at->nomFichier;
        $auteur = $at->auteur;
        $date = $at->annee;

        // On vérifie le type de piste
        if ($at instanceof PodcastTrack){
            // On insère la PodcastTrack dans la BDD
            $query = "Insert into track (titre, genre, duree, filename, type, auteur_podcast, date_podcast) values (?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(array($titre, $genre, $duree, $nomFichier, "P", $auteur, $date));
        } else {
            // On insère la AlbumTrack dans la BDD
            $nomAlbum = $at->nomAlbum;
            $numPiste = $at->numPiste;
            $query = "Insert into track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album) values (?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(array($titre, $genre, $duree, $nomFichier, "A", $auteur, $nomAlbum, $date, $numPiste));
        }

        // On met l'ID de la piste insérée dans la AudioTrack et on la renvoit
        $at->setID(intval($this->pdo->lastInsertId()));
        return $at;
    }

    /** Ajout d'une piste à une playlist dans la BDD
     * @param int $pl_id id de la playlist
     * @param int $track_id id de la piste
     * @return void
     */
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

        // Insertion dans la BDD
        $query = "Insert into playlist2track (id_pl, id_track, no_piste_dans_liste) values (?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        $insertion = $stmt->execute(array($pl_id, $track_id, $res));
    }

    /** Sauvegarder une playlist vide
     * @param Playlist $pl playlist vide
     * @return Playlist playlist à mettre en session
     */
    public function saveEmptyPlaylist(Playlist $pl) : Playlist {
        // Insertion de la playlist dans la BDD
        $query = "Insert into playlist (nom) values (?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($pl->name));
        $pl->setID(intval($this->pdo->lastInsertId()));

        // On renvoit la playlist pour la mettre en session
        return $pl;
    }

    /** Affecter une playlist à un utilisateur
     * @param int $id_user id de l'utilisateur
     * @param int $id_pl id de la playlist
     * @return string alerte d'insertion
     */
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

    /** Méthode permettant de trouver un utilisateur dans la BDD
     * @param string $mail mail de l'utilisateur
     * @return array|null utilisateur sous forme de tableau
     */
    public function getUser(string $mail) : ?array {
        $query = "Select id,email,passwd,role from user where email = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($mail));
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    /** Méthode d'ajout d'un utilisateur à la BDD
     * @param string $mail mail
     * @param string $mdp mot de passe
     * @return array|null utilisateur à mettre en session
     */
    public function addUser(string $mail, string $mdp) : ?array {
        // Vérification du mail
        if ($this->uniqueId($mail)) {
            // Insertion de l'utlisateur
            $query = "Insert into user (email, passwd) values (?, ?)";
            $stmt = $this->pdo->prepare($query);
            $hash = password_hash($mdp, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt->execute(array($mail, $hash));
            $user = ['id' => intval($this->pdo->lastInsertId()), 'email' => $mail, 'role'=> 1];
            return $user;
        }
        return null;
    }

    /** Méthode permettant de vérifier qu'un mail n'est pas dans la base
     * @param string $mail mail à vérifier
     * @return bool si le mail est dans la base
     */
    public function uniqueId(string $mail) : bool {
        // On exécute la requête
        $query = "Select count(id) as cmpt from user where email = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($mail));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Test de présence dans la base
        if ($row['cmpt'] == 1) {
            return false;
        }
        return true;
    }

    /** Vérification de propriété de playlist pour un utilisateur
     * @param int $id_user id de l'utilisateur
     * @param int $id_pl id de la playlist
     * @return bool si l'utilisateur est propriétaire de la playlist
     */
    public function isPlaylistOfUser(int $id_user, int $id_pl) : bool {
        $query = "Select id_user, id_pl from user2playlist where id_user = ? and id_pl = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array($id_user, $id_pl));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (bool)$row;
    }




}