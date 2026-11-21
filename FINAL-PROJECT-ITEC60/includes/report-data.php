<?php
/*
 * Every figure the admin dashboard shows, in one place, so the charts, the
 * written (textual) report and the printed sheet always agree.
 * Archived titles, genres and members are left out of the live figures.
 */

/* The parts of the report an admin can choose to print, in page order. */
const CV_REPORT_PARTS = [
    'summary' => 'Summary',
    'members' => 'Member sign-ups',
    'library' => 'Library by genre',
    'views'   => 'Views by genre',
    'decades' => 'Titles by decade',
    'top'     => 'Most watched titles',
    'recent'  => 'Recently added titles',
];

function cv_report_data($con)
{
    $one = function ($sql) use ($con) {
        $r = mysqli_query($con, $sql);
        $row = $r ? mysqli_fetch_row($r) : null;
        return (int) ($row[0] ?? 0);
    };
    $all = function ($sql) use ($con) {
        $r = mysqli_query($con, $sql);
        return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
    };
    $hasTable = function ($name) use ($con) {
        $stmt = $con->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $n = (int) $stmt->get_result()->fetch_row()[0];
        $stmt->close();
        return $n > 0;
    };

    $d = [];
    $d['generated_at'] = date('F j, Y g:i A');

    /* ---- Summary ---- */
    $d['members'] = $one('SELECT COUNT(*) FROM tbl_signup_acc WHERE is_archived = 0');
    $d['members_new_month'] = $one("SELECT COUNT(*) FROM tbl_signup_acc WHERE is_archived = 0 AND date_created >= DATE_FORMAT(CURDATE(), '%Y-%m-01')");
    $d['members_archived'] = $one('SELECT COUNT(*) FROM tbl_signup_acc WHERE is_archived = 1');
    $d['movies'] = $one("SELECT COUNT(*) FROM tbl_movie_series WHERE category = 'Movie'");
    $d['series'] = $one("SELECT COUNT(*) FROM tbl_movie_series WHERE category = 'Series'");
    $d['episodes'] = $one('SELECT COUNT(*) FROM tbl_movie_series_episodes e
        JOIN tbl_movie_series_seasons s ON s.season_id = e.season_id
        JOIN tbl_movie_series m ON m.movie_series_id = s.movie_series_id');
    $d['views'] = $one('SELECT COALESCE(SUM(views), 0) FROM tbl_movie_series');
    $d['watching'] = $hasTable('tbl_watch_progress')
        ? $one('SELECT COUNT(DISTINCT signup_id) FROM tbl_watch_progress WHERE updated_at >= NOW() - INTERVAL 30 DAY')
        : 0;
    $d['titles_archived'] = $hasTable('tbl_movie_series_archived')
        ? $one('SELECT COUNT(*) FROM tbl_movie_series_archived')
        : 0;

    /* ---- Member sign-ups: by day of the week, and per month (last 12) ---- */
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $byDay = array_fill(0, 7, 0);
    foreach ($all('SELECT DAYOFWEEK(date_created) - 1 AS d, COUNT(*) AS n FROM tbl_signup_acc WHERE is_archived = 0 GROUP BY d') as $r) {
        $byDay[(int) $r['d']] = (int) $r['n'];
    }
    $d['signups_by_day'] = array_combine($days, $byDay);

    $months = [];
    for ($i = 11; $i >= 0; $i--) {
        $months[date('Y-m', strtotime("first day of -$i month"))] = 0;
    }
    foreach ($all("SELECT DATE_FORMAT(date_created, '%Y-%m') AS m, COUNT(*) AS n FROM tbl_signup_acc
        WHERE is_archived = 0 AND date_created >= DATE_FORMAT(CURDATE() - INTERVAL 11 MONTH, '%Y-%m-01') GROUP BY m") as $r) {
        if (isset($months[$r['m']])) {
            $months[$r['m']] = (int) $r['n'];
        }
    }
    $d['signups_by_month'] = [];
    foreach ($months as $ym => $n) {
        $d['signups_by_month'][date('M Y', strtotime($ym . '-01'))] = $n;
    }

    /* ---- Genres: titles and views per genre ---- */
    $genres = [];
    foreach ($all('SELECT genre_id, genre_name FROM tbl_movie_series_genre ORDER BY genre_name') as $g) {
        $genres[(int) $g['genre_id']] = ['name' => $g['genre_name'], 'movies' => 0, 'series' => 0, 'movie_views' => 0, 'series_views' => 0];
    }
    foreach ($all('SELECT category, views, genre_id1, genre_id2, genre_id3 FROM tbl_movie_series') as $t) {
        $isMovie = $t['category'] === 'Movie';
        foreach (array_unique(array_filter([$t['genre_id1'], $t['genre_id2'], $t['genre_id3']])) as $gid) {
            if (!isset($genres[(int) $gid])) {
                continue;
            }
            $genres[(int) $gid][$isMovie ? 'movies' : 'series']++;
            $genres[(int) $gid][$isMovie ? 'movie_views' : 'series_views'] += (int) $t['views'];
        }
    }
    // Only genres that have titles, biggest first.
    $d['genres'] = array_values(array_filter($genres, fn($g) => $g['movies'] + $g['series'] > 0));
    usort($d['genres'], fn($a, $b) => ($b['movies'] + $b['series']) <=> ($a['movies'] + $a['series']));
    $byViews = $d['genres'];
    usort($byViews, fn($a, $b) => ($b['movie_views'] + $b['series_views']) <=> ($a['movie_views'] + $a['series_views']));
    $d['genres_by_views'] = $byViews;

    /* ---- Titles by decade (it's a classic-film site) ---- */
    $d['decades'] = $all("SELECT CONCAT(FLOOR(date_released / 10) * 10, 's') AS decade,
            SUM(category = 'Movie') AS movies, SUM(category = 'Series') AS series
        FROM tbl_movie_series WHERE date_released IS NOT NULL
        GROUP BY FLOOR(date_released / 10) ORDER BY FLOOR(date_released / 10)");

    /* ---- Most watched and recently added ---- */
    $d['top'] = $all('SELECT title, category, date_released, views FROM tbl_movie_series
        ORDER BY views DESC, title LIMIT 10');
    $d['recent'] = $all('SELECT title, category, date_released, date_posted FROM tbl_movie_series
        ORDER BY date_posted DESC, movie_series_id DESC LIMIT 10');

    return $d;
}

/* "1 movie" / "3 movies" */
function cv_count($n, $word, $plural = null)
{
    return number_format($n) . ' ' . ($n === 1 ? $word : ($plural ?? $word . 's'));
}
