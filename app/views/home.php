<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSCF – Open Source Contribution Finder</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/public/favicon.svg">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>OSCF</h1>
            <p>Open Source Contribution Finder</p>
        </div>
    </header>

    <main class="container">

        <!-- Suchformular -->
        <form class="search-form" method="GET" action="<?= BASE_URL ?>/">
            <select name="language">
                <option value="">Alle Sprachen</option>
                <?php foreach ($languages as $lang): ?>
                    <option value="<?= $lang ?>" <?= $selectedLanguage === $lang ? 'selected' : '' ?>>
                        <?= $lang ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="q" placeholder="Repository suchen..."
                   value="<?= htmlspecialchars($searchQuery) ?>">

            <select name="topic">
                <option value="">-- Topic wählen --</option>
                <option value="good-first-issue" <?= $selectedTopic === 'good-first-issue' ? 'selected' : '' ?>>good-first-issue</option>
                <option value="hacktoberfest" <?= $selectedTopic === 'hacktoberfest' ? 'selected' : '' ?>>hacktoberfest</option>
                <option value="beginner-friendly" <?= $selectedTopic === 'beginner-friendly' ? 'selected' : '' ?>>beginner-friendly</option>
                <option value="help-wanted" <?= $selectedTopic === 'help-wanted' ? 'selected' : '' ?>>help-wanted</option>
            </select>

            <button type="submit">Suchen</button>
        </form>

        <!-- Ergebnis-Info -->
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php else: ?>
            <p class="result-count">
                <strong><?= number_format($totalCount, 0, ',', '.') ?></strong> Repositories gefunden
                <?php if ($selectedLanguage): ?>· Sprache: <em><?= htmlspecialchars($selectedLanguage) ?></em><?php endif ?>
                <?php if ($searchQuery): ?>· Suche: <em>"<?= htmlspecialchars($searchQuery) ?>"</em><?php endif ?>
                <?php if ($totalCount > 1000): ?>
                    <span class="api-limit-hint">(GitHub zeigt max. 1.000 Ergebnisse)</span>
                <?php endif ?>
            </p>

            <!-- Repo Cards -->
            <div class="cards">
                <?php foreach ($items as $repo): ?>
                <div class="card">
                    <div class="card-header">
                        <?php if (!empty($repo['language'])): ?>
                            <span class="lang-badge"><?= htmlspecialchars($repo['language']) ?></span>
                        <?php endif ?>
                        <span class="star-badge">⭐ <?= number_format($repo['stargazers_count'], 0, ',', '.') ?></span>
                    </div>

                    <h3 class="card-title">
                        <a href="<?= htmlspecialchars($repo['html_url']) ?>" target="_blank">
                            <?= htmlspecialchars($repo['full_name']) ?>
                        </a>
                    </h3>

                    <?php if (!empty($repo['description'])): ?>
                        <p class="card-desc"><?= htmlspecialchars(mb_strimwidth($repo['description'], 0, 120, '…')) ?></p>
                    <?php endif ?>

                    <div class="card-footer">
                        <span>🐛 <?= number_format($repo['open_issues_count'], 0, ',', '.') ?> Issues</span>
                        <span>🍴 <?= number_format($repo['forks_count'], 0, ',', '.') ?> Forks</span>
                        <a href="<?= htmlspecialchars($repo['html_url']) ?>" target="_blank" class="btn-issue">
                            Repo ansehen →
                        </a>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <!-- Pagination -->
        <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?language=<?= urlencode($selectedLanguage) ?>&q=<?= urlencode($searchQuery) ?>&topic=<?= urlencode($selectedTopic) ?>&page=<?= $page - 1 ?>" class="btn-page">← Zurück</a>
            <?php endif ?>

            <div class="page-numbers">
                <?php
                $start = max(1, $page - 2);
                $end   = min($totalPages, $page + 2);
                if ($start > 1): ?>
                    <a href="?language=<?= urlencode($selectedLanguage) ?>&q=<?= urlencode($searchQuery) ?>&topic=<?= urlencode($selectedTopic) ?>&page=1" class="btn-page">1</a>
                    <?php if ($start > 2): ?><span class="page-dots">…</span><?php endif ?>
                <?php endif ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?language=<?= urlencode($selectedLanguage) ?>&q=<?= urlencode($searchQuery) ?>&topic=<?= urlencode($selectedTopic) ?>&page=<?= $i ?>"
                       class="btn-page <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?><span class="page-dots">…</span><?php endif ?>
                    <a href="?language=<?= urlencode($selectedLanguage) ?>&q=<?= urlencode($searchQuery) ?>&topic=<?= urlencode($selectedTopic) ?>&page=<?= $totalPages ?>" class="btn-page"><?= $totalPages ?></a>
                <?php endif ?>
            </div>

            <?php if ($page < $totalPages): ?>
                <a href="?language=<?= urlencode($selectedLanguage) ?>&q=<?= urlencode($searchQuery) ?>&topic=<?= urlencode($selectedTopic) ?>&page=<?= $page + 1 ?>" class="btn-page">Weiter →</a>
            <?php endif ?>
        </div>
        <?php endif ?>

    </main>

    <footer class="footer">
        <div class="container">
            <p>OSCF – Projekt &nbsp;|&nbsp; Daten via GitHub API &nbsp;|&nbsp; Cache: 60 Min.</p>
        </div>
    </footer>
</body>
</html>
