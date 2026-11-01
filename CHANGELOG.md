# Changelog

Toutes les modifications notables d’**astral-forum** sont documentées ici.

Le format s’inspire de [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/lang/fr/).

## [Unreleased]

Voir [ROADMAP.md](./ROADMAP.md) pour le chemin vers **1.0.0**.

## [0.4.0] - 2026-11-01

### Ajouté

- Compteurs `reply_count` / `view_count` sur les sujets (+ vues dédupliquées par session).
- Cache liste forums via IDs (`ForumCatalogService`).
- FAQ : `allow_member_replies` configurable ; resoumission après rejet.
- Abonnements sujets + notifications e-mail sync sur réponse.
- Lien profil auteur sur les messages.

### Migration

- `2026_11_01_000003_forum_040_counters_subscriptions.php`

## [0.3.0] - 2026-11-01

### Ajouté

- Recherche publique paginée (`/search`) — titre + contenu des messages publiés.
- Rôle app **moderator** (`ForumRoles`) : lock / pin / hide sans accès admin forums/FAQ/users.
- `ModeratorMiddleware` sur les routes de modération.

### Modifié

- `ForumPermissionService` : `canModerate` = Admin|Modérateur ; `canManageForums` / FAQ = Admin seul.

## [0.2.0] - 2026-11-01

### Ajouté

- Édition des messages (auteur dans la fenêtre configurable, admin illimité) + marqueur `edited_at`.
- Soft-delete auteur (`status=deleted`) ; premier message protégé (admin : hide).
- Throttle écriture via **astral-auth-extra** : sujet / réponse / proposition FAQ (`ActionThrottler` + `CacheThrottleStore`).
- Config `forum.edit_window_minutes` et `forum.throttle.*` ; provider `AuthExtraServiceProvider`.

### Dépendances

- `astral-php/astral-auth-extra:^0.2`

## [0.1.2] - 2026-10-31

### Corrigé

- Dépendance **astral-core 2.1.2** — restaure `bin/console`.

## [0.1.1] - 2026-10-31

### Modifié

- `astral-php/astral-core` **^2.1** (bump technique feuille 2.1 **P3** — pas de features produit).

### Notes

- PHP `^8.3` inchangé. Phases produit 0.2→1.0 : [ROADMAP.md](./ROADMAP.md).

## [0.1.0] - 2026-10-29

### Ajouté

- Application **create-project** sur **astral-starter 2.0** / **astral-core ^2** (PHP ^8.3).
- Forums (`general` seedé, `specialized`, `faq`), sujets, posts.
- Création atomique sujet + premier message ; réponses membres.
- Workflow FAQ (proposition → revue admin → publication atomique).
- Modération admin : verrouillage, épinglage, masquage de messages + journal.
- UI **Nova maroon** (`#9f1239`) — famille Nova Astral.
- Dépendances : `astral-form:^0.1.2`, `astral-cache:^0.1`.
- Smokes PHPUnit + script `bin/qa-parcours.php` (Laragon `http://astral-forum.local/`).
- [ROADMAP.md](./ROADMAP.md) — backlog explicite jusqu’à **1.0.0**.

### Notes

- Rôle Moderateur : pas en 0.1 (Admin only).
- Édition de posts, recherche, throttle : prévus avant **1.0.0**.
