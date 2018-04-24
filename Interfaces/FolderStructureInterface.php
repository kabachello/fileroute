<?php
namespace kabachello\FileRoute\Interfaces;

interface FolderStructureInterface extends \IteratorAggregate
{  
    public function getIndex() : ContentInterface;
    
    public function getParent() : FolderStructureInterface;
    
    public function hasIndex() : bool;
    
    public function getIndexFilePath();
    
    public function getIndexUrlPath();
    
    public function getFilePath() : string;
    
    public function getUrlPath() : string;
    
    public function getName() : string;
    
    public function isUrlRoot() : bool;
}