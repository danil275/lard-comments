<?php

namespace App\Exceptions;

use Exception;

class ArticleNotFoundException extends Exception
{
    public function __construct($message = "Article not found", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
