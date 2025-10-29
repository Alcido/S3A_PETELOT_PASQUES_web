<?php
declare(strict_types=1);

namespace src\classes\dispatch;
use src\classes\action\AddPlaylistAction;
use src\classes\action\AddTrackAction;
use src\classes\action\DefaultAction;
use src\classes\action\DisconnectAction;
use src\classes\action\LoginAction;
use src\classes\action\PlaylistUserAction;
use src\classes\action\RegisterAction;
use src\classes\action\SelectPlaylistAction;
use src\classes\action\ShowCurrPlaylistAction;
use src\classes\repository\QuoicouRepository;

/**
 * Dispatcher permettant de gérer les différentes actions
 */
class Dispatcher {

    private ?string $action;

    /** Constructeur du Dispatcher
     * @param string|null $action type d'action
     * @throws \Exception erreur
     */
    public function __construct(?string $action) {
        if (!isset($action)) {
            $this->action = 'default';
        } else {
            $this->action = $action;
        }
    }

    /** Méthode de lancement de l'action
     * @return void
     */
    public function run() : void {
        switch ($this->action) {
            case 'pl-user': // Affichage des playlists de l'utilisateur
                $actionExec = new PlaylistUserAction;
                break;
            case 'add-track': // Ajout d'une piste
                $actionExec = new AddTrackAction;
                break;
            case 'add-playlist': // Ajout d'une playlist
                $actionExec = new AddPlaylistAction;
                break;
            case 'pl-current': // Affichage de la playlist en session
                $actionExec = new ShowCurrPlaylistAction;
                break;
            case 'login': // Connexion d'un utilisateur
                $actionExec = new LoginAction;
                break;
            case 'register': // Inscription d'un utilisateur
                $actionExec = new RegisterAction;
                break;
            case 'disconnect': // Deconnexion d'un utilisateur
                $actionExec = new DisconnectAction;
                break;
            case 'select-playlist': // Mise d'une playlist de la BDD en session
                $actionExec = new SelectPlaylistAction;
                break;
            case 'default':
            default: // Action par défaut
                $actionExec = new DefaultAction;
                break;
        }

        // Affichage de la page avec le résultat de l'action
        $this->renderPage($actionExec());
    }

    /** Méthode d'affichage de la page dans le navigateur
     * @param string $html résultat de l'action
     * @return void
     */
    private function renderPage(string $html) : void {

        // Page HTML
        $page = <<<HTML
                <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="UTF-8">
                <title>Mon Deefy</title>
                <link rel="stylesheet" href="css/styleSpotify.css">
                </head>
                <body>
                HTML;

        // Si l'utilisateur est connecté
        if ($this->action !== 'login' and isset($_SESSION['user'])) {
            // Affichage du menu
            $page .=
                <<<HTML
                <div id = "choices">
                    <h1>Deefy</h1>
                       <nav>
                        <ul>
                          <li><a href="?action=default"><span>Accueil</span></a></li>
                          <li><a href="?action=pl-user"><span>Afficher vos Playlists</span></a></li>
                          <li><a href="?action=add-track"><span>Ajouter une track à la playlist courante</span></a></li>
                          <li><a href="?action=add-playlist"><span>Créer une nouvelle playlist</span></a></li>
                          <li><a href="?action=pl-current"><span>Afficher la playlist courante</span></a></li>
                          <li><a href="?action=register"><span>Ajouter un utilisateur</span></a></li>
                        </ul>
                        <form action="?action=disconnect" method="post">
                          <button type="submit">Déconnexion</button>
                        </form>
                      </nav>
                    </div>
                HTML;
        }

        // Ajout du résultat de l'action
        $page .=
            <<<HTML
            <main>
                <h2>Bienvenue sur Deefy</h2>
                    <div id="content">
                        $html
                    </div>
            </main>
            </body>
            </html>
            HTML;

        // On envoit la page
        echo $page;
    }

}