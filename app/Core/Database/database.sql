CREATE TABLE users
(
    id       BIGINT auto_increment NOT NULL,
    name     varchar(100) NOT NULL,
    email    varchar(200) NOT NULL,
    password varchar(200) NOT NULL,
    token    varchar(200) NULL,
    CONSTRAINT users_pk PRIMARY KEY (id)
);
CREATE TABLE drinks
(
    id         BIGINT auto_increment NOT NULL,
    user_id    BIGINT NOT NULL,
    drink     INT       DEFAULT 1 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL,
    CONSTRAINT drinks_pk PRIMARY KEY (id),
    CONSTRAINT drinks_FK FOREIGN KEY (user_id) REFERENCES users (id) on delete cascade on update cascade
)
