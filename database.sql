drop database if exists polaznik23;

create database polaznik23;
alter database polaznik23 character set utf8mb4 collate utf8mb4_unicode_ci;

use polaznik23;

create table user(
                       id int auto_increment primary key not null,
                       email varchar(100) not null unique,
                       firstname varchar(100) not null,
                       lastname varchar(100) not null,
                       pass varchar(100) not null,
                       imageurl varchar(100) default null,
                       deleted tinyint default 0,
                       roles varchar(100) not null,
                       cpufreq float default null,
                       cpucores int default null,
                       gpuvram int default null,
                       ram int default null,
                       storagespace int default null
);

create table genre(
                         id int auto_increment primary key not null,
                         name varchar(100) not null unique,
                         deleted tinyint default 0
);

create table game(
                      id int auto_increment primary key not null,
                      name varchar(100) unique,
                      releasedate date not null,
                      totalratingssum int default 0,
                      totalratingscount int default 0,
                      deleted tinyint default 0,
                      cpufreq float not null,
                      cpucores int not null,
                      gpuvram int not null,
                      ram int not null,
                      storagespace int not null
);

create table game_genre(
                            id int auto_increment primary key not null,
                            gameID int not null,
                            genreID int not null,
                            FOREIGN KEY (gameID) REFERENCES game(id)
                                on delete restrict,
                            FOREIGN KEY (genreID) REFERENCES genre(id)
                                on delete restrict
);

create table user_genre(
                           id int auto_increment primary key not null,
                           userID int not null,
                           genreID int not null,
                           FOREIGN KEY (userID) REFERENCES user(id)
                               on delete restrict,
                           FOREIGN KEY (genreID) REFERENCES genre(id)
                               on delete restrict
);

create table review(
                         id int auto_increment primary key not null,
                         userID int not null,
                         gameID int not null,
                         rating int not null,
                         title varchar(255) not null,
                         reviewtext text not null,
                         FOREIGN KEY (gameID) REFERENCES game(id)
                             on delete restrict,
                         FOREIGN KEY (userID) REFERENCES user(id)
                             on delete restrict
);

ALTER TABLE review ADD UNIQUE unique_index (userID, gameID);

create trigger update_rating_on_insert
    after insert on review
    for each row
    update game
    SET totalratingscount = totalratingscount + 1,
        totalratingssum = totalratingssum + NEW.rating
    WHERE id = NEW.gameID;

create trigger update_rating_on_delete
    before delete on review
    for each row
    update game
    SET totalratingscount = totalratingscount - 1,
        totalratingssum = totalratingssum - OLD.rating
    WHERE id = OLD.gameID;

create trigger update_rating_on_update
    after update on review
    for each row
    update game
    SET totalratingssum = totalratingssum - OLD.rating,
        totalratingssum = totalratingssum + NEW.rating
    WHERE id = NEW.gameID;

insert into user (firstname, lastname, email, pass, roles) values ('Admin', 'Admin', 'admin@admin.com', '$2y$10$MkCtNDV4iBI4ySwOiqTIA.HOd55UjDo2b9ibVmq4dkQXqSKPl3aLq','ROLE_USER,ROLE_ADMIN');