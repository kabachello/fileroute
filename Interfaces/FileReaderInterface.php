<?php
namespace kabachello\FileRoute\Interfaces;

interface FileReaderInterface
{
    public function readFile(string $filePath, string $urlPath) : ContentInterface;
}