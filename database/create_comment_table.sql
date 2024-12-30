create table comments (
	id int not null AUTO_INCREMENT primary key,
    username varchar(255) not null,
    content tinytext not null,
    article_id int not null references articles(id) on delete cascade,
    parent_comment_id int default null references comments(id) on delete cascade,
    created_at DateTime not null default now(),
    updated_at DateTime null
);