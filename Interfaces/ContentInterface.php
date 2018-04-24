<?php
namespace kabachello\FileRoute\Interfaces;

interface ContentInterface
{    
    public function getContent() : string;
    
    public function getTitle() : string;
    
    public function getSubtitle() : string;
    
    public function getFolder() : FolderStructureInterface;
    
    public function getDateTimeCreated() : \DateTime;
    
    public function getDateTimeUpdated() : \DateTime;
    
    public function getUrlPath() : string;
}