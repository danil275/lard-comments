<?php

namespace App\Repositories;

use App\Entities\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Interfaces\ArticleRepository;
use PDO;

class DefaultArticleRepository implements ArticleRepository
{

    public function __construct(
        private PDO $pdo
    ) {
    }

    function get(int $id): Article
    {
        $sql = "SELECT * FROM articles WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new ArticleNotFoundException();
        }

        $article = new Article();
        $article->id = $result['id'];
        $article->title = $result['title'];
        $article->content = $result['content'];
        $article->createdAt = new \DateTime($result['created_at']);
        return $article;
    }
}
