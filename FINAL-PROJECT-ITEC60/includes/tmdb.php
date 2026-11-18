<?php
/*
 * TMDB (The Movie Database) connection for the admin "Search TMDB" import.
 * TMDB gives details and images (title, year, runtime, rating, genres, cast,
 * overview, posters, title logos, trailers). It does not give the movie files.
 *
 * Needs 'tmdb_api_key' (v3) or 'tmdb_read_token' (v4) in includes/config.php.
 */

const TMDB_API = 'https://api.themoviedb.org/3';
const TMDB_IMG = 'https://image.tmdb.org/t/p/';

class TmdbException extends RuntimeException {}

function tmdb_configured()
{
    return cv_config('tmdb_api_key') || cv_config('tmdb_read_token');
}

function tmdb_get($path, array $params = [])
{
    if (!tmdb_configured()) {
        throw new TmdbException('TMDB is not set up yet. Add your TMDB API key to includes/config.php (tmdb_api_key).');
    }

    $params += ['language' => cv_config('tmdb_language', 'en-US')];
    $headers = ['Accept: application/json'];
    if (cv_config('tmdb_read_token')) {
        $headers[] = 'Authorization: Bearer ' . cv_config('tmdb_read_token');
    } else {
        $params['api_key'] = cv_config('tmdb_api_key');
    }

    $ch = curl_init(TMDB_API . $path . '?' . http_build_query($params));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        throw new TmdbException('Could not reach TMDB. Check the internet connection. (' . $err . ')');
    }
    $data = json_decode($body, true);
    if ($code === 401) {
        throw new TmdbException('TMDB rejected the API key. Check tmdb_api_key / tmdb_read_token in includes/config.php.');
    }
    if ($code === 404) {
        throw new TmdbException('That title was not found on TMDB.');
    }
    if ($code === 429) {
        throw new TmdbException('Too many TMDB requests at once. Wait a few seconds and try again.');
    }
    if ($code >= 400 || !is_array($data)) {
        throw new TmdbException('TMDB returned an error (' . $code . ').');
    }
    return $data;
}

function tmdb_image($filePath, $size = 'w500')
{
    return $filePath ? TMDB_IMG . $size . $filePath : null;
}

/* ---------- Search (movies and TV together) ---------- */
function tmdb_search($query)
{
    $data = tmdb_get('/search/multi', ['query' => $query, 'include_adult' => 'false', 'page' => 1]);
    $out = [];
    foreach ($data['results'] ?? [] as $r) {
        $type = $r['media_type'] ?? '';
        if ($type !== 'movie' && $type !== 'tv') {
            continue; // skip people
        }
        $date = $type === 'movie' ? ($r['release_date'] ?? '') : ($r['first_air_date'] ?? '');
        $out[] = [
            'id'       => (int) $r['id'],
            'type'     => $type,
            'title'    => $type === 'movie' ? ($r['title'] ?? '') : ($r['name'] ?? ''),
            'year'     => $date ? (int) substr($date, 0, 4) : null,
            'overview' => $r['overview'] ?? '',
            'poster'   => tmdb_image($r['poster_path'] ?? null, 'w185'),
        ];
        if (count($out) >= 12) {
            break;
        }
    }
    return $out;
}

/* ---------- Full details, shaped for the CineVault form ---------- */
function tmdb_details($con, $type, $id)
{
    $type = $type === 'tv' ? 'tv' : 'movie';
    $append = $type === 'movie' ? 'credits,release_dates,videos,images,alternative_titles' : 'credits,content_ratings,videos,images,alternative_titles';
    $d = tmdb_get("/$type/" . (int) $id, [
        'append_to_response'     => $append,
        'include_image_language' => 'en,null',
    ]);

    $isMovie = $type === 'movie';
    $date = $isMovie ? ($d['release_date'] ?? '') : ($d['first_air_date'] ?? '');

    // Duration: "1h 49min" for movies, "3 Seasons" for series.
    if ($isMovie) {
        $mins = (int) ($d['runtime'] ?? 0);
        $duration = $mins ? (intdiv($mins, 60) ? intdiv($mins, 60) . 'h ' : '') . ($mins % 60) . 'min' : '';
    } else {
        $n = (int) ($d['number_of_seasons'] ?? 0);
        $duration = $n ? $n . ' Season' . ($n === 1 ? '' : 's') : '';
    }

    // Top 3 cast.
    $cast = array_slice(array_map(fn($c) => $c['name'], $d['credits']['cast'] ?? []), 0, 3);

    // Title logo (transparent PNG) for the "modal poster title" image.
    $logo = null;
    foreach ($d['images']['logos'] ?? [] as $l) {
        if (substr($l['file_path'] ?? '', -4) === '.png') {
            $logo = $l['file_path'];
            break;
        }
    }

    // First YouTube trailer.
    $trailer = null;
    foreach ($d['videos']['results'] ?? [] as $v) {
        if (($v['site'] ?? '') === 'YouTube' && in_array($v['type'] ?? '', ['Trailer', 'Teaser'], true)) {
            $trailer = 'https://www.youtube.com/watch?v=' . $v['key'];
            break;
        }
    }

    // Already in the library?
    $stmt = $con->prepare('SELECT movie_series_id, title FROM tbl_movie_series WHERE tmdb_type = ? AND tmdb_id = ? LIMIT 1');
    $tid = (int) $id;
    $stmt->bind_param('si', $type, $tid);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();

    return [
        'tmdb_id'     => (int) $d['id'],
        'tmdb_type'   => $type,
        'title'       => $isMovie ? ($d['title'] ?? '') : ($d['name'] ?? ''),
        'year'        => $date ? (int) substr($date, 0, 4) : null,
        'duration'    => $duration,
        'age_rating'  => tmdb_age_rating($d, $isMovie),
        'category'    => $isMovie ? 'Movie' : 'Series',
        'genres'      => tmdb_match_genres($con, $d['genres'] ?? []),
        'cast'        => implode(', ', $cast),
        'description' => $d['overview'] ?? '',
        'poster_path' => $d['poster_path'] ?? null,
        'poster_url'  => tmdb_image($d['poster_path'] ?? null, 'w342'),
        'logo_path'   => $logo,
        'logo_url'    => tmdb_image($logo, 'w300'),
        'trailer'     => $trailer,
        'backdrop_path' => $d['backdrop_path'] ?? null,
        'runtime'     => $isMovie ? (int) ($d['runtime'] ?? 0) : null,
        'search_titles' => tmdb_search_titles($d, $isMovie),
        'existing'    => $existing,
    ];
}

