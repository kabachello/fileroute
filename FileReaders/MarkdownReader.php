<?php
namespace kabachello\FileRoute\FileReaders;

use kabachello\FileRoute\Interfaces\ContentInterface;
use kabachello\FileRoute\Interfaces\FileReaderInterface;
use kabachello\FileRoute\Interfaces\FolderStructureInterface;
use kabachello\FileRoute\FileTypes\MarkdownFile;
use kabachello\FileRoute\FolderTypes\PlainFolderStructure;
use kabachello\FileRoute\Exceptions\PageNotFoundException;

class MarkdownReader implements FileReaderInterface
{    
    public function readFile(string $filePath, string $urlPath): ContentInterface
    {
        if (! file_exists($filePath)) {
            throw new PageNotFoundException('Page "' . $urlPath . '" not found at "' . $filePath . '"!');
        }
        $fileFolder = pathinfo($filePath, PATHINFO_DIRNAME);
        $urlFolder = pathinfo($urlPath, PATHINFO_DIRNAME);
        if ($urlFolder === '.') {
            $urlFolder = '';
        }
        $folder = $this->readFolder($fileFolder, $urlFolder);
        return new MarkdownFile($filePath, $urlPath, $folder);
    }
    
    public function readFolder(string $filePath, string $urlPath) : FolderStructureInterface
    {
        return new PlainFolderStructure($filePath, $urlPath, $this, 'index.md', '*.md');
    }
}