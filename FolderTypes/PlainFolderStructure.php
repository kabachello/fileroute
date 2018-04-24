<?php
namespace kabachello\FileRoute\FolderTypes;

use kabachello\FileRoute\Interfaces\ContentInterface;
use kabachello\FileRoute\Interfaces\FolderStructureInterface;
use kabachello\FileRoute\Interfaces\FileReaderInterface;

class PlainFolderStructure implements FolderStructureInterface
{
    private $path = null;
    
    private $url = null;
    
    private $reader = null;
    
    private $indexFileName = null;
    
    private $contents = null;
    
    private $fileMask = null;
    
    public function __construct(string $filePath, string $urlPath, FileReaderInterface $fileReader, $indexFileName, $fileMask = '*')
    {
        if (! is_dir($filePath)) {
            throw new \UnexpectedValueException('Cannot read folder structure: "' . $filePath . '" is not a valid folder!');
        }
        $this->path = $filePath;
        $this->url = $urlPath;
        $this->reader = $fileReader;
        $this->indexFileName = $indexFileName;
        $this->fileMask = $fileMask;
    }
    
    public function getIterator()
    {
        return $this->getContents();
    }
    
    public function getContents() : array
    {
        if ($this->contents === null) {
            foreach ($this->getFiles() as $path) {
                $this->contents[] = $this->reader->readFile($path);       
            }
        }
        return $this->contents;
    }
    
    public function getFiles() : array
    {
        return glob(rtrim($this->path, "\\/") . DIRECTORY_SEPARATOR . $this->fileMask, glob);
    }

    public function getIndex(): ContentInterface
    {
        return $this->reader->readFile($this->getIndexFilePath());
    }
    
    public function hasIndex() : bool
    {
        return file_exists($this->getIndexFilePath());
    }
        
    public function getIndexFilePath()
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->indexFileName;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \kabachello\FileRoute\Interfaces\FolderStructureInterface::getIndexUrlPath()
     */
    public function getIndexUrlPath()
    {
        return ($this->url !== '' ? $this->url . '/' : '') . $this->indexFileName;
    }
    
    public function getParent(): FolderStructureInterface
    {
        $fileFolder = pathinfo($this->path, PATHINFO_DIRNAME);
        $urlFolder = pathinfo($this->url, PATHINFO_DIRNAME);
        if ($urlFolder === '.') {
            $urlFolder = '';
        }
        return $this->reader->readFolder($fileFolder, $urlFolder);
    }
    
    public function getFilePath() : string
    {
        return $this->path;
    }
    
    public function getUrlPath() : string
    {
        return $this->url;
    }

    public function getName() : string
    {
        return ucfirst(str_replace('_', ' ', pathinfo($this->url, PATHINFO_BASENAME)));
    }
    
    public function isUrlRoot() : bool
    {
        return $this->url === '';
    }
}