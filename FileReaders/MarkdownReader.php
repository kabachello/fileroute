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
        $folder = $this->readFolder(pathinfo($filePath, PATHINFO_DIRNAME));
        return new MarkdownFile($filePath, $urlPath, $folder);
    }
    
    protected function readFolder(string $path) : FolderStructureInterface
    {
        return new PlainFolderStructure($path, $this, 'index.md', '*.md');
    }
}