<?php

namespace App\Interfaces;

use App\Entities\Article;

interface ArticleRepository
{
    function get(int $id): Article;
}
