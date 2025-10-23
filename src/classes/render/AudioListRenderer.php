<?php
declare(strict_types=1);
namespace src\classes\render;


use src\classes\audio\lists\AudioList;

abstract class AudioListRenderer implements Renderer
{
    public AudioList $audioList;

    public function __construct(AudioList $audioList)
    {
        $this->audioList = $audioList;
    }

    public abstract function renderCompact() : string;
    public abstract function renderLong() : string;

    public function render(int $selector) : string {
        switch ($selector)
        {
            case Renderer::COMPACT:
                $html = $this->renderCompact();
                break;
            case Renderer::LONG:
                $html = $this->renderLong();
                break;
            default:
                break;
        }
        return $html;
    }
}