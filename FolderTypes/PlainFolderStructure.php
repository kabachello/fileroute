<?php
namespace kabachello\FileRoute\FolderTypes;

use kabachello\FileRoute\Interfaces\ContentInterface;
use kabachello\FileRoute\Interfaces\FolderStructureInterface;
use kabachello\FileRoute\Interfaces\FileReaderInterface;

class PlainFolderStructure implements FolderStructureInterface
{
    private $path = null;
    
    private $reader = null;
    
    private $indexFileName = null;
    
    private $contents = null;
    
    private $fileMask = null;
    
    public function __construct(string $path, FileReaderInterface $fileReader, $indexFileName, $fileMask = '*')
    {
        if (! is_dir($path)) {
            throw new \UnexpectedValueException('Cannot read folder structure: "' . $path . '" is not a valid folder!');
        }
        $this->path = $path;
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
        $this->reader->readFile($this->indexFileName);
    }
    
    public function getParent(): FolderStructureInterface
    {
        return $this->readFolder(pathinfo($this->path, PATHINFO_DIRNAME));
    }

    
}