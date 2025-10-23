<?php
declare(strict_types=1);

namespace src\classes\render;
interface Renderer
{
    const COMPACT = 1;
    const LONG = 2;

    public function renderCompact() : string;
    public  function renderLong() : string;
    public function render(int $selector) : string;
}