<?php
class HomeController {
    private array $languages = [
        'PHP', 'JavaScript', 'Python', 'Java', 'TypeScript',
        'C++', 'C#', 'Ruby', 'Go', 'Rust', 'Kotlin', 'Swift'
    ];

    public function index(): void {
        $selectedLanguage = $_GET['language'] ?? 'PHP';
        $selectedLabel    = $_GET['label']    ?? 'good first issue';

        $github = new GitHubModel();
        $result = $github->search($selectedLanguage, $selectedLabel);

        $issues     = $result['items']       ?? [];
        $totalCount = $result['total_count'] ?? 0;
        $error      = $result['error']       ?? null;
        $languages  = $this->languages;

        require_once APP_PATH . '/views/home.php';
    }
}