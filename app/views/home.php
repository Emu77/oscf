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

            <select name="label">
                <option value="good first issue" <?= $selectedLabel === 'good first issue' ? 'selected' : '' ?>>
                    good first issue
                </option>
                <option value="help wanted" <?= $selectedLabel === 'help wanted' ? 'selected' : '' ?>>
                    help wanted
                </option>
                <option value="beginner friendly" <?= $selectedLabel === 'beginner friendly' ? 'selected' : '' ?>>
                    beginner friendly
                </option>
            </select>

            <button type="submit">Suchen</button>
        </form>

        <!-- Ergebnis-Info -->
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php else: ?>
            <p class="result-count">
                <strong><?= number_format($totalCount, 0, ',', '.') ?></strong> Issues gefunden
                <?php if ($selectedLanguage): ?>
                    für <em><?= htmlspecialchars($selectedLanguage) ?></em>
                <?php endif ?>
            </p>

            <!-- Issue Cards -->
            <div class="cards">
                <?php foreach ($issues as $issue): ?>
                <div class="card">
                    <div class="card-header">
                        <span class="label-badge"><?= htmlspecialchars($selectedLabel) ?></span>
                        <?php if (!empty($issue['repository']['language'])): ?>
                            <span class="lang-badge"><?= htmlspecialchars($issue['repository']['language']) ?></span>
                        <?php endif ?>
                    </div>
                    <h3 class="card-title">
                        <a href="<?= $issue['html_url'] ?>" target="_blank">
                            <?= htmlspecialchars($issue['title']) ?>
                        </a>
                    </h3>
                    <p class="card-repo">
                        📁 <?= htmlspecialchars(str_replace('https://api.github.com/repos/', '', $issue['repository_url'] ?? '')) ?>
                    </p>
                    <div class="card-footer">
                        <span>💬 <?= $issue['comments'] ?> Kommentare</span>
                        <span>📅 <?= date('d.m.Y', strtotime($issue['created_at'])) ?></span>
                        <a href="<?= $issue['html_url'] ?>" target="_blank" class="btn-issue">
                            Issue ansehen →
                        </a>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </main>

    <footer class="footer">
        <div class="container">
            <p>OSCF – IHK Abschlussprojekt &nbsp;|&nbsp; Daten via GitHub API &nbsp;|&nbsp; Cache: 60 Min.</p>
        </div>
    </footer>
</body>
</html>