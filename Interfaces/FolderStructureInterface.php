<?php
namespace kabachello\FileRoute\Interfaces;

interface FolderStructureInterface extends \IteratorAggregate
{  
    public function getIndex() : ContentInterface;
    
    public function getParent() : FolderStructureInterface;
}