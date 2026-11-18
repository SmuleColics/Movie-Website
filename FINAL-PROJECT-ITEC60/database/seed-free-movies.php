<?php
/*
 * Fills CineVault with free, full-length public domain movies and classic
 * TV series from the Internet Archive. Details, posters, title logos and wide
 * backdrops come from TMDB; the videos stream from archive.org (nothing big is
 * downloaded to this server).
 *
 * Run from a terminal (needs the TMDB key in includes/config.php, and
 * database/update-2026-11-classics.sql already imported):
 *   C:\XAMPP-Projects\php\php.exe FINAL-PROJECT-ITEC60\database\seed-free-movies.php
 *
 * Safe to run again: titles already in the library are skipped.
 * A movie is only added when the archive.org copy is at least 80% of TMDB's
 * runtime, so trailers and clips are never added as the full film.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$_SERVER['REQUEST_URI'] = '/cli';
require __DIR__ . '/../includes/db-connection.php';
require __DIR__ . '/../includes/tmdb.php';
require __DIR__ . '/../includes/archive.php';

const POSTER_DIR = __DIR__ . '/../DASHBOARD-HTML/MOVIE_SERIES_TITLE';

/* ---------- Movies ----------
 * [title, year] or [title, year, ['ia' => archive item, 'tmdb' => id, 'q' => archive search]].
 * All public domain in the US, or openly licensed (Blender films: CC BY). */
