# Upgrade — astral-starter 0.x / 1.x → 2.0

## Prérequis

- PHP **8.3+**
- `"astral-php/astral-core": "^2.0"`

```bash
composer require astral-php/astral-core:^2.0
```

Voir aussi : [astral-core UPGRADE-2.0](https://github.com/astral-php/astral-core/blob/main/UPGRADE-2.0.md).

## Role

| Avant | 2.0 |
|-------|-----|
| `Role::ADMIN` | `Role::Admin` |
| `Role::USER` | `Role::User` |
| `Role::GUEST` | `Role::Guest` |

Les valeurs string (`admin`, `user`, `guest`) sont inchangées.  
Comparaisons avec `$user->role` (string) : utiliser `Role::Admin->value`.  
`Auth::is()` / `can()` acceptent `Role|string`.

## Breaking

- PHP plancher **8.3**
- Dépendance core **^2.0** (plus de ^1.2)

## Après upgrade

Vérifier admin rôles (`/admin/users`), login / register, et `composer test`.
