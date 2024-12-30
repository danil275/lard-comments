<?php

namespace App\Repositories;

use App\Entities\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Interfaces\CommentRepository;
use DateTime;
use PDO;

class DefaultCommentRepository implements CommentRepository
{

    public function __construct(
        private PDO $pdo
    ) {
    }
    public function countNestingOfResponses($id)
    {
        // Начальный уровень вложенности
        $level = 0;
        $currentId = $id;

        // Цикл для поднятия по дереву комментариев
        while (true) {
            $query = $this->pdo->prepare('SELECT parent_comment_id FROM comments WHERE id = :id');
            $query->execute(['id' => $currentId]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['parent_comment_id'] !== null) {
                $currentId = $result['parent_comment_id'];
                $level++;
            } else {
                break;
            }
        }

        return $level;
    }

    public function get(int $id): Comment
    {
        $sql = "SELECT * FROM comments WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException();
        }

        $comment = new Comment();
        $comment->id = $result['id'];
        $comment->username = $result['username'];
        $comment->content = $result['content'];
        $comment->articleId = $result['article_id'];
        $comment->createdAt = new DateTime($result['created_at']);
        $comment->comments = [];
        if ($result['updated_at'] !== null) {
            $comment->updatedAt = new DateTime($result['updated_at']);
        }

        return $comment;
    }

    public function getByArticle(int $id): array
    {
        $sql = "SELECT * FROM comments WHERE article_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $comments = [];

        foreach ($result as $r) {
            $comment = new Comment();
            $comment->id = $r['id'];
            $comment->username = $r['username'];
            $comment->content = $r['content'];
            $comment->articleId = $r['article_id'];
            $comment->createdAt = new DateTime($r['created_at']);
            $comment->comments = [];
            if ($r['updated_at'] !== null) {
                $comment->updatedAt = new DateTime($r['updated_at']);
            }

            if ($r['parent_comment_id'] === null) {
                $comments[$comment->id] = $comment;
            } else {
                $c = $this->recursiveArraySearch($comments, $r['parent_comment_id']);
                $c->comments[$comment->id] = $comment;
            }
        }

        return $comments;
    }

    private function recursiveArraySearch($commentsArray, $id)
    {
        if (isset($commentsArray[$id])) {
            return $commentsArray[$id];
        }
        foreach ($commentsArray as $comment) {
            $result = $this->recursiveArraySearch($comment->comments, $id);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }

    public function createForArticle(int $articleId, string $username, string $content): int
    {
        $sql = "INSERT INTO comments (username, content, article_id) VALUES (:username, :content, :articleId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':articleId', $articleId);

        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function createForComment(int $id, int $articleId, string $username, string $content): int
    {
        $sql = "INSERT INTO comments (username, content, article_id, parent_comment_id) VALUES (:username, :content, :article_id, :commentId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':article_id', $articleId);
        $stmt->bindParam(':commentId', $id);

        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function update(int $id, string $content)
    {
        $sql = "UPDATE comments SET content = :content, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    }
}