$movies = [
    // Horror
    ['Night of the Living Dead', 1968, ['ia' => 'night_of_the_living_dead_dvd', 'tmdb' => 10331]],
    ['Nosferatu', 1922, ['ia' => 'Nosferatu_most_complete_version_93_mins.', 'tmdb' => 653]],
    ['The Phantom of the Opera', 1925, ['ia' => 'ThePhantomoftheOpera', 'tmdb' => 964]],
    ['House on Haunted Hill', 1959, ['ia' => 'house_on_haunted_hill_ipod', 'tmdb' => 15856]],
    ['Carnival of Souls', 1962, ['ia' => 'CarnivalofSouls', 'tmdb' => 16093]],
    ['The Little Shop of Horrors', 1960, ['ia' => 'TheLittleShopOfHorrors1960_765', 'tmdb' => 24452]],
    ['The Last Man on Earth', 1964],
    ['The Terror', 1963],
    ['The Bat', 1959],
    ['White Zombie', 1932],
    ['The Brain That Wouldn\'t Die', 1962],
    ['Dementia 13', 1963],
    ['A Bucket of Blood', 1959],
    ['Häxan', 1922, ['q' => 'Haxan']],
    ['The Killer Shrews', 1959],
    ['Attack of the Giant Leeches', 1959],
    ['The Screaming Skull', 1958],
    ['The Ape Man', 1943],
    ['Invisible Ghost', 1941],
    ['The Corpse Vanishes', 1942],
    ['Bluebeard', 1944],
    // Drama / thriller / noir / crime
    ['The Cabinet of Dr. Caligari', 1920, ['ia' => 'DasKabinettdesDoktorCaligariTheCabinetofDrCaligari', 'tmdb' => 234]],
    ['Suddenly', 1954, ['ia' => 'suddenly', 'tmdb' => 18398]],
    ['The Stranger', 1946, ['ia' => 'TheStranger_0', 'tmdb' => 20246]],
    ['Detour', 1945, ['ia' => 'Detour', 'tmdb' => 20367]],
    ['D.O.A.', 1950, ['q' => 'D.O.A. 1950']],
    ['Scarlet Street', 1945],
    ['The Hitch-Hiker', 1953],
    ['Kansas City Confidential', 1952],
    ['Quicksand', 1950],
    ['The Second Woman', 1950],
    ['Fear in the Night', 1947],
    ['Too Late for Tears', 1949],
    ['Woman on the Run', 1950],
    ['The Big Combo', 1955],
    ['Strange Illusion', 1945],
    ['The Man Who Knew Too Much', 1934],
    ['The 39 Steps', 1935],
    ['The Lady Vanishes', 1938],
    ['Sabotage', 1936],
    ['The Most Dangerous Game', 1932],
    ['Beat the Devil', 1953],
    ['Meet John Doe', 1941],
    ['Penny Serenade', 1941],
    ['A Star Is Born', 1937],
    ['Of Human Bondage', 1934],
    ['Jane Eyre', 1934],
    ['Life with Father', 1947],
    ['Algiers', 1938],
    ['Rain', 1932],
    ['Angel on My Shoulder', 1946],
    ['The Southerner', 1945],
    ['Salt of the Earth', 1954],
    ['Sita Sings the Blues', 2008, ['ia' => 'Sita_Sings_the_Blues', 'tmdb' => 20529]],
    // Comedy / romance
    ['His Girl Friday', 1940, ['ia' => 'his_girl_friday', 'tmdb' => 3085]],
    ['Charade', 1963],
    ['My Man Godfrey', 1936],
    ['Nothing Sacred', 1937],
    ['Made for Each Other', 1939],
    ['Love Affair', 1939],
    ['Royal Wedding', 1951],
    ['Topper Returns', 1941],
    ['Second Chorus', 1940],
    ['The Inspector General', 1949],
    ['Road to Bali', 1952],
    ['Africa Screams', 1949],
    ['Jack and the Beanstalk', 1952],
    ['The Flying Deuces', 1939],
    ['Babes in Toyland', 1934],
    ['Till the Clouds Roll By', 1946],
    ['Bird of Paradise', 1932],
    ['The Kid', 1921],
    ['The Gold Rush', 1925],
    ['Safety Last!', 1923],
    ['Sherlock Jr.', 1924],
    ['The Navigator', 1924],
    ['Steamboat Bill, Jr.', 1928],
    ['The General', 1926, ['ia' => 'TheGeneral', 'tmdb' => 961]],
    // Action / adventure / western
    ['McLintock!', 1963, ['ia' => 'mclintok_widescreen', 'tmdb' => 15263]],
    ['The Lost World', 1925, ['ia' => 'lost_world', 'tmdb' => 2981]],
    ['The Thief of Bagdad', 1924],
    ['Robin Hood', 1922],
    ['The Mark of Zorro', 1920],
    ['The Black Pirate', 1926],
    ['The Three Musketeers', 1921],
    ['Jungle Book', 1942],
    ['Tarzan\'s Revenge', 1938],
    ['Captain Kidd', 1945],
    ['The Fast and the Furious', 1954],
    ['Hercules', 1958],
    ['Hercules Unchained', 1959],
    ['Angel and the Badman', 1947],
    ['Kansas Pacific', 1953],
    ['Randy Rides Alone', 1934],
    ['The Star Packer', 1934],
    ['Blue Steel', 1934],
    ['Riders of Destiny', 1933],
    ['Sagebrush Trail', 1933],
    ['The Lawless Frontier', 1934],
    ['\'Neath the Arizona Skies', 1934],
    ['The Man from Utah', 1934],
    ['Rainbow Valley', 1935],
    ['The Dawn Rider', 1935],
    ['Paradise Canyon', 1935],
    ['The Desert Trail', 1935],
    ['West of the Divide', 1934],
    // Science fiction
    ['Plan 9 from Outer Space', 1957, ['ia' => 'plan-9-from-outer-space', 'tmdb' => 10513]],
    ['Metropolis', 1927],
    ['A Trip to the Moon', 1902],
    ['Robot Monster', 1953],
    ['Killers from Space', 1954],
    ['Teenagers from Outer Space', 1959],
    ['Rocketship X-M', 1950],
    ['Phantom from Space', 1953],
    ['Santa Claus Conquers the Martians', 1964],
    ['Voyage to the Prehistoric Planet', 1965],
    ['The Amazing Transparent Man', 1960],
    ['Beyond the Time Barrier', 1960],
    // Animation / family
    ['Gulliver\'s Travels', 1939, ['ia' => 'gullivers_travels1939', 'tmdb' => 42518]],
    ['Mr. Bug Goes to Town', 1941],
    ['The Little Princess', 1939],
    ['The Adventures of Prince Achmed', 1926],
    ['Popeye the Sailor Meets Sindbad the Sailor', 1936],
    ['Popeye the Sailor Meets Ali Baba\'s Forty Thieves', 1937],
    ['Aladdin and His Wonderful Lamp', 1939],
    ['Big Buck Bunny', 2008, ['ia' => 'BigBuckBunny_124', 'tmdb' => 10378]],
    ['Sintel', 2010, ['ia' => 'Sintel', 'tmdb' => 45745]],
    ['Tears of Steel', 2012, ['ia' => 'Tears-of-Steel', 'tmdb' => 133701]],
    ['Elephants Dream', 2006],
    ['Cosmos Laundromat', 2015],
    ['Spring', 2019, ['q' => 'Spring Blender open movie']],
    ['Agent 327: Operation Barbershop', 2017, ['q' => 'Agent 327']],
    ['Sprite Fright', 2021],
    ['Charge', 2022, ['q' => 'Charge Blender']],
];

