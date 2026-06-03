# OSCF – Open Source Contribution Finder

Ein PHP/MySQL-Webtool zur Suche nach einsteigerfreundlichen Open-Source-Issues auf GitHub.

---

## Projektbeschreibung

Der **Open Source Contribution Finder (OSCF)** hilft Entwicklern dabei, geeignete Open-Source-Projekte auf GitHub zu finden, zu denen sie beitragen können. Über die GitHub Search API werden Issues gefiltert nach Programmiersprache und Label (z. B. `good first issue`, `help wanted`) angezeigt – mit integriertem Caching-System zur Reduzierung von API-Anfragen.

Dieses Projekt entstand als Projekt im Rahmen der Ausbildung zum Fachinformatiker Anwendungsentwicklung.

---

## Features

- Suche nach Open-Source-Issues über die GitHub REST API v3
- Filter nach Programmiersprache (PHP, JavaScript, Python, Java, TypeScript, u. v. m.)
- Filter nach Issue-Label (`good first issue`, `help wanted`, `beginner friendly`)
- Datenbankbasiertes Caching (60 Minuten TTL) zur Schonung des API-Rate-Limits
- Responsives Dark-Mode-Dashboard im GitHub-Design
- MVC-Architektur (Model – View – Controller)
- URL-Routing über Apache mod_rewrite

---

## Technologien

| Bereich       | Technologie          |
|---------------|----------------------|
| Backend       | PHP 8.2              |
| Datenbank     | MySQL 8 (via PDO)    |
| Frontend      | HTML5, CSS3          |
| API           | GitHub REST API v3   |
| Webserver     | Apache 2.4 (XAMPP)   |
| Architektur   | MVC (eigenes Framework) |

---

## Projektstruktur

```
osscf/
├── app/
│   ├── controllers/
│   │   └── HomeController.php     # Routing-Logik, Datenweitergabe an View
│   ├── models/
│   │   ├── Database.php           # PDO-Singleton für DB-Verbindung
│   │   ├── CacheModel.php         # Cache-Verwaltung (lesen/schreiben/löschen)
│   │   └── GitHubModel.php        # GitHub API-Anbindung via cURL
│   └── views/
│       └── home.php               # HTML-Dashboard mit PHP-Templating
├── config/
│   └── database.php               # DB-Zugangsdaten (nicht einchecken!)
├── public/
│   ├── css/
│   │   └── style.css              # Dark-Mode-Stylesheet
│   └── js/                        # (für spätere Erweiterungen)
├── cache/                         # (Verzeichnis, reserviert)
├── .htaccess                      # URL-Rewriting für den Router
└── index.php                      # Einstiegspunkt, Autoloader, Router
```

---

## Installation

### Voraussetzungen

- XAMPP (Apache + MySQL + PHP 8.2+)
- PHP-Extension `pdo_mysql` und `curl` aktiviert

### Schritte

**1. Repository klonen / Projektordner anlegen**
```bash
cd /opt/lampp/htdocs        # oder eigener htdocs-Pfad
git clone <repo-url> osscf  # oder Ordner manuell anlegen
```

**2. Datenbank anlegen** (phpMyAdmin oder MySQL-CLI)
```sql
CREATE DATABASE oscf CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE oscf;

CREATE TABLE cache (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cache_key   VARCHAR(255)  NOT NULL UNIQUE,
    data        LONGTEXT      NOT NULL,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cache_key (cache_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**3. Datenbank-Konfiguration anpassen**

Datei `config/database.php`:
```php
<?php
define('DB_HOST',    'localhost');
define('DB_NAME',    'oscf');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');
```

**4. Apache mod_rewrite aktivieren**

In `httpd.conf` für das htdocs-Verzeichnis:
```apache
AllowOverride All
```

**5. Anwendung aufrufen**
```
http://localhost/oscf/
```

---

## Konfiguration

### GitHub API Token (optional, empfohlen)

Ohne Token erlaubt GitHub nur **60 Anfragen/Stunde**. Mit Token **5.000/Stunde**.

Token unter https://github.com/settings/tokens generieren (kein Scope nötig).

In `app/models/GitHubModel.php` eintragen:
```php
CURLOPT_HTTPHEADER => [
    'User-Agent: OSCF-App/1.0',
    'Accept: application/vnd.github.v3+json',
    'Authorization: Bearer ghp_DEIN_TOKEN_HIER',
],
```

### Cache-Dauer anpassen

In `app/models/CacheModel.php`:
```php
private int $ttl = 3600; // Sekunden (Standard: 60 Minuten)
```

---

## Architektur

```
Browser → .htaccess → index.php (Router)
                            ↓
                    HomeController
                    ↙           ↘
             GitHubModel      CacheModel
                  ↓                ↕
            GitHub API         MySQL (cache)
                  ↘
               home.php (View)
                    ↓
                Browser
```

---

## Lizenz

Dieses Projekt wurde im Rahmen einer IHK-Abschlussprüfung erstellt und dient ausschließlich Ausbildungszwecken.

---

## Autor

**Emu** – IHK-Abschlussprojekt Fachinformatiker Anwendungsentwicklung
