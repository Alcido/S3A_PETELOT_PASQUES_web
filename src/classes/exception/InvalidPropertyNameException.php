<?php
declare(strict_types=1);

namespace src\classes\exception;
class InvalidPropertyNameException extends \Exception
{

    public function __construct(string $mes) {
        parent::__construct($mes);
    }

}