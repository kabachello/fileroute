<?php
namespace kabachello\FileRoute\Templates;

use kabachello\FileRoute\Interfaces\ContentInterface;
use kabachello\FileRoute\Interfaces\TemplateInterface;
use kabachello\FileRoute\Interfaces\FolderStructureInterface;

class PlaceholderFileTemplate implements TemplateInterface
{
    private $pathToTemplate = null;
    
    private $baseUrl = null;
    
    private $breadcrumbsRootName = 'Home';
    
    public function __construct(string $pathToTemplate, string $baseUrl)
    {
        $this->pathToTemplate = $pathToTemplate;
        $this->baseUrl = $baseUrl;
    }
    
    public function render(ContentInterface $content): string
    {
        $tpl = file_get_contents($this->pathToTemplate);
        
        if ($tpl === false) {
            throw new \UnexpectedValueException('Template file "' . $this->pathToTemplate . '" not readable!');
        }
        
        
        
        $phs = [
            'title' => $content->getTitle(),
            'subtitle' => $content->getSubtitle(),
            'content' => $content->getContent(),
            'baseurl' => $this->baseUrl,
            'breadcrumbs' => $this->buildHtmlBreadcrumbs($this->baseUrl, $content)
        ];
        
        $html = $this::replacePlaceholders($tpl, $phs);
        $urlPath = $content->getUrlPath();
        $urlFolder = $content->getFolder()->getUrlPath();
        $html = $this->rebaseRelativeLinks($html, $this->baseUrl . '/' . $urlFolder);
        
        return $html;
    }
    
    
    /**
     * Looks for placeholders ([#...#]) in a string and replaces them with values from
     * the given array, where the key matches the placeholder.
     *
     * Examples:
     * - replacePlaceholder('Hello [#world#][#dot#]', ['world'=>'WORLD', 'dot'=>'!']) -> "Hello WORLD!"
     * - replacePlaceholder('Hello [#world#][#dot#]', ['world'=>'WORLD']) -> exception
     * - replacePlaceholder('Hello [#world#][#dot#]', ['world'=>'WORLD'], false) -> "Hello WORLD"
     *
     * @param string $string
     * @param string[] $placeholders
     * @param bool $strict
     *
     * @throws \RangeException if no value is found for a placeholder
     *
     * @return string
     */
    protected static function replacePlaceholders(string $string, array $placeholders, bool $strict = true) : string
    {
        $phs = static::findPlaceholders($string);
        $search = [];
        $replace = [];
        foreach ($phs as $ph) {
            if (! isset($placeholders[$ph])) {
                if ($strict === true) {
                    throw new \RangeException('Missing value for "' . $ph . '"!');
                } else {
                    $replace[] = '';
                }
            }
            $search[] = '[#' . $ph . '#]';
            $replace[] = $placeholders[$ph];
        }
        return str_replace($search, $replace, $string);
    }
    
    /**
     * Returns an array of ExFace-placeholders found in a string.
     * E.g. will return ["name", "id"] for string "Object [#name#] has the id [#id#]"
     *
     * @param string $string
     * @return array
     */
    protected static function findPlaceholders($string)
    {
        $placeholders = array();
        preg_match_all("/\[#([^\]\[#]+)#\]/", $string, $placeholders);
        return is_array($placeholders[1]) ? $placeholders[1] : array();
    }
    
    /**
     *
     * @param string $html
     * @return mixed
     */
    protected function rebaseRelativeLinks(string $html, string $baseUrl) : string
    {
        $base = rtrim($baseUrl, "/\\") . '/';
        $html = preg_replace('#(href|src)="([^:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#','$1="' . $base . '$2$3', $html);
        return $html;
    }
    
    protected function buildHtmlBreadcrumbs(string $baseUrl, ContentInterface $content) : string
    {
        $baseUrl = rtrim($baseUrl, "/") . '/';
        $urlPath = $content->getFolder()->getUrlPath();
        if ($urlPath === '' || $urlPath === '/') {
            return '';
        }
        $html = '';
        $crumbs = explode('/', $urlPath);
        array_unshift($crumbs, '');
        $crumbsCount = count($crumbs);
        $folders = [];
        $folder = $content->getFolder();
        for ($i=0; $i<$crumbsCount; $i++) {
            $folders[($crumbsCount-$i-1)] = $folder;
            $folder = $folder->getParent();
        }
        $crumbPath = '';
        foreach ($crumbs as $nr => $crumb) {
            $crumbFolder = $folders[$nr];
            $crumbTitle = $crumbFolder->isUrlRoot() ? $this->getBreadcrumbsRootName() : $crumbFolder->getName();
            if ($content->getUrlPath() === $crumbFolder->getIndexUrlPath()) {
                continue;
            }
            if ($crumbPath !== '' && ! $crumbFolder->hasIndex()) {
                $html .= ($html ? ' > ' : '') . $crumbTitle;
            } else {
                $html .= ($html ? ' > ' : '') . '<a href="' . $baseUrl . $crumbFolder->getIndexUrlPath() . '">' . $crumbTitle . '</a>';
            }
            $crumbPath .= '/' . $crumb;
        }
        return $html;
    }
    
    public function setBreadcrumbsRootName(string $name) : TemplateInterface
    {
        $this->breadcrumbsRootName = $name;
        return $this;
    }
    
    public function getBreadcrumbsRootName() : string
    {
        return $this->breadcrumbsRootName;
    }
}