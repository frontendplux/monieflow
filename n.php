<?php

/*
|--------------------------------------------------------------------------
| MULTI-ENGINE SEARCH BACKEND (via SearXNG)
|--------------------------------------------------------------------------
|
| index.php?search=1&q=hello
|
| SearXNG is a metasearch engine: one request here fans out to every
| engine listed in $ENGINES below and SearXNG merges + dedupes the
| results for us, each one tagged with which engine(s) found it.
|
|--------------------------------------------------------------------------
*/

if (isset($_GET['search'])) {

    header('Content-Type: application/json; charset=utf-8');

    $query = trim($_GET['q'] ?? '');

    if ($query === '') {
        echo json_encode([
            'success' => false,
            'error' => 'Enter a search query'
        ]);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | ENGINES TO QUERY
    |--------------------------------------------------------------------------
    |
    | These are the engine "names" SearXNG ships support for out of the box.
    | Add/remove names here to change which engines get queried - each name
    | must exist (and be enabled, disabled: false) in your SearXNG
    | instance's settings.yml under the `engines:` section.
    |
    | NOTE: a handful of names from a "top search engines" list don't have
    | a real, independent index to query anymore (WebCrawler, Dogpile,
    | MetaCrawler, HotBot, Excite are just Bing/Yahoo resellers today), and
    | a few require a paid API key SearXNG doesn't ship by default (Kagi,
    | You.com). Those are left out - there's nothing real to query.
    |
    |--------------------------------------------------------------------------
    */

    $ENGINES = [
        'google',
        'bing',
        'brave',
        'duckduckgo',
        'yahoo',
        'yandex',
        'baidu',
        'naver',
        'qwant',
        'mojeek',
        'ecosia',
        'startpage',
        'swisscows',
        'seznam',
        'sogou',
        'metager',
        'marginalia',
        'yacy',
        'wiby',
    ];

    /*
    |--------------------------------------------------------------------------
    | YOUR SEARXNG SERVER
    |--------------------------------------------------------------------------
    |
    | Example if SearXNG is running on your server:
    |
    | http://127.0.0.1:8080
    |
    | Or:
    |
    | https://search.yourdomain.com
    |
    |--------------------------------------------------------------------------
    */

    $searxng =
        'https:' .
        http_build_query([
            'q' => $query,
            'format' => 'json',
            'categories' => 'general',
            'engines' => implode(',', $ENGINES),
            'language' => 'en',
            'safesearch' => 1
        ]);

    /*
    |--------------------------------------------------------------------------
    | CURL
    |--------------------------------------------------------------------------
    */

    $ch = curl_init($searxng);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT => 'MySearch/1.0',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        echo json_encode([
            'success' => false,
            'error' => 'Search request failed: ' . $error
        ]);
        exit;
    }

    if ($status < 200 || $status >= 300) {
        echo json_encode([
            'success' => false,
            'error' => 'Search server returned HTTP ' . $status
        ]);
        exit;
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid search response'
        ]);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | RESULTS
    |--------------------------------------------------------------------------
    |
    | SearXNG already dedupes results that multiple engines agreed on, and
    | tells us every engine that surfaced each one via `engines` (plural).
    | We normalize that into a clean array for the frontend.
    |
    |--------------------------------------------------------------------------
    */

    $results = [];
    $engines_seen = [];

    foreach (($data['results'] ?? []) as $item) {

        $url = $item['url'] ?? '';
        $title = $item['title'] ?? '';
        $description = $item['content'] ?? '';

        // SearXNG gives a plural `engines` list when several engines agree
        // on a result, falling back to the singular `engine` field.
        $engines = $item['engines'] ?? (isset($item['engine']) ? [$item['engine']] : ['unknown']);

        if (!$url || !$title) {
            continue;
        }

        foreach ($engines as $e) {
            $engines_seen[$e] = true;
        }

        $results[] = [
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'engines' => $engines,
            'score' => $item['score'] ?? 0
        ];
    }

    usort($results, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    echo json_encode([
        'success' => true,
        'query' => $query,
        'total' => count($results),
        'engines_requested' => $ENGINES,
        'engines_that_responded' => array_keys($engines_seen),
        'results' => $results
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Multi-Engine Search</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        .search {
            display: flex;
            gap: 10px;
        }
        input {
            flex: 1;
            padding: 14px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 14px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background: #1a73e8;
            color: #fff;
            font-weight: 600;
        }
        .meta {
            color: #666;
            font-size: 13px;
            margin: 14px 0;
        }
        .result {
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
        }
        .title {
            font-size: 20px;
            color: #1a0dab;
            text-decoration: none;
        }
        .url {
            color: #188038;
            font-size: 14px;
            margin: 6px 0;
        }
        .description {
            color: #444;
            line-height: 1.5;
        }
        .engine-tags {
            margin-top: 8px;
        }
        .engine {
            display: inline-block;
            margin-right: 6px;
            margin-top: 4px;
            padding: 3px 8px;
            background: #eee;
            border-radius: 4px;
            font-size: 12px;
            text-transform: capitalize;
        }
    </style>
</head>
<body>

<h1>My Multi-Engine Search</h1>

<div class="search">
    <input id="query" type="text" placeholder="Search the web across many engines...">
    <button id="search_net">Search</button>
</div>

<div class="meta"></div>
<div class="data"></div>

<script>
const button = document.getElementById('search_net');
const input = document.getElementById('query');
const data = document.querySelector('.data');
const metaEl = document.querySelector('.meta');

button.onclick = search;

input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') search();
});

async function search() {
    const query = input.value.trim();
    if (!query) return;

    metaEl.innerHTML = '';
    data.innerHTML = `<p>Searching across multiple engines...</p>`;

    try {
        const response = await fetch('?search=1&q=' + encodeURIComponent(query));
        const result = await response.json();

        if (!result.success) {
            data.innerHTML = `<p>${escapeHtml(result.error)}</p>`;
            return;
        }

        if (!result.results || result.results.length === 0) {
            data.innerHTML = `<p>No results found.</p>`;
            return;
        }

        metaEl.innerHTML =
            `${result.total} results &middot; ` +
            `engines that responded: ${escapeHtml((result.engines_that_responded || []).join(', '))}`;

        data.innerHTML = result.results.map(item => `
            <div class="result">
                <a class="title" href="${escapeAttribute(item.url)}" target="_blank" rel="noopener noreferrer">
                    ${escapeHtml(item.title)}
                </a>
                <div class="url">${escapeHtml(item.url)}</div>
                <div class="description">${escapeHtml(item.description || '')}</div>
                <div class="engine-tags">
                    ${(item.engines || []).map(e => `<span class="engine">${escapeHtml(e)}</span>`).join('')}
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error(error);
        data.innerHTML = `<p>Unable to search the web.</p>`;
    }
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

function escapeAttribute(value) {
    return escapeHtml(value);
}
</script>

</body>
</html