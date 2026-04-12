# Salonify — Plateforme de Gestion de Salons de Coiffure

> Application web complète de réservation et de gestion de salons de coiffure au Maroc, développée avec Laravel 11 et PHP 8.2.

---

## Sommaire

- [Aperçu du projet](#aperçu-du-projet)
- [Fonctionnalités](#fonctionnalités)
- [Architecture & Rôles](#architecture--rôles)
- [Technologies utilisées](#technologies-utilisées)
- [Modèle de données](#modèle-de-données)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Comptes de test](#comptes-de-test)
- [Structure du projet](#structure-du-projet)
- [Routes principales](#routes-principales)
- [Problèmes fréquents](#problèmes-fréquents)

---

## Aperçu du projet

**Salonify** est une plateforme multi-rôles qui met en relation des clients avec des salons de coiffure au Maroc. Les clients peuvent rechercher un salon par ville, consulter les services et les avis, puis réserver un créneau en quelques étapes. Les gérants de salon gèrent leur espace depuis un tableau de bord dédié. Un administrateur supervise l'ensemble de la plateforme.

Ce projet a été réalisé dans le cadre d'un **projet tuteuré** à l'ISGA.

---

## Fonctionnalités

### Espace Client
- Inscription / Connexion (formulaire ou **Google OAuth**)
- Vérification d'adresse e-mail obligatoire
- Recherche de salons par ville et quartier
- Consultation des fiches salon (services, horaires, note, avis)
- **Processus de réservation en 3 étapes** : choix du service → choix de l'employé → choix du créneau
- Annulation d'une réservation (jusqu'à 24h avant)
- Dépôt d'un avis après un rendez-vous terminé
- Tableau de bord personnel (réservations à venir, historique)
- Notifications en temps réel (confirmation, rappels 24h et 2h)
- Gestion du profil et du mot de passe

### Espace Salon (Gérant)
- Tableau de bord avec statistiques (réservations du jour, CA, note moyenne)
- Gestion du profil salon (photo, description, horaires, coordonnées GPS)
- Gestion des **services** (nom, catégorie, prix en MAD, durée, activation/désactivation)
- Gestion des **employés** (activation/désactivation)
- Gestion des **réservations** (confirmation, marquage terminé, annulation)
- Gestion des **disponibilités** (blocage de créneaux)
- Réponse aux avis clients et signalement d'avis abusifs

### Espace Admin
- Tableau de bord global avec statistiques plateforme
- Validation / suspension / suppression de salons
- Gestion complète des utilisateurs (CRUD)
- Gestion des villes disponibles
- Modération des avis (approbation / suppression)
- Export des statistiques

---

## Architecture & Rôles

L'application repose sur **trois rôles distincts** avec des espaces et des middlewares séparés :

| Rôle    | Préfixe URL  | Middleware         |
|---------|--------------|--------------------|
| Client  | `/client/`   | `auth`, `verified` |
| Salon   | `/salon/`    | `auth`, `role:salon` |
| Admin   | `/admin/`    | `auth`, `role:admin` |

---

## Technologies utilisées

| Couche         | Technologie                        |
|----------------|------------------------------------|
| Backend        | PHP 8.2 / Laravel 11               |
| Authentification | Laravel Sanctum + Google OAuth (Socialite) |
| Base de données | MySQL 8.0                         |
| Templates      | Blade (Laravel)                    |
| Emails         | SMTP (Mailtrap pour les tests)     |
| Stockage       | Laravel Storage (fichiers locaux)  |
| Tests          | PHPUnit 11                         |
| Serveur local  | XAMPP (Apache + MySQL)             |

---

## Modèle de données

Le schéma de base de données comporte **8 tables principales** :

```
villes          — Villes du Maroc disponibles sur la plateforme
users           — Tous les utilisateurs (clients, gérants, admin)
salons          — Salons de coiffure (géré par un user de rôle salon)
employes        — Employés rattachés à un salon
services        — Services proposés par un salon (coupe, coloration…)
reservations    — Réservations (client × salon × service × employé)
avis            — Avis laissés par les clients après une réservation
notifications   — Notifications in-app (confirmation, rappels…)
```

**Relations clés :**
- Un `Salon` appartient à une `Ville` et à un `User`
- Un `Salon` possède plusieurs `Services`, `Employes`, `Reservations`, `Avis`
- Une `Reservation` lie un `User` (client), un `Salon`, un `Service` et un `Employe`
- Un `Avis` est rattaché à une `Reservation` (un avis par réservation terminée)

---

## Prérequis

Avant d'installer le projet, assure-toi d'avoir installé :

| Logiciel   | Version minimale | Lien de téléchargement |
|------------|-----------------|------------------------|
| XAMPP      | 8.2+            | https://www.apachefriends.org |
| PHP        | 8.2+            | Inclus dans XAMPP |
| MySQL      | 8.0+            | Inclus dans XAMPP |
| Composer   | 2.x             | https://getcomposer.org |
| Git        | Dernière        | https://git-scm.com |

Un compte **Mailtrap** gratuit est également nécessaire pour les e-mails de test :
https://mailtrap.io

---

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/ASMAE-DEV-SE/GestionSaonCoiffure.git
cd GestionSaonCoiffure/salonify
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Créer le fichier de configuration

```bash
cp .env.example .env
```

### 4. Générer la clé d'application

```bash
php artisan key:generate
```

### 5. Créer la base de données

Dans phpMyAdmin (`http://localhost/phpmyadmin`) :
- Créer une nouvelle base de données : `salonify`
- Interclassement : `utf8mb4_unicode_ci`

### 6. Lancer les migrations et les seeders

```bash
php artisan migrate --seed
```

### 7. Créer le lien symbolique pour le stockage

```bash
php artisan storage:link
```

### 8. Démarrer le serveur de développement

```bash
php artisan serve --port=8000
```

L'application est accessible sur : **http://localhost:8000**

---

## Configuration

Modifier le fichier `.env` avec tes propres paramètres :

```env
# Application
APP_NAME=Salonify
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salonify
DB_USERNAME=root
DB_PASSWORD=          # Laisser vide si XAMPP sans mot de passe

# Emails (Mailtrap)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=VOTRE_USERNAME_MAILTRAP
MAIL_PASSWORD=VOTRE_PASSWORD_MAILTRAP
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@salonify.ma"
MAIL_FROM_NAME="Salonify"

# Google OAuth (optionnel)
GOOGLE_CLIENT_ID=VOTRE_CLIENT_ID
GOOGLE_CLIENT_SECRET=VOTRE_CLIENT_SECRET
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

## Comptes de test

Après avoir exécuté `php artisan migrate --seed`, les comptes suivants sont disponibles :

| Rôle   | Email                  | Mot de passe |
|--------|------------------------|--------------|
| Admin  | admin@salonify.ma      | password     |
| Salon  | salon@salonify.ma      | password     |
| Client | client@salonify.ma     | password     |

---

## Structure du projet

```
salonify/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/       # DashboardController, SalonController, UserController...
│   │   │   ├── Auth/        # Login, Register, Google OAuth, Email Verification...
│   │   │   ├── Client/      # Home, Salon, Reservation, Avis, Profile...
│   │   │   └── Salon/       # Dashboard, Service, Employe, Disponibilite...
│   │   └── Middleware/      # Middleware de rôle (role:admin, role:salon)
│   ├── Models/
│   │   ├── Avis.php
│   │   ├── Employe.php
│   │   ├── Notification.php
│   │   ├── Reservation.php
│   │   ├── Salon.php
│   │   ├── Service.php
│   │   ├── User.php
│   │   └── Ville.php
│   ├── Mail/                # Classes d'e-mails (confirmation, rappel, etc.)
│   └── Services/            # Services métier
├── database/
│   ├── migrations/          # 10 migrations (villes → notifications)
│   └── seeders/             # Données de test
├── resources/
│   └── views/
│       ├── admin/           # Vues espace administrateur
│       ├── auth/            # Connexion, inscription, reset password
│       ├── client/          # Accueil, salons, réservations, profil
│       ├── salon/           # Tableau de bord gérant
│       ├── emails/          # Templates d'e-mails
│       └── layouts/         # Layouts partagés (Blade)
├── routes/
│   └── web.php              # Toutes les routes (public, auth, client, salon, admin)
├── public/                  # Assets publics (CSS, JS, images)
├── storage/                 # Fichiers uploadés, logs, sessions
├── .env.example             # Modèle de configuration
└── composer.json            # Dépendances PHP
```

---

## Routes principales

### Publiques
| Méthode | URL                         | Description                  |
|---------|-----------------------------|------------------------------|
| GET     | `/`                         | Page d'accueil               |
| GET     | `/salons/{ville}`           | Liste des salons d'une ville |
| GET     | `/salons/{ville}/{slug}`    | Fiche détail d'un salon      |
| GET     | `/villes`                   | Liste des villes             |
| GET/POST| `/contact`                  | Formulaire de contact        |

### Authentification
| Méthode | URL                        | Description               |
|---------|----------------------------|---------------------------|
| GET/POST| `/connexion`               | Connexion                 |
| GET/POST| `/inscription`             | Inscription               |
| GET     | `/auth/google`             | Connexion via Google      |
| GET/POST| `/mot-de-passe-oublie`     | Réinitialisation du mot de passe |

### Client (authentifié + email vérifié)
| Méthode | URL                                  | Description              |
|---------|--------------------------------------|--------------------------|
| GET     | `/client/dashboard`                  | Tableau de bord client   |
| GET     | `/reservations/{salon}/step1`        | Étape 1 — Choisir service |
| GET     | `/reservations/{salon}/step2`        | Étape 2 — Choisir employé |
| GET     | `/reservations/{salon}/step3`        | Étape 3 — Choisir créneau |
| POST    | `/reservations/{salon}`              | Confirmer la réservation |
| POST    | `/reservations/{id}/annuler`         | Annuler une réservation  |
| GET/POST| `/avis/create/{reservation}`         | Laisser un avis          |

### Salon (rôle gérant)
| Méthode  | URL                                   | Description              |
|----------|---------------------------------------|--------------------------|
| GET      | `/salon/dashboard`                    | Tableau de bord salon    |
| GET/POST | `/salon/services`                     | Gestion des services     |
| GET/POST | `/salon/employes`                     | Gestion des employés     |
| GET      | `/salon/reservations`                 | Liste des réservations   |
| POST     | `/salon/reservations/{id}/confirmer`  | Confirmer un RDV         |
| GET      | `/salon/disponibilites`               | Gestion des disponibilités |

### Admin
| Méthode | URL                              | Description               |
|---------|----------------------------------|---------------------------|
| GET     | `/admin/dashboard`               | Tableau de bord admin     |
| GET     | `/admin/salons`                  | Gestion des salons        |
| POST    | `/admin/salons/{id}/valider`     | Valider un salon          |
| POST    | `/admin/salons/{id}/suspendre`   | Suspendre un salon        |
| GET     | `/admin/users`                   | Gestion des utilisateurs  |
| GET     | `/admin/statistiques`            | Statistiques plateforme   |
| GET     | `/admin/statistiques/export`     | Export des statistiques   |

---

## Problèmes fréquents

**`php` n'est pas reconnu dans le terminal**
> Ajouter `C:\xampp\php` à la variable d'environnement PATH Windows, puis redémarrer le terminal.

**`SQLSTATE[HY000] [2002] Connection refused`**
> MySQL n'est pas démarré. Ouvrir XAMPP Control Panel et démarrer MySQL.

**`Class not found` après une modification**
> Exécuter `composer install` puis `php artisan config:clear && php artisan cache:clear`.

**`The stream or file storage/logs could not be opened`**
> Sous Windows, faire un clic droit sur le dossier `storage` → Propriétés → Sécurité → accorder le contrôle total à l'utilisateur courant.

**Les images uploadées ne s'affichent pas**
> Vérifier que `php artisan storage:link` a bien été exécuté.

---

## Auteur

Projet réalisé par **ASMAE** dans le cadre d'un projet tuteuré à l'**ISGA**.

---

*Application développée avec [Laravel](https://laravel.com) 11 — PHP 8.2*
