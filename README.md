# Astral Forum

Forum **create-project** (starter **2.0** + astral-core **^2**) : forums, sujets, posts, FAQ modérée.

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![Version](https://img.shields.io/badge/version-0.1.0-blue)](./CHANGELOG.md)
[![License](https://img.shields.io/badge/license-MIT-green)](./LICENSE)
[![Tests](https://img.shields.io/badge/tests-PHPUnit%20OK-9933CC)](./phpunit.xml)

| | |
|---|---|
| **UI** | Nova **maroon** (`#9f1239`) |
| **URL locale** | [http://astral-forum.local/](http://astral-forum.local/) |
| **Roadmap 1.0** | [ROADMAP.md](./ROADMAP.md) |

## Prérequis

- PHP ^8.3
- Composer
- Laragon (vhost `astral-forum.local` → `public/`) recommandé

## Installation

```bash
composer create-project astral-php/astral-forum:^0.1 mon-forum
cd mon-forum
cp .env.example .env
php bin/console migrate
```

Ouvrir **http://astral-forum.local/** (ou configurer le vhost) → `/register` (premier compte = admin).

### Dépendances clés

- `astral-php/astral-core:^2.0`
- `astral-php/astral-form:^0.1.2` (éviter la contrainte exacte `0.1` → résout 0.1.0 + hub ^1)
- `astral-php/astral-cache:^0.1`

`composer serve` (127.0.0.1:8083) = secours hors Laragon.

## Fonctionnalités 0.1.0

- Forum général seedé à la migration
- Lecture publique, création sujet + réponse (membres)
- Forums specialized / faq (admin)
- Workflow FAQ + publication atomique
- Modération : lock / pin / hide (admin)
- Auth starter (register / login / rôles Admin · User · Guest)

## Tests

```bash
composer test
composer qa   # serveur Laragon requis
```

CSRF : champ `_token`.

## Liens

- Packagist : [astral-php/astral-forum](https://packagist.org/packages/astral-php/astral-forum)
- GitHub : [astral-php/astral-forum](https://github.com/astral-php/astral-forum)
- Changelog : [CHANGELOG.md](./CHANGELOG.md)
