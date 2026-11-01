# Installation — Astral Forum

## Laragon (local)

1. `composer create-project astral-php/astral-forum:^0.5 mon-forum`
2. Vhost `astral-forum.local` → dossier `public/`
3. `cp .env.example .env` puis ajuster `APP_BASE_URL`
4. `php bin/console migrate`
5. Ouvrir le site → `/register` (premier compte = **admin**)
6. **Première visite Administration** → wizard communauté :
   - nom de la communauté
   - accroche (home)
   - description du **Forum général** (le nom reste « Forum général »)
   - la **FAQ** est créée / alignée sur ce thème

## FTP / prod

Uploader le projet **sans** `vendor/`, `.env`, `database/*.sqlite`.

Sur le serveur :

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env   # puis éditer
php bin/console migrate
```

`public/` = document root. Après le premier admin : `/admin/setup`.

## `.env` utile

| Clé | Rôle |
|-----|------|
| `APP_NAME` / `APP_VERSION` | Fallback + badge version |
| `APP_BASE_URL` | Liens e-mail |
| `AUTH_REGISTRATION` | `direct` ou `confirm` |
| `FORUM_PRIMARY` | Couleur maroon |
| `MAIL_*` | Notifications réponses |
| `AUTH_EXTRA_REMEMBER_DAYS` | Durée « Se souvenir de moi » |

Identité communauté : **pas** le `.env` — fichier `storage/community.json` via `/admin/setup`.

## Rôles

| Rôle | Accès |
|------|--------|
| Invité | Lecture publique |
| User | Sujets, réponses, FAQ proposées, profil |
| Modérateur | + verrouiller / épingler / masquer |
| Admin | + forums, FAQ, utilisateurs, setup communauté |

## Compte

Suppression = **anonymisation** (messages conservés, nom « Membre parti »). Le dernier admin ne peut pas s’anonymiser.
