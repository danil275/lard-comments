<?php

namespace App\Interfaces;

use App\Entities\Article;

interface ArticleService 
{
    function getArticleById(int $id): Article;
}