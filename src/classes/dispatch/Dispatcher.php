<?php
declare(strict_types=1);

namespace src\classes\dispatch;
class Dispatcher {

    private ?string $action;

    public function __construct(?string $action) {
        if (!isset($action)) {
            $this->action = 'default';
        } else {
            $this->action = $action;
        }
        DeefyRepository::setConfig("config/config.db.ini");
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
                <link rel="stylesheet" href="src/classes/dispatch/styleSpotify.css">
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
                          <li><a href="?action=playlist"><span>Afficher les Playlists</span></a></li>
                          <li><a href="?action=add-playlist"><span>Créer une Playlist</span></a></li>
                          <li><a href="?action=add-track"><span>Ajouter une track</span></a></li>
                          <li><a href="?action=add-user"><span>Ajouter un utilisateur</span></a></li>
                        </ul>
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