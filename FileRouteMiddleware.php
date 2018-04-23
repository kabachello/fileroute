<?php
namespace kabachello\FileRoute;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use kabachello\FileRoute\Interfaces\FileReaderInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;

class FileRouteMiddleware implements MiddlewareInterface
{
    private $urlMatcher = null;
    
    private $loader = null;
    
    private $logger = null;
    
    public function __construct(callable $urlMatcher, FileReaderInterface $loader, LoggerInterface $logger = null)
    {
        $this->urlMatcher = $urlMatcher;
        $this->loader = $loader;
        $this->logger = $logger;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = call_user_func($this->urlMatcher, $request->getUri());
        if ($path === false || ! file_exists($path)) {
            return $handler->handle($request);
        }
        
        try {
            $contentFile = $this->loader->load($path);
            $body = $contentFile->getContent();
            $response = new Response(200, [], $body);
            return $response;
        } catch (\Throwable $e) {
            // TODO
            throw $e;
        }
    }
}