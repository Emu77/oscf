<?php
class GitHubModel {
    private CacheModel $cache;
    private string $apiBase = 'https://api.github.com';

    public function __construct() {
        $this->cache = new CacheModel();
    }

    public function search(string $language = '', string $label = 'good first issue'): array {
        $cacheKey = 'gh_' . md5($language . $label);

        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Issues suchen, nicht Repositories
        $query = 'state:open label:"' . $label . '"';
        if ($language) {
            $query .= ' language:' . $language;
        }

        // /search/issues statt /search/repositories
        $url = $this->apiBase . '/search/issues?q=state:open+label:"good+first+issue"+language:' . urlencode($language) . '&sort=created&per_page=20';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'User-Agent: OSCF-App/1.0',
                'Accept: application/vnd.github.v3+json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'GitHub API Fehler: ' . $httpCode, 'items' => []];
        }

        $data = json_decode($response, true);
        $this->cache->set($cacheKey, $data);

        return $data;
    }
}