/* ---------- Series ----------
 * Classic TV shows whose episodes are in the public domain. Each takes the
 * most-watched episodes on archive.org (max 'max'). */
$series = [
    // Comedy
    ['The Beverly Hillbillies', 1962, ['items' => [
        'thebeverlyhillbilliestheclampettsstrikeoilhq', 'thebeverlyhillbilliesgettingsettledhq',
        'thebeverlyhillbilliesmeanwhilebackatthecabinhq', 'thebeverlyhillbilliestheclampettsmeetmrsdrysdalehq',
        'Beverly_Hillbillies_Ep06_Trick_Or_Treat', 'Beverly_Hillbillies_Ep07_The_Servants',
        'Beverly_Hillbillies_Ep13_Home_For_Christmas']]],
    ['The Dick Van Dyke Show', 1961, ['items' => [
        'dick_van_dyke_show_31_never_name_a_duck', 'dick_van_dyke_show_34_bank_book_6565696',
        'dick_van_dyke_show_39_the_night_the_roof_fell_in']]],
    ['The Lucy Show', 1962],
    ['The Andy Griffith Show', 1960],
    ['The Jack Benny Program', 1950],
    ['The George Burns and Gracie Allen Show', 1950, ['q' => 'Burns and Allen']],
    ['The Adventures of Ozzie and Harriet', 1952, ['q' => 'Ozzie and Harriet']],
    ['Petticoat Junction', 1963],
    ['The Life of Riley', 1953],
    ['My Little Margie', 1952],
    ['The Abbott and Costello Show', 1952],
    ['I Married Joan', 1952],
    ['The Bob Cummings Show', 1955, ['q' => 'Love That Bob']],
    ['Make Room for Daddy', 1953],
    // Westerns
    ['Bonanza', 1959, ['items' => ['Bonanza_pd'], 'max' => 12]],
    ['The Cisco Kid', 1950],
    ['The Lone Ranger', 1949],
    ['Annie Oakley', 1954],
    ['Hopalong Cassidy', 1952],
    ['The Roy Rogers Show', 1951],
    ['The Adventures of Wild Bill Hickok', 1951, ['q' => 'Wild Bill Hickok']],
    ['The Range Rider', 1951],
    ['The Gene Autry Show', 1950],
    ['Stories of the Century', 1954],
    ['The Rifleman', 1958],
    // Sci-fi & fantasy
    ['Flash Gordon', 1954],
    ['Rocky Jones, Space Ranger', 1954, ['q' => 'Rocky Jones']],
    ['Tom Corbett, Space Cadet', 1950, ['q' => 'Tom Corbett']],
    ['Space Patrol', 1950],
    ['Captain Z-Ro', 1951],
    ['Commando Cody: Sky Marshal of the Universe', 1955, ['q' => 'Commando Cody']],
    ['One Step Beyond', 1959],
    ['Science Fiction Theatre', 1955],
    ['Tales of Tomorrow', 1951],
    ['Men into Space', 1959],
    ['The Veil', 1958],
    // Cartoons
    ['Max Fleischer\'s Superman', 1941, ['tmdb_movie' => 342605, 'items' => [
        'superman_1941', 'superman_the_mechanical_monsters', 'superman_the_mad_scientist',
        'superman_electric_earthquake', 'superman_eleventh_hour']]],
    ['Popeye the Sailor', 1933],
    ['Casper the Friendly Ghost', 1945, ['q' => 'Casper']],
    ['Betty Boop', 1930],
    ['Felix the Cat', 1919],
    ['Little Lulu', 1943],
    ['Herman and Katnip', 1950],
    ['Color Classics', 1934, ['q' => 'Color Classic']],
    ['Noveltoons', 1943],
    ['Tom and Jerry', 1931, ['q' => 'Van Beuren Tom and Jerry']],
    ['Merrie Melodies', 1931],
    ['Looney Tunes', 1930],
    ['Mighty Mouse', 1942, ['q' => 'Mighty Mouse']],
    ['Woody Woodpecker', 1941, ['q' => 'Woody Woodpecker']],
    ['Little Audrey', 1947],
    ['Heckle and Jeckle', 1946],
    ['Raggedy Ann', 1941, ['q' => 'Raggedy Ann']],
    ['Scrappy', 1931, ['q' => 'Scrappy cartoon']],
    ['Flip the Frog', 1930],
    // More westerns
    ['Death Valley Days', 1952],
    ['Tales of the Texas Rangers', 1955],
    ['The Adventures of Kit Carson', 1951, ['q' => 'Kit Carson']],
    ['Judge Roy Bean', 1956],
    ['Buffalo Bill, Jr.', 1955, ['q' => 'Buffalo Bill Jr']],
    ['Frontier Doctor', 1958],
    ['26 Men', 1957],
    ['The Restless Gun', 1957],
    // More sci-fi, fantasy and mystery
    ['Captain Video and His Video Rangers', 1949, ['q' => 'Captain Video']],
    ['Rod Brown of the Rocket Rangers', 1953, ['q' => 'Rod Brown']],
    ['Atom Squad', 1953],
    ['Out There', 1951],
    ['Inner Sanctum', 1954],
    ['Lights Out', 1949],
    ['Sherlock Holmes', 1954, ['q' => 'Sherlock Holmes 1954']],
    ['Topper', 1953],
    ['The Adventures of Sir Lancelot', 1956, ['q' => 'Sir Lancelot']],
];

