<?php
namespace kabachello\FileRoute;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use kabachello\FileRoute\Interfaces\FileReaderInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use kabachello\FileRoute\Interfaces\TemplateInterface;
use kabachello\FileRoute\Exceptions\PageNotFoundException;

class FileRouteMiddleware implements MiddlewareInterface
{
    private $urlMatcher = null;
    
    private $template = null;
    
    private $loader = null;
    
    private $logger = null;
    
    private $basePath = null;
    
    public function __construct(callable $urlMatcher, string $basePath, FileReaderInterface $loader, TemplateInterface $template, LoggerInterface $logger = null)
    {
        $this->urlMatcher = $urlMatcher;
        $this->template = $template;
        $this->loader = $loader;
        $this->logger = $logger;
        $this->basePath = $basePath;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $urlPath = call_user_func($this->urlMatcher, $request->getUri());
        $filePath = $this->basePath . DIRECTORY_SEPARATOR . $urlPath;
        
        if ($urlPath === false) {
            return $handler->handle($request);
        }
        
        try {
            $contentFile = $this->loader->readFile($filePath, $urlPath);
            $body = $this->template->render($contentFile);
            $response = new Response(200, [], $body);
            return $response;
        } catch (PageNotFoundException $e) {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            // TODO
            throw $e;
        }
    }
}