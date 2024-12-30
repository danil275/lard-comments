<?php

namespace App\Services;

use App\Entities\Article;
use App\Interfaces\ArticleRepository;
use App\Interfaces\ArticleService;

class DefaultArticleService implements ArticleService
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
    }

    public function getArticleById(int $id): Article
    {
        return $this->articleRepository->get($id);
    }
}
