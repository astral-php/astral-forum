# astral-forum — Roadmap produit

> **1.0.0** livré. Prochaine phase : Markdown, polish UI, seed contenu.

## Jalons

| Version | Focus |
|---------|--------|
| **0.1.0** | Forums, sujets, posts, FAQ modérée, UI nova-maroon |
| **0.2.0** | Édition / soft-delete posts + throttle (auth-extra) |
| **0.3.0** | Recherche + rôle Moderateur / permissions |
| **0.4.0** | Notifications, compteurs/cache, FAQ configurable |
| **0.5.0** | UI Nova Maroon Light (home + admin shell) |
| **0.5.1** | Polish formulaires / nav compte / admin forums |
| **1.0.0** | Setup identité 1re visite admin · CI · docs · politique comptes · FAQ rédacteur |

## Setup communauté (1.0 — figé)

**Quand :** première visite **admin** (pas CLI).

| # | Champ | Règle |
|---|--------|--------|
| 1 | Nom communauté | Ex. *La communauté des jardiniers en permaculture* |
| 2 | Tagline / accroche home | Phrase d’accueil |
| 3 | Forum **général** | Nom **figé** « Forum général » — seule la **description** est éditable |

**FAQ :** toujours alignée sur le thème de la communauté. Libellé **FAQ** stable ; description dérivée du thème.

**Modèle FAQ (figé) :** membre **rédige** (Sujet + Rédiger la FAQ) ; admin publie / demande un complément / refuse ; publiée = lecture seule.

Hors wizard : couleurs, mails, atouts, forums spécialisés, logo.

## P0 — Indispensable 1.0 ✅

1. ~~Édition / suppression soft des posts~~ ✅ **0.2.0**
2. ~~Rôle Moderateur / permissions~~ ✅ **0.3.0**
3. Throttle écriture ✅ **0.2.0** · remember-me ✅
4. ~~Recherche paginée~~ ✅ **0.3.0**
5. ~~Profil auteur sur les messages~~ ✅ **0.4.0**
6. ~~CI GitHub Actions + PHPUnit~~ ✅
7. ~~Anonymisation compte~~ ✅
8. ~~Docs 1.0~~ ✅ `docs/INSTALL.md` · `docs/FAQ.md`
9. ~~Setup 1re visite admin~~ ✅ `/admin/setup`

## Prochaine phase (post-1.0)

À reprendre quand on revient sur le forum :

| # | Item |
|---|------|
| 10 | Markdown assaini + doc utilisateur (titres, liens, tableaux, listes, citations, code). **Pas d’IA.** |
| 13 | Polish UI restant (empty states, mobile, breadcrumbs) |
| 14 | Seed démo contenu (sujets d’exemple) |

Déjà livré en P1 avant 1.0 :

9. ~~Abonnements / notifications e-mail~~ ✅ **0.4.0**
11. ~~Compteurs + cache listes~~ ✅ **0.4.0**
12. ~~FAQ configurable + resoumission~~ ✅ **0.4.0** (+ modèle rédacteur **1.0.0**)

## Après (1.1+)

15. Vite / Tailwind build
16. astral-template layouts
17. Digests async (worker)
18. Pièces jointes
19. Multi-langue
20. API REST
21. Logo / presets couleur / atouts home éditables

## Hors périmètre

Paiement, PayPal, shipping, twide, **IA « transformer en Markdown »**.