/* ---------- Classic cartoon series ----------
 * TMDB has these theatrical cartoons only as single shorts, not as series, so
 * each series is built here: the shorts come from archive.org's public domain
 * cartoon collections, and the poster comes from one well-known short.
 * [series title, first year, archive title words, poster short [title, year], description] */
$cartoons = [
    ['Popeye the Sailor', 1933, 'popeye', ['Popeye the Sailor', 1933],
        'The spinach-powered sailor man takes on Bluto, rescues Olive Oyl and gets into trouble with Swee\'Pea and Wimpy in these classic theatrical shorts.'],
    ['Betty Boop', 1930, 'betty boop', ['Snow-White', 1933],
        'The Jazz Age flapper Betty Boop, with Bimbo and Koko the Clown, in surreal, music-filled Fleischer Studios cartoons.'],
    ['Felix the Cat', 1919, 'felix', ['Feline Follies', 1919],
        'The first great cartoon star: a clever black cat whose silent-era adventures twist reality any way he likes.'],
    ['Bugs Bunny and Friends', 1941, 'bugs bunny', ['Falling Hare', 1943],
        'Bugs Bunny, Daffy Duck and Elmer Fudd in public domain Warner Bros. cartoons from the 1940s.'],
    ['Porky Pig', 1935, 'porky', ['Porky in Wackyland', 1938],
        'The stuttering pig from Warner Bros. in early Looney Tunes adventures, from Wackyland to wartime spy chases.'],
    ['Little Lulu', 1943, 'little lulu', ['Eggs Don\'t Bounce', 1943],
        'The mischievous girl from the comic strip gets into trouble at school, at the store and on the golf course.'],
    ['Little Audrey', 1947, 'little audrey', ['Santa\'s Surprise', 1947],
        'Famous Studios\' cheerful, dreamy little girl and her imaginative adventures.'],
    ['Gabby', 1940, 'gabby', ['King for a Day', 1940],
        'The busybody town crier from Fleischer\'s Gulliver\'s Travels stars in his own Lilliput adventures.'],
    ['Flip the Frog', 1930, 'flip the frog', ['Fiddlesticks', 1930],
        'Ub Iwerks\' cheerful frog in some of the first sound and color cartoons, full of music and gags.'],
    ['Tom and Jerry (Van Beuren)', 1931, 'tom and jerry', ['Wot a Night', 1931],
        'Not the cat and mouse: the original Tom and Jerry were a tall and short human duo in zany Van Beuren Studios cartoons.'],
    ['Heckle and Jeckle', 1946, 'heckle', ['The Talking Magpies', 1946],
        'Two wisecracking magpies from Terrytoons who outwit everyone they meet.'],
];

