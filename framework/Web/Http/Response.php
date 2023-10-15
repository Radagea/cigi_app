<?php

namespace Majframe\Web\Http;

use Majframe\Web\Http\View;

final class Response
{
    const JSON = 'application/json';
    const XML = 'application/xml';
    const HTML = 'text/html';

    public View|null $view;
    public Array|null $vars;
    private String $contentType;
    private int $responseCode;
    private Array $headers;

    public function __construct(Array $vars = null, View $view = null, int $responseCode = 200, String $contentType = self::HTML)
    {
        $this->view = $view;
        $this->vars = $vars;
        $this->responseCode = $responseCode;
        $this->setContentType($contentType);
    }

    public function setHeader(String $name, String $value) : void
    {
        $this->headers[$name] = $value;
    }

    public function setContentType(String $value) : void
    {
        $this->contentType = $value;
        $this->headers['Content-Type'] = ': ' . $value;
    }

    public function setResponseCode(int $code) : void
    {
        $this->responseCode = $code;
    }

    public function getResponseCode() : int
    {
        return $this->responseCode;
    }

    public function getHeaders() : Array
    {
        return $this->headers;
    }

    public function getContentType() : String
    {
        return $this->contentType;
    }
}