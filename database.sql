drop database if exists polaznik23;

create database polaznik23;
alter database polaznik23 character set utf8mb4 collate utf8mb4_unicode_ci;

use polaznik23;

create table user (
                       id int auto_increment primary key not null,
                       email varchar(255) not null unique,
                       first_name varchar(255) not null,
                       last_name varchar(255) not null,
                       password varchar(255) not null,
                       image_url varchar(255) default null,
                       deleted tinyint default null,
                       roles json not null,
                       cpu_freq float default null,
                       cpu_cores int default null,
                       gpu_vram int default null,
                       ram int default null,
                       storage int default null

);

create table genre(
                         id int auto_increment primary key not null,
                         name varchar(255) not null unique,
                         deleted tinyint default null
);

create table game(
                      id int auto_increment primary key not null,
                      name varchar(255) unique,
                      release_date date not null,
                      total_ratings_sum int default 0,
                      total_ratings_count int default 0,
                      deleted tinyint default null,
                      image_url varchar(255) default null,
                      cpu_freq float default null,
                      cpu_cores int default null,
                      gpu_vram int default null,
                      ram int default null,
                      storage int default null
);

create table game_genre(
                            id int auto_increment primary key not null,
                            game_id int,
                            genre_id int,
                            FOREIGN KEY (game_id) REFERENCES game(id)
                                on delete restrict,
                            FOREIGN KEY (genre_id) REFERENCES genre(id)
                                on delete restrict
);

create table user_genre(
                           id int auto_increment primary key not null,
                           user_id int,
                           genre_id int,
                           FOREIGN KEY (user_id) REFERENCES user(id)
                               on delete restrict,
                           FOREIGN KEY (genre_id) REFERENCES genre(id)
                               on delete restrict
);

create table review(
                         id int auto_increment primary key not null,
                         user_id int not null,
                         game_id int not null,
                         rating int not null,
                         title varchar(255) not null,
                         review_text text default null,
                         FOREIGN KEY (game_id) REFERENCES game(id)
                             on delete restrict,
                         FOREIGN KEY (user_id) REFERENCES user(id)
                             on delete restrict

);

create trigger update_rating_on_insert
    after insert on review
    for each row
    update game
    SET total_ratings_count = total_ratings_count + 1,
        total_ratings_sum = total_ratings_sum + NEW.rating
    WHERE id = NEW.game_id;

create trigger update_rating_on_delete
    before delete on review
    for each row
    update game
    SET total_ratings_count = total_ratings_count - 1,
        total_ratings_sum = total_ratings_sum - OLD.rating
    WHERE id = OLD.game_id;

create trigger update_rating_on_update
    after update on review
    for each row
    update game
    SET total_ratings_sum = total_ratings_sum - OLD.rating,
        total_ratings_sum = total_ratings_sum + NEW.rating
    WHERE id = NEW.game_id;