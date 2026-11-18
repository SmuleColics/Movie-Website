-- Restores the titles shown in the Movies page and Series page carousels.
-- Their banner images, title images and videos were always in the project,
-- but their database rows were added after the old dump was made.
-- Safe to run more than once: each row is added only if it is missing.
-- Genre ids: 1 Action, 2 Adventure, 4 Comedy, 5 Crime, 7 Drama, 8 Family,
--            14 Science fiction, 16 Thriller, 25 Talk, 26 War & Politics.
-- Note: Amaran and The Island are films, but the original site showed them in
-- the Series carousel, so they keep category 'Series' here.

USE db_finalproject;

-- ---------- Movies page carousel ----------
INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'Kraven the Hunter', '2h 7min', 'KravenMovieCarousel.jpg', 'KravenVideoMovieCarousel.mp4', 'kravenTitleMovieCarousel.png',
       2024, 16, 'Movie', 'Aaron Taylor-Johnson, Ariana DeBose, Russell Crowe',
       'Sergei Kravinoff, a hunter shaped by a brutal father, sets out to prove he is the greatest predator alive.',
       1, 2, 16
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'KravenMovieCarousel.jpg');

INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'Panda Plan', '1h 39min', 'PandaPlanMovieCarousel.jpg', 'PandaPlanVideoMovieCarousel.mp4', 'pandaPlanTitleMovieCarousel.png',
       2024, 13, 'Movie', 'Jackie Chan',
       'Jackie Chan, playing himself, steps in to protect a rare baby panda from an international crime ring.',
       1, 4, 8
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'PandaPlanMovieCarousel.jpg');

INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'Venom: The Last Dance', '1h 49min', 'VenomMovieCarousel.jpg', 'VenomVideoMovieCarousel.mp4', 'venomTitleMovieCarousel.png',
       2024, 13, 'Movie', 'Tom Hardy, Chiwetel Ejiofor, Juno Temple',
       'Eddie Brock and Venom are hunted by forces from both their worlds and must make one final, devastating choice.',
       1, 2, 14
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'VenomMovieCarousel.jpg');

-- ---------- Series page carousel ----------
INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'The Late Show with Stephen Colbert', 'Talk show', 'LateNightShowSeriesCarousel.jpg', 'LateNightVideoSeriesCarousel.mp4', 'LateNightShowTitleSeriesCarousel.png',
       2015, 13, 'Series', 'Stephen Colbert',
       'Stephen Colbert hosts a late-night mix of comedy, celebrity interviews and live music from New York.',
       25, 4, NULL
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'LateNightShowSeriesCarousel.jpg');

INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'The Island', '1h 30min', 'TheIslandSeriesCarousel.jpg', 'TheIslandVideoSeriesCarousel.mp4', 'TheIslandTitleSeriesCarousel.png',
       2023, 16, 'Series', 'Michael Jai White',
       'A city cop returns to the island he grew up on after his brother''s death and takes on the crime boss who controls it.',
       1, 5, 16
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'TheIslandSeriesCarousel.jpg');

INSERT INTO tbl_movie_series (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, cast, description, genre_id1, genre_id2, genre_id3)
SELECT 'Amaran', '2h 49min', 'AmaranSeriesCarousel.jpg', 'AmaranVideoSeriesCarousel.mp4', 'AramanTitleSeriesCarousel.png',
       2024, 13, 'Series', 'Sivakarthikeyan, Sai Pallavi',
       'The true story of an Indian Army officer, his love story, and the mission that defined his life.',
       1, 7, 26
WHERE NOT EXISTS (SELECT 1 FROM tbl_movie_series WHERE poster = 'AmaranSeriesCarousel.jpg');
