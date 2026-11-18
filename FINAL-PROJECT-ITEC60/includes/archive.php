<?php
/*
 * Internet Archive (archive.org) connection for free, full-length videos.
 * Used by the admin "Free full movie" search: it finds public domain films
 * and cartoons and returns a direct MP4 link that CineVault's own player
 * streams. Nothing is downloaded to this server. No API key is needed.
 *
 * Only search the collections below: they hold public domain and freely
 * licensed films. Still check each title before adding it.
 */

const ARCHIVE_COLLECTIONS = [
    'feature_films', 'classic_tv', 'classic_cartoons', 'film_noir', 'sci-fi_horror',
    'comedy_films', 'silent_films', 'short_films', 'animationandcartoons', 'opensource_movies',
];

class ArchiveException extends RuntimeException {}

function archive_get($url)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'CineVault/1.0 (student project)',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = $body === false ? null : json_decode($body, true);
    if ($code !== 200 || !is_array($data)) {
        throw new ArchiveException('Could not reach the Internet Archive. Try again in a moment.');
    }
    return $data;
}

/* Search public domain films / cartoons / TV by title. */
function archive_search($query)
{
    $words = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $query);
    $words = trim(preg_replace('/\s+/', ' ', $words));
    if ($words === '') {
        return [];
    }
    $q = 'title:(' . $words . ') AND mediatype:movies AND collection:(' . implode(' OR ', ARCHIVE_COLLECTIONS) . ')';
    $url = 'https://archive.org/advancedsearch.php?' . http_build_query([
        'q' => $q,
        'fl' => ['identifier', 'title', 'year', 'downloads'],
        'sort' => ['downloads desc'],
        'rows' => 12,
        'output' => 'json',
    ]);
    $data = archive_get($url);
    $out = [];
    foreach ($data['response']['docs'] ?? [] as $d) {
        $out[] = [
            'id'    => (string) $d['identifier'],
            'title' => is_array($d['title'] ?? '') ? implode(' ', $d['title']) : (string) ($d['title'] ?? $d['identifier']),
            'year'  => isset($d['year']) ? (int) (is_array($d['year']) ? $d['year'][0] : $d['year']) : null,
            'thumb' => 'https://archive.org/services/img/' . rawurlencode($d['identifier']),
        ];
    }
    return $out;
}

/*
 * The playable MP4 files of one archive.org item (best quality first).
 * Each has a direct https://archive.org/download/... link.
 */
function archive_videos($identifier)
{
    if (!preg_match('/^[A-Za-z0-9._-]{1,100}$/', $identifier)) {
        throw new ArchiveException('Not an Internet Archive item.');
    }
    $data = archive_get('https://archive.org/metadata/' . rawurlencode($identifier));
    if (empty($data['files'])) {
        throw new ArchiveException('That item was not found on the Internet Archive.');
    }

    $rank = ['h.264 HD' => 5, 'h.264' => 4, '512Kb MPEG4' => 3, 'h.264 IA' => 2, 'MPEG4' => 1];
    $files = [];
    foreach ($data['files'] as $f) {
        $name = (string) ($f['name'] ?? '');
        if (!preg_match('/\.mp4$/i', $name) || !isset($rank[$f['format'] ?? ''])) {
            continue;
        }
        $url = 'https://archive.org/download/' . rawurlencode($identifier) . '/' . implode('/', array_map('rawurlencode', explode('/', $name)));
        if (!cv_is_stream_url($url)) {
            continue; // too long or odd characters
        }
        $len = (float) ($f['length'] ?? 0);
        if ($len <= 0 && preg_match('/^(\d+):(\d+)(?::(\d+))?/', (string) ($f['length'] ?? ''), $m)) {
            $len = isset($m[3]) && $m[3] !== '' ? $m[1] * 3600 + $m[2] * 60 + $m[3] : $m[1] * 60 + $m[2];
        }
        $files[] = [
            'name'    => $name,
            'url'     => $url,
            'format'  => $f['format'],
            'size_mb' => round(((int) ($f['size'] ?? 0)) / 1048576),
            'minutes' => $len > 0 ? (int) round($len / 60) : null,
            'rank'    => $rank[$f['format']],
        ];
    }
    // Best format first, then the longest file (the full film, not a clip).
    usort($files, fn($a, $b) => [$b['rank'], $b['minutes'] ?? 0] <=> [$a['rank'], $a['minutes'] ?? 0]);

    $meta = $data['metadata'] ?? [];
    return [
        'id'      => $identifier,
        'title'   => is_array($meta['title'] ?? null) ? $meta['title'][0] : (string) ($meta['title'] ?? $identifier),
        'year'    => isset($meta['year']) ? (int) (is_array($meta['year']) ? $meta['year'][0] : $meta['year']) : null,
        'license' => (string) ($meta['licenseurl'] ?? ''),
        'page'    => 'https://archive.org/details/' . rawurlencode($identifier),
        'videos'  => $files,
    ];
}
