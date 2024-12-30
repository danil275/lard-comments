create table articles (
	id int not null AUTO_INCREMENT primary key,
    title varchar(255) not null,
    content text not null,
    created_at DateTime not null default now()
);