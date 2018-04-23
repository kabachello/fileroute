<?php
namespace kabachello\FileRoute\FileReaders;

use kabachello\FileRoute\Interfaces\ContentInterface;
use kabachello\FileRoute\Interfaces\FileReaderInterface;
use kabachello\FileRoute\Interfaces\FolderStructureInterface;
use kabachello\FileRoute\FileTypes\MarkdownFile;
use kabachello\FileRoute\FolderTypes\PlainFolderStructure;

class MarkdownReader implements FileReaderInterface
{
    public function readFile(string $path): ContentInterface
    {
        $folder = $this->readFolder(pathinfo($path, PATHINFO_DIRNAME));
        return new MarkdownFile($path, $folder);
    }
    
    protected function readFolder(string $path) : FolderStructureInterface
    {
        return new PlainFolderStructure($path, $this, 'index.md', '*.md');
    }
}