<?php
namespace kabachello\FileRoute\Interfaces;

interface TemplateInterface
{
    public function render(ContentInterface $content) : string;
}