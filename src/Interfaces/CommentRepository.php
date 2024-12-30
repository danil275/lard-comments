<?php

namespace App\Interfaces;

use App\Entities\Comment;

interface CommentRepository
{
    public function get(int $id): Comment;
    public function getByArticle(int $id): array;
    public function createForArticle(int $articleId, string $username, string $content): int;
    public function createForComment(int $id, int $articleId, string $username, string $content): int;
    public function update(int $id, string $content);
    public function countNestingOfResponses($id);
}