function cartoon_shorts($words, $max)
{
    $data = archive_get('https://archive.org/advancedsearch.php?' . http_build_query([
        'q' => 'title:(' . $words . ') AND mediatype:movies AND collection:(classic_cartoons OR animationandcartoons)',
        'fl' => ['identifier', 'title', 'year'],
        'sort' => ['downloads desc'],
        'rows' => 40,
        'output' => 'json',
    ]));
    $eps = [];
    $seen = [];
    foreach ($data['response']['docs'] ?? [] as $doc) {
        if (count($eps) >= $max) {
            break;
        }
        $t = is_array($doc['title']) ? $doc['title'][0] : (string) $doc['title'];
        // Skip compilations, theater shows, VHS/DVD rips and remasters.
        if (preg_match('/theat(er|re)|cartoons\b|collection|compil|vhs|dvd|colori[sz]ed|trailer|commercial|dub\b|\bep\.? ?\d/i', $t)) {
            continue;
        }
        $clean = trim(preg_replace(['/\(.*?\)|\[.*?\]/', '/^.*?(:| - |\bin\b)\s*/i', '/\s+/'], ['', '', ' '], $t), " \t\"'-");
        $key = norm($clean);
        if ($clean === '' || isset($seen[$key])) {
            continue;
        }
        try {
            $v = archive_videos($doc['identifier']);
        } catch (ArchiveException $e) {
            continue;
        }
        foreach ($v['videos'] as $f) {
            $mins = $f['minutes'] ?? 0;
            if ($f['rank'] >= 2 && $mins >= 4 && $mins <= 25) {
                $seen[$key] = true;
                $year = isset($doc['year']) ? (int) (is_array($doc['year']) ? $doc['year'][0] : $doc['year']) : 0;
                $eps[] = ['title' => $clean, 'year' => $year, 'url' => $f['url'], 'minutes' => $mins];
                break;
            }
        }
    }
    usort($eps, fn($a, $b) => [$a['year'] ?: 9999, $a['title']] <=> [$b['year'] ?: 9999, $b['title']]);
    return $eps;
}

foreach ($cartoons as [$title, $year, $words, $posterShort, $desc]) {
    try {
        $shortId = tmdb_find('movie', $posterShort[0], $posterShort[1]);
        if (!$shortId) {
            say("skip  (poster short not on TMDB) cartoons $title");
            continue;
        }
        if (already_added($con, 'short', $shortId)) {
            continue;
        }
        $short = tmdb_get('/movie/' . $shortId);
        if (empty($short['poster_path'])) {
            say("skip  (no poster) cartoons $title");
            continue;
        }
        $eps = cartoon_shorts($words, 8);
        if (count($eps) < 3) {
            say("skip  (fewer than 3 shorts found) cartoons $title");
            continue;
        }
        $d = [
            'tmdb_id' => $shortId, 'tmdb_type' => 'short', 'title' => $title, 'year' => $year,
            'genres' => [['id' => 3], ['id' => 8], ['id' => 4]], 'age_rating' => 7,
            'cast' => 'Various', 'description' => $desc,
            'poster_path' => $short['poster_path'], 'logo_path' => null, 'backdrop_path' => $short['backdrop_path'] ?? null,
        ];
        $con->begin_transaction();
        $msId = insert_title($con, $d, 'Series', count($eps) . ' Episodes', $eps[0]['url'], $title);
        if (!$msId) {
            $con->rollback();
            continue;
        }
        $n = 1;
        $st = 'Season 1';
        $stmt = $con->prepare('INSERT INTO tbl_movie_series_seasons (movie_series_id, season_number, season_title) VALUES (?, ?, ?)');
        $stmt->bind_param('iis', $msId, $n, $st);
        $stmt->execute();
        $seasonId = $stmt->insert_id;
        $stmt->close();
        foreach ($eps as $ep) {
            $dur = $ep['minutes'] . 'min';
            $edesc = $title . ': ' . $ep['title'] . ($ep['year'] ? ' (' . $ep['year'] . ')' : '') . '.';
            $stmt = $con->prepare('INSERT INTO tbl_movie_series_episodes (season_id, episode_title, episode_description, episode_duration, episode_video) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('issss', $seasonId, $ep['title'], $edesc, $dur, $ep['url']);
            $stmt->execute();
            $stmt->close();
        }
        $con->commit();
        say("added #$msId  $title: " . count($eps) . ' shorts');
        $added++;
    } catch (Exception $e) {
        @$con->rollback();
        say("error cartoons $title: " . $e->getMessage());
    }
}

/* ---------- Featured spots (carousels and banners) ---------- */
$features = [
    'home-1' => 'Night of the Living Dead', 'home-2' => 'His Girl Friday', 'home-3' => 'Metropolis',
    'movies-1' => 'The General', 'movies-2' => 'Nosferatu', 'movies-3' => 'The Phantom of the Opera',
    'series-1' => 'Bonanza', 'series-2' => 'The Beverly Hillbillies', 'series-3' => 'The Dick Van Dyke Show',
    'banner1' => 'Charade', 'banner2' => 'The Thief of Bagdad',
    'banner3' => 'Carnival of Souls', 'banner4' => 'Detour',
    'banner5' => 'The Lucy Show', 'banner6' => 'The Andy Griffith Show',
];

/* ---------- Helpers ---------- */
function say($msg) { echo $msg, PHP_EOL; }

function norm($s)
{
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string) $s) ?: (string) $s;
    return preg_replace('/[^a-z0-9]+/', '', strtolower($s));
}

