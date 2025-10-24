<?php

namespace src\classes\action;

/**
 * Action par défaut
 */
class DefaultAction extends Action {

    /** Méthode de lancement de GET
     * @return string page d'accueil en HTML
     */
    public function lancerGet(): string
    {
        $html = <<<HTML
        <p>Page d'accueil de QuoicouBuzz, pas grand chose à faire ici pour l'instant</p>
HTML;
    return $html;
    }

    /** Méthode de lancement du POST
     * @return string message d'erreur
     */
    public function lancerPost(): string
    {
        return "<p>Pas censé être ici</p>";
    }

}