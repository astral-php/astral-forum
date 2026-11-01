# astral-forum — Roadmap produit

> Objectif : **1.0.0** aussi abouti que possible.  
> **0.1.0** = fondation validée (QA). Les lots suivants enrichissent sans casser le contrat create-project.

## Jalons

| Version | Focus |
|---------|--------|
| **0.1.0** | Forums, sujets, posts, FAQ modérée, UI nova-maroon |
| **0.2.0** | Édition / soft-delete posts + throttle (auth-extra) |
| **0.3.0** | Recherche + rôle Moderateur / permissions |
| **0.4.0** | Notifications, compteurs/cache, FAQ configurable |
| **0.5.0** | UI Nova Maroon Light (home + admin shell) |
| **1.0.0** | Tests CI, docs prod, polish restant, politique comptes |

## P0 — Indispensable 1.0

1. Édition / suppression soft des posts (auteur + délai ; admin illimité)
2. Rôle Moderateur **ou** permissions granulaires (lock/pin/hide sans Admin full)
3. `astral-auth-extra` : throttle sujet / réponse / FAQ + remember-me
4. Recherche sujets/posts (paginée)
5. Profil auteur sur les messages
6. `astral-testing` compatible core ^2 + smokes CI
7. Politique suppression compte (anonymisation, pas de cascade destructive)
8. Docs 1.0 : install Laragon/FTP, `.env`, permissions, workflow FAQ

## P1 — Fortement recommandé 1.0

9. Abonnements / notifications e-mail (sync ; worker = plus tard)
10. Citations ; Markdown assaini (optionnel)
11. Compteurs (réponses, vues) + cache listes (IDs/arrays)
12. FAQ : réponses membres configurables ; resoumission après reject
13. Polish UI Nova maroon (empty states, mobile, breadcrumbs)
14. Seed démo (forums + sujets d’exemple)

## P2 — Après 1.0 (1.1+)

15. Vite / Tailwind build  
16. astral-template layouts  
17. Digests async (worker)  
18. Pièces jointes  
19. Multi-langue  
20. API REST  

## Hors périmètre

Paiement, PayPal, shipping, twide (apps officielles = vues PHP natives).
