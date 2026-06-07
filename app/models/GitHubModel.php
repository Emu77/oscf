<?php
class GitHubModel {
    private CacheModel $cache;
    private string $apiBase = 'https://api.github.com';

    public function __construct() {
        $this->cache = new CacheModel();
    }

    // Repos suchen (Hauptansicht)
    public function searchRepos(string $language = '', string $searchQuery = '', int $page = 1): array {
        $cacheKey = 'repo_' . md5($language . $searchQuery . $page);

        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $q = 'has:issues is:public';
        if ($language)    $q .= ' language:' . $language;
        if ($searchQuery) $q .= ' ' . $searchQuery . ' in:name,description,readme';

        $url = $this->apiBase . '/search/repositories?q=' . urlencode($q)
             . '&sort=updated&per_page=20&page=' . $page;

        $data = $this->curl($url);
        if (!isset($data['error'])) {
            $this->cache->set($cacheKey, $data);
        }
        return $data;
    }

    // Issues suchen (zweite Ansicht)
    public function searchIssues(string $language = '', string $label = '', string $searchQuery = '', int $page = 1): array {
        $cacheKey = 'iss_' . md5($language . $label . $searchQuery . $page);

        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $q = 'state:open';
        if ($label)       $q .= ' label:"' . $label . '"';
        if ($language)    $q .= ' language:' . $language;
        if ($searchQuery) $q .= ' ' . $searchQuery . ' in:title';

        $url = $this->apiBase . '/search/issues?q=' . urlencode($q)
             . '&sort=created&per_page=20&page=' . $page;

        $data = $this->curl($url);
        if (!isset($data['error'])) {
            $this->cache->set($cacheKey, $data);
        }
        return $data;
    }

    private function curl(string $url): array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'User-Agent: OSCF-App/1.0',
                'Accept: application/vnd.github.v3+json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'GitHub API Fehler: ' . $httpCode, 'items' => []];
        }

        return json_decode($response, true);
    }
}
