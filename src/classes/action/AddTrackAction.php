<?php

namespace src\classes\action;

use src\classes\audio\tracks\AlbumTrack;
use src\classes\audio\tracks\AudioTrack;
use src\classes\render\PlaylistRenderer;
use src\classes\repository\QuoicouRepository;
use src\classes\tracks\PodcastTrack;

class AddTrackAction extends Action {

    public function lancerGet() : string{
        if (!isset($_SESSION['playlist'])) return "<p>Aucune playlist en session</p>";
        $user = $_SESSION['user'];
        $pl = $_SESSION['playlist'];
        if (!QuoicouRepository::getInstance()->isPlaylistOfUser($user->id, $pl->id)) return "<p>Vous n'êtes pas le propriétaire de la playlist</p>";
        $html =
            <<<HTML
                 <h2>Choisissez un formulaire :</h2>
                      <button onclick="afficherForm('form1')">Formulaire 1</button>
                      <button onclick="afficherForm('form2')">Formulaire 2</button>
                    
                      <div id="form1" class="formulaire">
                        <h3>Ajouter une PodcastTrack</h3>
                         <form method="POST" action="?action=add-track" enctype="multipart/form-data">
                            <fieldset>
                                <legend>Parametres de la track</legend>
                                <label for="in1">Nom de la Track</label>
                                <input type="text" id="in1" name="track-title" placeholder="Track1" required autofocus><br>
                                <label for="in2">Auteur de la Track</label>
                                <input type="text" id="in2" name="track-author" placeholder="Auteur1"><br>
                                <label for="in3">Date de la Track</label>
                                <input type="number" id="in3" name="track-date" placeholder="Annee"><br>
                                <label for="in4">Genre de la Track</label>
                                <input type="text" id="in4" name="track-genre" placeholder="Pop-Rock"><br>
                                <label for="in5">Duree de la Track</label>
                                <input type="number" id="in5" name="track-duree" placeholder="100"><br>
                                <label for="in6">fichier de la Track</label>
                                <input type="file" id="in6" name="track-file" placeholder="track" required><br>
                                <button type="submit" name="validerTrack">Sauvegarder la track</button>
                            </fieldset>
                            </form>
                      </div>
                    
                      <div id="form2" class="formulaire">
                        <h3>Ajouter une AlbumTrack</h3>
                        <form method="POST" action="?action=add-track" enctype="multipart/form-data">
                            <fieldset>
                                <legend>Parametres de la track</legend>
                                <label for="in1">Nom de la Track</label>
                                <input type="text" id="in1" name="track-title" placeholder="Track1" required autofocus><br>
                                <label for="in2">Auteur de la Track</label>
                                <input type="text" id="in2" name="track-author" placeholder="Auteur1"><br>
                                <label for="in3">Date de la Track</label>
                                <input type="number" id="in3" name="track-date" placeholder="Annee"><br>
                                <label for="in4">Genre de la Track</label>
                                <input type="text" id="in4" name="track-genre" placeholder="Pop-Rock"><br>
                                <label for="in5">Duree de la Track</label>
                                <input type="number" id="in5" name="track-duree" placeholder="100"><br>
                                <label for="in6">Nom de l'album</label>
                                <input type="text" id="in6" name="nom-album" placeholder="Valses"><br>
                                <label for="in7">Numéro du titre dans l'album</label>
                                <input type="number" id="in7" name="numero-album" placeholder="2"><br>
                                <label for="in8">fichier de la Track</label>
                                <input type="file" id="in8" name="track-file" placeholder="track" required><br>
                                <button type="submit" name="validerTrack">Sauvegarder la track</button>
                            </fieldset>
                            </form>
                      </div>
                    
                      <script>
                        document.querySelectorAll('.formulaire').forEach(el => el.style.display = 'none');
                        function afficherForm(id) {
                          document.querySelectorAll('.formulaire').forEach(el => el.style.display = 'none');
                          document.getElementById(id).style.display = 'block';
                        }
                      </script>
                    
                HTML;
        return $html;

    }

    public function lancerPost() : string{

        $name = $_POST['track-title'];
        $author = $_POST['track-author'];
        $date = $_POST['track-date'];
        $genre = $_POST['track-genre'];
        $duree = intval($_POST['track-duree']);

        if (!$this->verifDonnee($name, $author, $date, $genre, $duree)) {
            return $this->lancerGet() . "<script>alert(\"mauvais type de données\")</script>";
        }

        if (str_ends_with($_FILES['track-file']['name'], '.mp3') and $_FILES['track-file']['type'] === 'audio/mpeg') {
            $tmp = $_FILES['track-file']['tmp_name'];
            $namefile = $_FILES['track-file']['name'];
            move_uploaded_file($tmp, "files/". $namefile);
        } else {
            return "<b>Mauvais type de fichier</b><br>" . $this->lancerGet();
        }

        if (isset($_POST['nom-album'])) {
            $album = $_POST['nom-album'];
            $numTrack = $_POST['numero-album'];
            if (!$this->verifDonneeAlbumTrack($album, $numTrack)) {
                return $this->lancerGet() . "<script>alert(\"mauvais type de données\")</script>";
            }
            $track = new AlbumTrack($name, $namefile, $album, $numTrack, $author, $date, $genre, $duree);
        } else {
            $track = new PodcastTrack($name, $namefile,$author, $date, $genre, $duree);
        }

        $newTrack = QuoicouRepository::getInstance()->saveAudioTrack($track);
        $playlist = unserialize($_SESSION['playlist']);
        QuoicouRepository::getInstance()->addTrackToPlaylist($playlist->id,$newTrack->id);

        $playlist = QuoicouRepository::getInstance()->findPlaylistById($playlist->id);
        $_SESSION["playlist"] = serialize($playlist);

        $renderer = new PlaylistRenderer($playlist);
        $affichage = $renderer->render(2);

        return $affichage . "<a href=\"?action=add-track\">Ajouter encore une piste</a>";

    }

    public function verifDonnee(string $name, string $author, mixed $date, string $genre, int $duree) : bool {
        return filter_var($name, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z0-9._-]+$/']])
            and filter_var($author, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z0-9._-]+$/']])
            and (filter_var($date, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                or filter_var($date, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z0-9._-]+$/']]))
            and filter_var($genre, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z0-9._-]+$/']])
            and filter_var($duree, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    }

    public function verifDonneeAlbumTrack( string $album, int $numAlbum) : bool {
        return filter_var($album,FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z0-9._-]+$/']])
        and filter_var($numAlbum, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    }

}