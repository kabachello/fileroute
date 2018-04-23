<?php
namespace kabachello\FileRoute\Interfaces;

interface FileReaderInterface
{
    public function readFile(string $path) : ContentInterface;
}