<?php

namespace src\classes\exception;

use Throwable;

class AccessControlException extends \Exception {

    public function __construct(?string $message = null) {
        parent::__construct($message);
    }
}