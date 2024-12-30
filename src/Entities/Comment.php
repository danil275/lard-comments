<?php

namespace App\Entities;

class Comment
{
    public $id;
    public $username;
    public $content;
    public $articleId;

    public $parentCommentId;
    public array $comments;

    public $createdAt;
    public $updatedAt;
}