/* Certification -> CineVault's age rating number (7, 13, 16, 18). */
function tmdb_age_rating(array $d, $isMovie)
{
    $wanted = [strtoupper(cv_config('tmdb_region', 'PH')), 'US'];
    $cert = '';
    foreach ($wanted as $country) {
        if ($isMovie) {
            foreach ($d['release_dates']['results'] ?? [] as $r) {
                if ($r['iso_3166_1'] === $country) {
                    foreach ($r['release_dates'] as $rd) {
                        if (!empty($rd['certification'])) {
                            $cert = $rd['certification'];
                            break 3;
                        }
                    }
                }
            }
        } else {
            foreach ($d['content_ratings']['results'] ?? [] as $r) {
                if ($r['iso_3166_1'] === $country && !empty($r['rating'])) {
                    $cert = $r['rating'];
                    break 2;
                }
            }
        }
    }

    $map = [
        // US
        'G' => 7, 'PG' => 7, 'PG-13' => 13, 'R' => 16, 'NC-17' => 18,
        'TV-Y' => 7, 'TV-Y7' => 7, 'TV-G' => 7, 'TV-PG' => 7, 'TV-14' => 13, 'TV-MA' => 18,
        // Philippines (MTRCB)
        'SPG' => 13, 'R-13' => 13, 'R-16' => 16, 'R-18' => 18,
    ];
    return $map[strtoupper(trim($cert))] ?? null;
}

/* TMDB genres -> this site's genre ids (by name, with a few aliases). */
function tmdb_match_genres($con, array $tmdbGenres)
{
    $local = [];
    $res = mysqli_query($con, 'SELECT genre_id, genre_name FROM tbl_movie_series_genre');
    while ($g = mysqli_fetch_assoc($res)) {
        $local[strtolower(trim($g['genre_name']))] = (int) $g['genre_id'];
    }
    $alias = [
        'horror' => 'terror', 'tv movie' => 'cinema tv',
        'action & adventure' => 'action', 'science fiction' => 'science fiction',
    ];

    $out = [];
    foreach ($tmdbGenres as $g) {
        $name = strtolower(trim($g['name'] ?? ''));
        $key = isset($local[$name]) ? $name : ($alias[$name] ?? null);
        if ($key !== null && isset($local[$key]) && !in_array($local[$key], array_column($out, 'id'), true)) {
            $out[] = ['id' => $local[$key], 'name' => $g['name']];
        }
        if (count($out) === 3) {
            break;
        }
    }
    return $out;
}

/*
 * Downloads a TMDB image into the uploads folder under a random name.
 * Only accepts TMDB image paths (e.g. "/abc123.jpg"), so the server can't be
 * pointed at any other address. Returns the saved file name or null.
 */
function tmdb_save_image($filePath, $size, $targetDir, &$error = null)
{
    if (!is_string($filePath) || !preg_match('#^/[A-Za-z0-9_-]+\.(jpg|jpeg|png)$#', $filePath, $m)) {
        $error = 'Not a TMDB image.';
        return null;
    }
    $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);

    $ch = curl_init(TMDB_IMG . $size . $filePath);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_CONNECTTIMEOUT => 8]);
    $bytes = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($bytes === false || $code !== 200 || strlen($bytes) > 8 * 1024 * 1024) {
        $error = 'Could not download the image from TMDB.';
        return null;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
    if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
        $error = 'TMDB returned something that is not an image.';
        return null;
    }
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    if (file_put_contents(rtrim($targetDir, '/') . '/' . $name, $bytes) === false) {
        $error = 'Could not save the image.';
        return null;
    }
    return $name;
}

/* Names to look for a free copy under: the title, the original title, and
 * other English titles (e.g. "Tell Your Children" is also "Reefer Madness"). */
function tmdb_search_titles(array $d, $isMovie)
{
    $names = [$isMovie ? ($d['title'] ?? '') : ($d['name'] ?? ''), $isMovie ? ($d['original_title'] ?? '') : ($d['original_name'] ?? '')];
    $alts = $d['alternative_titles'][$isMovie ? 'titles' : 'results'] ?? [];
    // Main alternative names first; reissue, colorized and other versions last.
    usort($alts, fn($a, $b) => (($a['type'] ?? '') !== '') <=> (($b['type'] ?? '') !== ''));
    foreach ($alts as $a) {
        if (in_array($a['iso_3166_1'] ?? '', ['US', 'GB', 'CA', 'AU', 'PH'], true)) {
            $names[] = $a['title'] ?? '';
        }
    }
    $out = [];
    foreach ($names as $n) {
        $n = trim((string) $n);
        if ($n !== '' && preg_match('/[a-z]/i', $n) && !in_array(strtolower($n), array_map('strtolower', $out), true)) {
            $out[] = $n;
        }
    }
    return array_slice($out, 0, 5);
}
