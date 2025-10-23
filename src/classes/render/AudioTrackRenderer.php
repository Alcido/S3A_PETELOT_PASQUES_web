<?php
declare(strict_types=1);
namespace src\classes\render;


use src\classes\audio\tracks\AudioTrack;

abstract class AudioTrackRenderer implements Renderer
{
    public AudioTrack $audioTrack;

    public function __construct(AudioTrack $audioTrack)
    {
        $this->audioTrack = $audioTrack;
    }

    public abstract function renderCompact() : string;
    public abstract function renderLong() : string;

    public function render(int $selector) : string
    {
        switch ($selector)
        {
            case Renderer::COMPACT:
                $this->renderCompact();
                break;
            case Renderer::LONG:
                $this->renderLong();
                break;
            default:
                break;
        }
    }
}