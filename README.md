# QuizBattle — API Backend (Laravel)

API REST pour **QuizBattle**, une app de quiz de culture générale entre potes, en mode **asynchrone** (chacun joue à son rythme) avec scores et classements.

## Stack

- Laravel 11
- Laravel Sanctum (auth par token)
- MySQL / PostgreSQL

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configurer la base de données dans `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD), puis :

```bash
php artisan migrate --seed
php artisan serve
```

L'API est disponible sur `http://localhost:8000/api`.

## Modèle de données

- **users** — pseudo, email, mot de passe, avatar (emoji)
- **categories** — catégories de questions (Culture générale, Sport, Cinéma, etc.)
- **questions** — banque de questions (texte, options JSON, index de la bonne réponse)
- **quiz_sessions** — une session = un code à partager + une catégorie + N questions tirées au hasard
- **session_questions** — snapshot des questions exactes tirées pour une session donnée
- **participations** — un joueur qui a rejoint une session (score, statut terminé/non terminé)
- **answers** — chaque réponse donnée par un joueur, avec temps de réponse et points gagnés

## Barème de points

Réponse correcte : `max(20, 100 - temps_de_réponse_en_secondes × 4)` points.
Réponse incorrecte ou absente : 0 point.
Plus tu réponds vite et juste, plus tu marques de points.

## Endpoints principaux

| Méthode | Route | Description |
|---|---|---|
| POST | `/api/register` | Inscription (pseudo, email, mot de passe, avatar) |
| POST | `/api/login` | Connexion |
| POST | `/api/logout` | Déconnexion (auth requise) |
| GET | `/api/me` | Profil courant |
| GET | `/api/categories` | Liste des catégories |
| POST | `/api/quiz-sessions` | Créer une session (category_id, questions_count) → renvoie un code |
| POST | `/api/quiz-sessions/{code}/join` | Rejoindre une session via son code |
| POST | `/api/quiz-sessions/{code}/answer` | Soumettre une réponse |
| POST | `/api/quiz-sessions/{code}/finish` | Terminer sa participation |
| GET | `/api/quiz-sessions/{code}/leaderboard` | Classement d'une session |
| GET | `/api/leaderboard/global` | Classement global cumulé |

Toutes les routes sauf `/register` et `/login` nécessitent un header `Authorization: Bearer {token}`.

## Exemple de flux

1. Un joueur s'inscrit / se connecte → reçoit un token
2. Il crée une session sur une catégorie → reçoit un `code` (ex: `AB3XQ9`)
3. Il partage le code à ses potes
4. Chaque pote rejoint via `/join`, répond aux questions à son rythme via `/answer`
5. Chacun termine via `/finish`
6. Tout le monde consulte `/leaderboard` pour voir le classement de la partie
