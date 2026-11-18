-- Restores the 3 titles shown in the home page carousel.
-- Their posters, title images and videos were always in the project
-- (DASHBOARD-HTML/MOVIE_SERIES_TITLE and MOVIE_SERIES_VIDEO), but their
-- database rows were added after the old dump was made, so they were lost.
-- Safe to run more than once: each row is added only if it is missing.
-- Genre ids: 1 Action, 2 Adventure, 3 Animation, 4 Comedy, 8 Family, 14 Science fiction.

USE db_finalproject;

INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'Captain America: Brave New World', '1h 58min', 'CaptainAmericaHomeCarousel.jpg', 'CAVideoHomeCarousel.mp4', 'CATitleHomeCarousel.png',
       2025, 13, 'Movie', 'Anthony Mackie, Danny Ramirez, Harrison Ford',
       'Sam Wilson, the new Captain America, is caught in a global crisis after a summit goes wrong and must find out who is pulling the strings.',
       1, 2, 14
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'CaptainAmericaHomeCarousel.jpg');

INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'Moana 2', '1h 40min', 'MoanaHomeCarousel.jpg', 'MoanaVideoHomeCarousel.mp4', 'MoanaTitleHomeCarousel.png',
       2024, 7, 'Movie', 'Auli''i Cravalho, Dwayne Johnson, Hualalai Chung',
       'Called by her ancestors, Moana sails with Maui and a new crew to the far seas of Oceania in search of a lost island.',
       3, 2, 8
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'MoanaHomeCarousel.jpg');

INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'Sonic the Hedgehog 3', '1h 50min', 'SonicHomeCarousel.jpg', 'SonicVideoHomeCarousel.mp4', 'SonicTitleHomeCarousel.png',
       2024, 7, 'Movie', 'Jim Carrey, Ben Schwartz, Keanu Reeves',
       'Sonic, Knuckles and Tails face Shadow, a powerful new rival, and are forced into an unlikely team-up with Dr. Robotnik.',
       1, 2, 4
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'SonicHomeCarousel.jpg');
