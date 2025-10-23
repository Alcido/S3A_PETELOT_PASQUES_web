<?php

namespace src\classes\action;

class DisconnectAction extends Action {

    public function lancerGet(): string
    {
        return $this->lancerPost();
    }

    public function lancerPost(): string
    {
        unset($_SESSION['user']);
        header("Location: ?action=login");
        return "";
    }

}