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
use src\classes\action\ShowCurrPlaylistAction;
use src\classes\repository\QuoicouRepository;

class Dispatcher {

    private ?string $action;

    public function __construct(?string $action) {
        if (!isset($action)) {
            $this->action = 'default';
        } else {
            $this->action = $action;
        }
        QuoicouRepository::setConfig("config/config.db.ini");
    }

    public function run() : void {
        switch ($this->action) {
            case 'pl-user':
                $actionExec = new PlaylistUserAction;
                break;
            case 'add-track':
                $actionExec = new AddTrackAction;
                break;
            case 'add-playlist':
                $actionExec = new AddPlaylistAction;
                break;
            case 'pl-current':
                $actionExec = new ShowCurrPlaylistAction;
                break;
            case 'login':
                $actionExec = new LoginAction;
                break;
            case 'register':
                $actionExec = new RegisterAction;
                break;
            case 'disconnect':
                $actionExec = new DisconnectAction;
                break;
            case 'default':
            default:
                $actionExec = new DefaultAction;
                break;
        }
        $this->renderPage($actionExec());
    }

    private function renderPage(string $html) : void {
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
        if ($this->action !== 'login') {
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

        echo $page;
    }

}