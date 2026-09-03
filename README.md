# QuizBattle API

Stack : PHP 8.3, Laravel 13, PostgreSQL, Docker, Render

API REST du backend de QuizBattle, une application de quiz de culture generale a jouer entre amis. Chaque joueur cree ou rejoint une session via un code court, repond aux questions a son propre rythme, et compare son score dans un classement de session ainsi que dans un classement global cumule.

Le frontend associe (Next.js) vit dans le depot separe quizbattle-frontend.

## Sommaire

- Fonctionnalites
- Stack technique
- Architecture
- Modele de donnees
- Points de terminaison de l'API
- Installation
- Deploiement
- Structure du projet

## Fonctionnalites

- Authentification par token via Laravel Sanctum (inscription, connexion, deconnexion)
- Creation de sessions de quiz par categorie, avec un nombre de questions configurable
- Categorie "Mix" tirant des questions parmi toutes les categories confondues
- Mode de jeu asynchrone : chaque joueur repond a son propre rythme
- Bareme de points pondere par le temps de reponse
- Classement par session et classement global cumule, calcules cote serveur
- API entierement stateless

## Stack technique

| Categorie | Choix | Justification |
|---|---|---|
| Langage | PHP 8.3 | Version stable, typage strict |
| Framework | Laravel 13 | Ecosysteme mature, Eloquent ORM, Sanctum |
| Authentification | Laravel Sanctum | Token leger adapte a une API separee du frontend |
| Base de donnees | PostgreSQL en production, SQLite en developpement | Robustesse en production, simplicite en local |
| Conteneurisation | Docker | Build reproductible, independant de l'environnement hote |
| Hebergement | Render | Deploiement continu depuis GitHub |

## Architecture

L'application suit une architecture REST en couches classique : routes, middleware d'authentification et CORS, controleurs, modeles Eloquent, base de donnees.

Chaque controleur reste concentre sur une seule responsabilite (authentification, categories, sessions de quiz, classements). Le calcul du score est centralise dans QuizSessionController, ce qui garantit un bareme coherent quel que soit le point d'entree.

## Modele de donnees

Tables principales :

- users : compte joueur (pseudo, email, mot de passe, avatar)
- categories : categories de questions, y compris la categorie speciale "Mix"
- questions : banque de questions (enonce, options JSON, index de la bonne reponse, difficulte)
- quiz_sessions : une partie identifiee par un code court, liee a une categorie
- session_questions : instantane des questions tirees pour une session donnee
- participations : participation d'un joueur a une session (score, statut termine ou non)
- answers : chaque reponse individuelle (option choisie, exactitude, temps de reponse, points)

Bareme de points : une reponse correcte rapporte entre 20 et 100 points selon le temps de reponse (100 moins 4 points par seconde ecoulee, avec un minimum de 20). Une reponse incorrecte ou absente ne rapporte rien.

## Points de terminaison de l'API

### Authentification

| Methode | Route | Auth | Description |
|---|---|---|---|
| POST | /api/register | Non | Inscription (pseudo, email, mot de passe, avatar) |
| POST | /api/login | Non | Connexion, renvoie un token Bearer |
| POST | /api/logout | Oui | Revocation du token courant |
| GET | /api/me | Oui | Profil du joueur authentifie |

### Categories et sessions

| Methode | Route | Auth | Description |
|---|---|---|---|
| GET | /api/categories | Oui | Liste des categories avec nombre de questions disponibles |
| POST | /api/quiz-sessions | Oui | Cree une session et renvoie un code de partage |
| POST | /api/quiz-sessions/{code}/join | Oui | Rejoint une session existante |
| POST | /api/quiz-sessions/{code}/answer | Oui | Soumet une reponse |
| POST | /api/quiz-sessions/{code}/finish | Oui | Marque la participation comme terminee |
| GET | /api/quiz-sessions/{code}/leaderboard | Oui | Classement d'une session |
| GET | /api/leaderboard/global | Oui | Classement global cumule |

Toute route authentifiee attend un header Authorization: Bearer suivi du token, obtenu via /register ou /login.

## Installation

Prerequis : PHP 8.3, Composer.

composer install
cp .env.example .env
php artisan key:generate

Configurer la connexion a la base de donnees dans .env, puis :

php artisan migrate --seed
php artisan serve

L'API est alors disponible sur http://localhost:8000/api. Le seeding fournit six categories et une trentaine de questions pretes a l'emploi.

## Deploiement

Le deploiement en production repose sur l'image Docker definie a la racine du projet (Dockerfile). Render reconstruit cette image a chaque push sur la branche principale, execute les migrations et le seeding au demarrage du conteneur, puis lance le serveur applicatif.

Variables d'environnement requises en production : APP_KEY, APP_ENV, APP_DEBUG, DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, FRONTEND_URL, SANCTUM_STATEFUL_DOMAINS.

## Structure du projet

app/Http/Controllers/Api : controleurs de l'API (Auth, Category, QuizSession, Leaderboard)
app/Models : modeles Eloquent et leurs relations
database/migrations : schema de la base de donnees
database/seeders : donnees de demonstration
routes/api.php : declaration des routes de l'API
Dockerfile : image de production utilisee par Render
