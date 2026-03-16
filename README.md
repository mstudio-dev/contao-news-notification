# Contao News Notification Bundle

Sends an email notification when a news article with a non-empty teaser is saved in the Contao backend.

## Requirements

- PHP ^8.1
- Contao ^5.0
- contao/news-bundle ^5.0

## Installation

### Via Composer

```bash
composer require mstudio-dev/contao-news-notification
```

### Ohne Packagist (direkt über GitHub)

In der `composer.json` des Contao-Projekts:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/mstudio-dev/contao-news-notification"
    }
]
```

```bash
composer require mstudio-dev/contao-news-notification:^1.0
```

Anschließend im Contao-Backend unter **Systemwartung → Datenbank aktualisieren**.

## Konfiguration

Die Einstellungen befinden sich im Contao-Backend unter **Einstellungen** im Abschnitt **News-Benachrichtigung**:

| Feld | Beschreibung |
|---|---|
| **Absenderadresse** | E-Mail-Adresse, die als Absender verwendet wird. Wenn leer, wird `no-reply@<hostname>` genutzt. |
| **Empfängeradresse** | Pflichtfeld – an diese Adresse wird die Benachrichtigung gesendet. |

## Verhalten

- Eine E-Mail wird nur ausgelöst, wenn der **Teaser** des News-Eintrags **nicht leer** ist.
- Der Backend-Benutzername wird in der E-Mail-Benachrichtigung mitgesendet.
- Versandfehler werden ins Symfony-Log geschrieben, ohne die Backend-Oberfläche zu unterbrechen.

## Lizenz

MIT – © Markus Schnagl, [mstudio.de](https://mstudio.de)