function already_added($con, $type, $id)
{
    $stmt = $con->prepare('SELECT movie_series_id FROM tbl_movie_series WHERE tmdb_type = ? AND tmdb_id = ?');
    $stmt->bind_param('si', $type, $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ? (int) $row['movie_series_id'] : 0;
}

/* Finds a TMDB id by title and year (within a year either side). */
function tmdb_find($type, $title, $year)
{
    $dateKey = $type === 'movie' ? 'release_date' : 'first_air_date';
    $nameKey = $type === 'movie' ? 'title' : 'name';
    $res = tmdb_get("/search/$type", ['query' => $title, 'include_adult' => 'false']);
    $best = null;
    foreach ($res['results'] ?? [] as $r) {
        $y = (int) substr($r[$dateKey] ?? '', 0, 4);
        if ($year && $y && abs($y - $year) > ($type === 'tv' ? 4 : 1)) {
            continue;
        }
        $score = (norm($r[$nameKey] ?? '') === norm($title) ? 10 : 0) + (($r['original_title'] ?? $r['original_name'] ?? '') && norm($r['original_title'] ?? $r['original_name']) === norm($title) ? 5 : 0) + min(5, ($r['vote_count'] ?? 0) / 50);
        if (!$best || $score > $best[0]) {
            $best = [$score, (int) $r['id']];
        }
    }
    return $best ? $best[1] : null;
}

/* Archive.org items for a title, best first (title words must all appear). */
function archive_candidates($query, $mustContain)
{
    $want = array_filter(explode(' ', strtolower(preg_replace('/[^a-z0-9 ]+/i', ' ', $mustContain))), fn($w) => strlen($w) > 2 && !in_array($w, ['the', 'and', 'from'], true));
    $out = [];
    foreach (archive_search($query) as $r) {
        $t = strtolower(preg_replace('/[^a-z0-9 ]+/i', ' ', $r['title']));
        $ok = true;
        foreach ($want as $w) {
            if (!str_contains($t, $w)) {
                $ok = false;
                break;
            }
        }
        if ($ok) {
            $out[] = $r;
        }
    }
    return $out;
}

function save_img($path, $size)
{
    return $path ? tmdb_save_image($path, $size, POSTER_DIR) : null;
}

function insert_title($con, array $d, $category, $duration, $video, $title = null)
{
    $poster = save_img($d['poster_path'], 'w500');
    if (!$poster) {
        return 0;
    }
    $logo = save_img($d['logo_path'], 'w500');
    $backdrop = save_img($d['backdrop_path'] ?? null, 'w1280');

    $g = array_column($d['genres'], 'id');
    $g1 = $g[0] ?? 7; // Drama when TMDB's genres don't match this site's list
    $g2 = $g[1] ?? null;
    $g3 = $g[2] ?? null;
    $age = $d['age_rating'] ?? (array_intersect([3, 8, 27], $g) ? 7 : 13);
    $title = $title ?? $d['title'];
    $year = $d['year'];
    $cast = $d['cast'] !== '' ? $d['cast'] : 'Various';
    $desc = $d['description'] !== '' ? $d['description'] : $title;
    $tmdbId = $d['tmdb_id'];
    $tmdbType = $d['tmdb_type'];

    $stmt = $con->prepare('INSERT INTO tbl_movie_series
        (tmdb_id, tmdb_type, title, duration, poster, video, modal_poster_title, backdrop, date_released, age_rating, category, genre_id1, genre_id2, genre_id3, cast, description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssssssiisiiiss', $tmdbId, $tmdbType, $title, $duration, $poster, $video, $logo, $backdrop, $year, $age, $category, $g1, $g2, $g3, $cast, $desc);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

/* All TMDB episodes of a show, keyed by "season-episode", plus a name index. */
function tmdb_episodes($tvId, $seasonCount)
{
    $byNum = $byName = [];
    for ($n = 1; $n <= min($seasonCount, 12); $n++) {
        try {
            $s = tmdb_get("/tv/$tvId/season/$n");
        } catch (TmdbException $e) {
            continue;
        }
        foreach ($s['episodes'] ?? [] as $ep) {
            $row = ['season' => (int) $ep['season_number'], 'episode' => (int) $ep['episode_number'], 'name' => $ep['name'], 'overview' => $ep['overview'] ?? ''];
            $byNum[$row['season'] . '-' . $row['episode']] = $row;
            $byName[norm($ep['name'])] = $row;
        }
    }
    return [$byNum, $byName];
}

$added = 0;

/* ---------- Movies ---------- */
foreach ($movies as $m) {
    [$title, $year] = $m;
    $opt = $m[2] ?? [];
    try {
        $tmdbId = $opt['tmdb'] ?? tmdb_find('movie', $title, $year);
        if (!$tmdbId) {
            say("skip  (not on TMDB) $title ($year)");
            continue;
        }
        if (already_added($con, 'movie', $tmdbId)) {
            continue;
        }
        $d = tmdb_details($con, 'movie', $tmdbId);
        $runtime = $d['runtime'] ?: 0;

        // Find a full-length, browser-playable copy.
        $items = isset($opt['ia']) ? [['id' => $opt['ia']]] : archive_candidates($opt['q'] ?? $title, $opt['q'] ?? $title);
        $pick = null;
        foreach (array_slice($items, 0, 6) as $it) {
            try {
                $v = archive_videos($it['id']);
            } catch (ArchiveException $e) {
                continue;
            }
            foreach ($v['videos'] as $f) {
                if ($f['rank'] < 2) {
                    continue; // "MPEG4" originals may not play in browsers
                }
                $mins = $f['minutes'] ?? 0;
                if ($runtime && $mins && $mins < $runtime * 0.8) {
                    continue; // a clip or trailer, not the full film
                }
                if (!$mins && $runtime > 20) {
                    continue; // unknown length: don't risk it
                }
                $pick = $f + ['item' => $it['id']];
                break 2;
            }
        }
        if (!$pick) {
            say("skip  (no full copy found) {$d['title']} ($year)");
            continue;
        }
        $duration = $d['duration'] ?: ($pick['minutes'] . 'min');
        $id = insert_title($con, $d, 'Movie', $duration, $pick['url']);
        if ($id) {
            say("added #$id  {$d['title']} ({$d['year']})  <- {$pick['item']} [{$pick['minutes']} of {$runtime} min]");
            $added++;
        } else {
            say("skip  (no poster) {$d['title']}");
        }
    } catch (Exception $e) {
        say("error $title: " . $e->getMessage());
    }
}

/* ---------- Series ---------- */
foreach ($series as $s) {
    [$title, $year] = $s;
    $opt = $s[2] ?? [];
    try {
        if (isset($opt['tmdb_movie'])) {
            $type = 'movie';
            $tmdbId = $opt['tmdb_movie'];
        } else {
            $type = 'tv';
            $tmdbId = tmdb_find('tv', $title, $year);
        }
        if (!$tmdbId) {
            say("skip  (not on TMDB) series $title");
            continue;
        }
        if (already_added($con, $type, $tmdbId)) {
            continue;
        }
        $d = tmdb_details($con, $type, $tmdbId);
        $max = $opt['max'] ?? 6;

        // Episode videos: either listed items or the most-watched matches.
        $itemIds = $opt['items'] ?? array_column(array_slice(archive_candidates($opt['q'] ?? $title, $opt['q'] ?? $title), 0, 12), 'id');
        $eps = [];
        $seen = [];
        foreach ($itemIds as $item) {
            if (count($eps) >= $max) {
                break;
            }
            try {
                $v = archive_videos($item);
            } catch (ArchiveException $e) {
                continue;
            }
            $files = array_values(array_filter($v['videos'], fn($f) => $f['rank'] >= 2 && ($f['minutes'] ?? 0) >= 5));
            if (!$files) {
                continue;
            }
            // A big item holds a whole season (one file each); otherwise one episode.
            $names = array_unique(array_map(fn($f) => preg_replace('/(_512kb|\.ia)?\.mp4$/i', '', $f['name']), $files));
            $pack = count($names) >= 3;
            foreach ($pack ? $files : [$files[0]] as $f) {
                $key = preg_replace('/(_512kb|\.ia)?\.mp4$/i', '', $f['name']);
                if (isset($seen[$key]) || count($eps) >= $max) {
                    continue;
                }
                $seen[$key] = true;
                $label = $pack ? pathinfo($f['name'], PATHINFO_FILENAME) : $v['title'];
                $eps[] = ['label' => $label, 'url' => $f['url'], 'minutes' => $f['minutes']];
            }
        }
        if (count($eps) < 2) {
            say("skip  (fewer than 2 episodes found) series $title");
            continue;
        }

        [$byNum, $byName] = $type === 'tv' ? tmdb_episodes($tmdbId, (int) preg_replace('/\D/', '', $d['duration']) ?: 3) : [[], []];
        foreach ($eps as $i => &$ep) {
            $match = null;
            if (preg_match('/s\s*0*(\d+)\s*e\s*0*(\d+)/i', $ep['label'], $mm) || preg_match('/\b(\d)x(\d{1,2})\b/', $ep['label'], $mm)) {
                $match = $byNum[(int) $mm[1] . '-' . (int) $mm[2]] ?? null;
            }
            $clean = preg_replace('/^.*?(#\d+:|:| - )\s*/', '', $ep['label']);
            $clean = trim(preg_replace('/\s*(S\d+\s*E\d+|\(?\d{4}\)?|HQ)\s*$/i', '', $clean));
            if (!$match) {
                $match = $byName[norm($clean)] ?? null;
            }
            if (!$match) {
                foreach ($byName as $key => $row) {
                    if (strlen($key) > 5 && str_contains(norm($ep['label']), $key)) {
                        $match = $row;
                        break;
                    }
                }
            }
            $ep['title'] = $match['name'] ?? ($clean !== '' && norm($clean) !== norm($title) ? $clean : 'Episode ' . ($i + 1));
            $ep['season'] = $match['season'] ?? 1;
            $ep['episode'] = $match['episode'] ?? (100 + $i);
            $ep['overview'] = $match['overview'] ?? '';
        }
        unset($ep);
        usort($eps, fn($a, $b) => [$a['season'], $a['episode']] <=> [$b['season'], $b['episode']]);

        $seasonNums = array_values(array_unique(array_column($eps, 'season')));
        $duration = count($seasonNums) > 1 ? count($seasonNums) . ' Seasons' : count($eps) . ' Episodes';

        $con->begin_transaction();
        $msId = insert_title($con, $d, 'Series', $duration, $eps[0]['url'], $title);
        if (!$msId) {
            $con->rollback();
            say("skip  (no poster) series $title");
            continue;
        }
        $seasonIds = [];
        foreach ($seasonNums as $n) {
            $st = 'Season ' . $n;
            $stmt = $con->prepare('INSERT INTO tbl_movie_series_seasons (movie_series_id, season_number, season_title) VALUES (?, ?, ?)');
            $stmt->bind_param('iis', $msId, $n, $st);
            $stmt->execute();
            $seasonIds[$n] = $stmt->insert_id;
            $stmt->close();
        }
        foreach ($eps as $ep) {
            $dur = $ep['minutes'] ? $ep['minutes'] . 'min' : '';
            $desc = $ep['overview'] !== '' ? $ep['overview'] : $title . ': ' . $ep['title'];
            $stmt = $con->prepare('INSERT INTO tbl_movie_series_episodes (season_id, episode_title, episode_description, episode_duration, episode_video) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('issss', $seasonIds[$ep['season']], $ep['title'], $desc, $dur, $ep['url']);
            $stmt->execute();
            $stmt->close();
        }
        $con->commit();
        say("added #$msId  $title: " . count($eps) . ' episodes');
        $added++;
    } catch (Exception $e) {
        @$con->rollback();
        say("error series $title: " . $e->getMessage());
    }
}

/* ---------- Backdrops for titles added before backdrops existed ---------- */
$res = mysqli_query($con, 'SELECT movie_series_id, tmdb_id, tmdb_type FROM tbl_movie_series WHERE backdrop IS NULL AND tmdb_id IS NOT NULL');
while ($r = mysqli_fetch_assoc($res)) {
    try {
        $d = tmdb_get('/' . ($r['tmdb_type'] === 'tv' ? 'tv' : 'movie') . '/' . (int) $r['tmdb_id']);
        $b = save_img($d['backdrop_path'] ?? null, 'w1280');
        if ($b) {
            $stmt = $con->prepare('UPDATE tbl_movie_series SET backdrop = ? WHERE movie_series_id = ?');
            $stmt->bind_param('si', $b, $r['movie_series_id']);
            $stmt->execute();
            $stmt->close();
        }
    } catch (Exception $e) {
    }
}

/* ---------- Featured spots ---------- */
mysqli_query($con, 'UPDATE tbl_movie_series SET feature_slot = NULL');
foreach ($features as $slot => $title) {
    $stmt = $con->prepare('UPDATE tbl_movie_series SET feature_slot = ? WHERE title = ? AND backdrop IS NOT NULL AND modal_poster_title IS NOT NULL AND feature_slot IS NULL ORDER BY movie_series_id LIMIT 1');
    $stmt->bind_param('ss', $slot, $title);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        say("note  featured spot $slot: \"$title\" is missing, has no backdrop or no title logo");
    }
    $stmt->close();
}

say("Done. $added new title(s).");
