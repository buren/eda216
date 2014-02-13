-- 7.a
set foreign_key_checks=0;
drop table if exists users;
drop table if exists movie_performances;
drop table if exists reservations;
drop table if exists movies;
drop table if exists theaters;
set foreign_key_checks=1;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `movies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `theaters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL UNIQUE,
  `seats` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

-- CREATE TABLE `movie_performances` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `movie_id` int(11) NOT NULL,
--   `show_date` date NOT NULL,
--   `theater_id` int(11) NOT NULL,
--   PRIMARY KEY (`id`),
--   FOREIGN KEY (movie_id) REFERENCES movies(id),
--   FOREIGN KEY (theater_id) REFERENCES theaters(id)
-- ); -- MUST use (movie_id, show_date) as foreign key (for some freakin stupid reason)
-- ALTER TABLE movie_performances add unique index(movie_id, show_date);
CREATE TABLE `movie_performances` (
  `movie_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `theater_id` int(11) NOT NULL,
  PRIMARY KEY (movie_id, show_date),
  FOREIGN KEY (movie_id) REFERENCES movies(id),
  FOREIGN KEY (theater_id) REFERENCES theaters(id)
);


CREATE TABLE `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `movie_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (movie_id, show_date) REFERENCES movie_performances(movie_id, show_date)
);

INSERT INTO `users`
(`username`, `name`, `address`, `phone`)
VALUES
("buren", "Jacob", "Bankgatan 14", "0735005004");

INSERT INTO `users`
(`username`, `name`, `address`, `phone`)
VALUES
("nerub", "bocaj", "bnkgtn 14", "4005005370");

INSERT INTO `users`
(`username`, `name`, `address`, `phone`)
VALUES
("jac", "bur", "lund", "0708123320");

INSERT INTO `movies`
(`name`) VALUES ("thor 1");

INSERT INTO `movies`
(`name`) VALUES ("thor 2");

INSERT INTO `movies`
(`name`) VALUES ("thor 3");

INSERT INTO `movies`
(`name`) VALUES ("thor 4");

INSERT INTO `theaters`
(`name`, `seats`)
VALUES
("Salong", 1);

INSERT INTO `theaters`
(`name`, `seats`)
VALUES
("Salong 1", 1);

INSERT INTO `theaters`
(`name`, `seats`)
VALUES
("Salong 2", 1);

INSERT INTO `theaters`
(`name`, `seats`)
VALUES
("Salong 3", 1);

INSERT INTO `movie_performances`
(`movie_id`, `show_date`, `theater_id`)
VALUES
(1, "2014-03-03", 1);

INSERT INTO `movie_performances`
(`movie_id`, `show_date`, `theater_id`)
VALUES
(2, "2014-03-03", 2);

INSERT INTO `movie_performances`
(`movie_id`, `show_date`, `theater_id`)
VALUES
(3, "2014-03-03", 3);

INSERT INTO `movie_performances`
(`movie_id`, `show_date`, `theater_id`)
VALUES
(4, "2014-03-03", 4);


-- 7.b
SELECT * FROM `movie_performances`
INNER JOIN movies ON `movie_performances`.movie_id = `movies`.id;


-- 7.c
INSERT INTO `reservations`
(`user_id`, `movie_id`, `show_date`)
VALUES
(1, 1, '2013-03-03');
-- 9
-- Available seats is not enfored i DB for reservations

-- Relationship model
-- users(id, username, name, address, phone);
-- reservations(id, user_id, movie_performance_id);
-- movie_performances(id, movie_id, show_date, theater_id);
-- movies(id, name);
-- theaters(id, name, seats)
