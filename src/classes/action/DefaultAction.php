<?php

namespace src\classes\action;

class DefaultAction extends Action {

    public function lancerGet(): string
    {
        $html = <<<HTML
        <p>Page d'accueil de QuoicouBuzz, pas grand chose à faire ici pour l'instant</p>
HTML;
    return $html;
    }

    public function lancerPost(): string
    {
        return "<p>Pas censé être ici</p>";
    }

}