<?php
namespace kabachello\FileRoute\FileTypes;

use kabachello\FileRoute\Interfaces\ContentInterface;
use cebe\markdown\GithubMarkdown;
use cebe\markdown\Markdown;
use kabachello\FileRoute\Interfaces\FolderStructureInterface;

class MarkdownFile implements ContentInterface
{
    private $contentRaw = '';
    
    private $contentRendered = null;
    
    private $fileInfo = '';
    
    private $folder = null;
    
    public function __construct(string $path, FolderStructureInterface $folder)
    {
        $this->fileInfo = new \SplFileInfo($path);
        $this->contentRaw = file_get_contents($path);
        $this->folder = $folder;
    }
    
    protected function getContentRaw(): string
    {
        return $this->contentRaw;
    }

    public function getContent(): string
    {
        if ($this->contentRendered === null) {
            $this->contentRendered = $this->getParser()->parse($this->getContentRaw());
        }
        return $this->contentRendered;
    }

    public function getTitle(): string
    {
        return ltrim(strtok($this->getContentRaw(), "\n"), "#");
    }

    public function getDateTimeUpdated(): \DateTime
    {
        \DateTime::createFromFormat("U", $this->getFileInfo()->getMTime());
    }

    public function getDateTimeCreated(): \DateTime
    {
        \DateTime::createFromFormat("U", $this->getFileInfo()->getCTime());
    }

    public function getSubtitle(): string
    {
        return '';
    }
    
    protected function getFileInfo() : \SplFileInfo
    {
        return $this->fileInfo;
    }
    
    protected function getParser() : Markdown
    {
        return new GithubMarkdown();
    }
    
    public function getFolder() : FolderStructureInterface
    {
        return $this->folder;
    }
}