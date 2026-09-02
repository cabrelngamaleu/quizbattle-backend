# QuizBattle — API Backend

API REST développée en Laravel pour QuizBattle, une application de quiz de culture générale conçue pour être jouée entre amis en mode asynchrone : chaque joueur répond aux questions à son propre rythme, et un système de classement (par session et global) permet de comparer les scores.

Ce dépôt contient uniquement le backend. Le frontend associé (Next.js) se trouve dans le dépôt quizbattle-frontend.

## Stack technique

- Langage : PHP 8.3
- Framework : Laravel 13
- Authentification : Laravel Sanctum (tokens API)
- Base de données : PostgreSQL en production, SQLite en développement local
- Conteneurisation : Docker (image personnalisée avec extensions PHP requises)
- Hébergement : Render

## Architecture et choix techniques

L'application suit une architecture API REST classique en couches (routes, contrôleurs, modèles Eloquent), sans logique métier dans les contrôleurs au-delà de ce qui est nécessaire à des endpoints simples.

Modèle de données :
- users : compte joueur (pseudo, email, mot de passe, avatar)
- categories : catégories de questions, y compris une catégorie "Mix" qui pioche des questions dans toutes les catégories
- questions : banque de questions avec options de réponse au format JSON et index de la bonne réponse
- quiz_sessions : une session de jeu identifiée par un code court, associée à une catégorie et à un nombre de questions
- session_questions : instantané des questions exactes tirées pour une session donnée, pour que le classement reste cohérent même si la banque de questions évolue par la suite
- participations : la participation d'un joueur à une session, avec son score et son statut (terminé ou non)
- answers : chaque réponse individuelle donnée par un joueur, avec le temps de réponse et les points obtenus

Le barème de points favorise la rapidité : une réponse correcte rapporte entre 20 et 100 points selon le temps de réponse, une réponse incorrecte ou absente ne rapporte rien.

## Démarche de développement

Le projet a été conçu et développé entièrement depuis un terminal Android (Termux), sans poste de travail traditionnel disponible. Cette contrainte a nécessité de résoudre plusieurs problèmes d'environnement rarement rencontrés dans un contexte de développement standard :

- Contraintes de compatibilité de versions entre PHP local et l'image Docker de production, résolues en retirant composer.lock du contrôle de version pour laisser chaque environnement résoudre ses propres dépendances compatibles
- Incompatibilité du serveur de développement PHP intégré avec le système de verrouillage de fichiers d'Android, contournée en désactivant OPcache pour l'exécution locale
- Mise en place d'un pipeline de build Docker complet (extensions PHP, dépendances système) pour un déploiement reproductible sur Render, indépendant de l'environnement de développement local

## Installation locale

Prérequis : PHP 8.3, Composer.

    composer install
    cp .env.example .env
    php artisan key:generate

Configurer la connexion à la base de données dans .env, puis :

    php artisan migrate --seed
    php artisan serve

L'API est disponible sur http://localhost:8000/api.

## Déploiement

Le déploiement en production utilise l'image Docker définie dans le Dockerfile à la racine du projet. Render construit cette image à chaque push sur la branche principale, exécute les migrations et le seeding automatiquement au démarrage du conteneur, puis lance le serveur.

Variables d'environnement requises en production : APP_KEY, APP_ENV, APP_DEBUG, DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD, FRONTEND_URL, SANCTUM_STATEFUL_DOMAINS.

## Points de terminaison de l'API

| Méthode | Route | Authentification | Description |
|---|---|---|---|
| POST | /api/register | Non | Inscription (pseudo, email, mot de passe, avatar) |
| POST | /api/login | Non | Connexion |
| POST | /api/logout | Oui | Déconnexion |
| GET | /api/me | Oui | Profil du joueur connecté |
| GET | /api/categories | Oui | Liste des catégories disponibles |
| POST | /api/quiz-sessions | Oui | Création d'une session de quiz |
| POST | /api/quiz-sessions/{code}/join | Oui | Rejoindre une session via son code |
| POST | /api/quiz-sessions/{code}/answer | Oui | Soumission d'une réponse |
| POST | /api/quiz-sessions/{code}/finish | Oui | Fin de la participation d'un joueur |
| GET | /api/quiz-sessions/{code}/leaderboard | Oui | Classement d'une session |
| GET | /api/leaderboard/global | Oui | Classement global cumulé |

L'authentification se fait par token Bearer (header Authorization) obtenu via /register ou /login.
