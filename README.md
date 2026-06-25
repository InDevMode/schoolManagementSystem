# School Management System

Système de gestion scolaire multi-établissements développé avec **Laravel 10**, **Vue 3**, **Inertia.js** et **Tailwind CSS**.

Conçu pour gérer plusieurs écoles depuis une seule plateforme — chaque établissement dispose de ses propres données (utilisateurs, classes, matières, évaluations, bulletins, frais...) isolées des autres.

---

## Table des matières

1. [Stack technique](#1-stack-technique)
2. [Prérequis](#2-prérequis)
3. [Installation](#3-installation)
4. [Configuration](#4-configuration)
5. [Base de données](#5-base-de-données)
6. [Lancer le projet](#6-lancer-le-projet)
7. [Comptes de démo](#7-comptes-de-démo)
8. [Architecture du projet](#8-architecture-du-projet)
9. [Fonctionnalités](#9-fonctionnalités)
10. [Rôles et permissions](#10-rôles-et-permissions)
11. [Structure des migrations](#11-structure-des-migrations)
12. [Structure des seeders](#12-structure-des-seeders)
13. [Variables d'environnement](#13-variables-denvironnement)
14. [Commandes artisan utiles](#14-commandes-artisan-utiles)
15. [Paiements en ligne](#15-paiements-en-ligne)
16. [Authentification sociale](#16-authentification-sociale)
17. [Emails](#17-emails)
18. [Contribuer](#18-contribuer)

---

## 1. Stack technique

| Couche | Technologie | Version |
|--------|-------------|---------|
| Backend | Laravel | 10.x |
| Frontend | Vue 3 + Inertia.js | Vue 3.5 / Inertia 2.0 |
| CSS | Tailwind CSS + Flowbite | 3.x |
| Build | Vite | 5.x |
| Base de données | MySQL | 8.0+ |
| Auth & RBAC | Laravel Sanctum + Spatie Permission | Sanctum 3.3 / Spatie 6.x |
| Exports | Maatwebsite Excel | 3.1 |
| Paiements | KkiaPay · Stripe · FedaPay · PayPal | — |
| Auth sociale | Laravel Socialite | 5.x |
| Éditeur riche | TipTap | 3.x |
| Langage | PHP 8.1+ / TypeScript | — |

---

## 2. Prérequis

Avant de cloner le projet, assurez-vous d'avoir installé :

- **PHP** ≥ 8.1 avec les extensions : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- **Composer** ≥ 2.x → [getcomposer.org](https://getcomposer.org)
- **Node.js** ≥ 18.x et **npm** ≥ 9.x → [nodejs.org](https://nodejs.org)
- **MySQL** ≥ 8.0 (ou MariaDB ≥ 10.6)
- **Git** → [git-scm.com](https://git-scm.com)

Vérifier les versions :

```bash
php --version
composer --version
node --version
npm --version
mysql --version
```

---

## 3. Installation

### Étape 1 — Cloner le dépôt

```bash
git clone <URL_DU_DEPOT> schoolManagementSystem
cd schoolManagementSystem
```

### Étape 2 — Installer les dépendances PHP

```bash
composer install
```

> Si vous êtes en production, utilisez `composer install --no-dev --optimize-autoloader`

### Étape 3 — Installer les dépendances JavaScript

```bash
npm install
```

### Étape 4 — Déchiffrer le fichier d'environnement

Ce projet ne contient pas de `.env.example`. Le fichier `.env` est distribué sous forme chiffrée (`.env.encrypted`) et versionné dans le dépôt.

Pour le déchiffrer, demandez la **clé de déchiffrement** au responsable du projet, puis exécutez :

```bash
php artisan env:decrypt --key=LA_CLE_FOURNIE_PAR_LE_RESPONSABLE
```

Cela génère le fichier `.env` à la racine du projet.

> **Important** : La clé de déchiffrement est **fixe** — c'est celle qui a servi à chiffrer le fichier `.env.encrypted`. Générer une nouvelle clé avec `php artisan key:generate` ne permettra **pas** de déchiffrer ce fichier. La clé du `.env.encrypted` est distincte de `APP_KEY`.

> La clé de déchiffrement doit être transmise de façon sécurisée (message privé chiffré, gestionnaire de secrets, etc.) — jamais dans le dépôt Git.

### Étape 5 — Vérifier la clé d'application

Après le déchiffrement, la variable `APP_KEY` est déjà présente dans le `.env` généré. Si elle est vide pour une raison quelconque, régénérez-la :

```bash
php artisan key:generate
```

### Étape 6 — Créer la base de données

Connectez-vous à MySQL et créez la base :

```sql
CREATE DATABASE schoolManagementSystem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Étape 7 — Configurer les variables d'environnement

Éditez le fichier `.env` et renseignez au minimum :

```env
APP_NAME=schoolManagementSystem
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=schoolManagementSystem
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### Étape 8 — Migrer et peupler la base de données

```bash
php artisan migrate --seed
```

Cette commande crée les 36 tables et insère toutes les données de démo en un seul appel.

### Étape 9 — Publier les assets Spatie Permission

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

> À faire **une seule fois** après le premier `composer install`. Le fichier `config/permission.php` est déjà présent dans le dépôt — cette commande met à jour les assets si la version du package change.

### Étape 10 — Créer le lien symbolique storage

```bash
php artisan storage:link
```

---

## 4. Configuration

### Fichier `.env` — paramètres essentiels

```env
# ── Application ──────────────────────────────────────────────
APP_NAME=schoolManagementSystem
APP_ENV=local          # local | production
APP_DEBUG=true         # false en production
APP_URL=http://127.0.0.1:8000

# ── Base de données ───────────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=schoolManagementSystem
DB_USERNAME=root
DB_PASSWORD=

# ── Email (SMTP Gmail) ────────────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_application
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# ── Paiements (optionnel) ─────────────────────────────────────
KKIAPAY_PUBLIC_KEY=
KKIAPAY_PRIVATE_KEY=
KKIAPAY_SECRET_KEY=
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=
FEDAPAY_PUBLIC_KEY=
FEDAPAY_SECRET_KEY=

# ── Auth sociale Google (optionnel) ───────────────────────────
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

> **Note Gmail** : Pour `MAIL_PASSWORD`, générez un **mot de passe d'application** depuis les paramètres de sécurité de votre compte Google (l'authentification à deux facteurs doit être activée).

---

## 5. Base de données

### Lancer les migrations et le seed

```bash
# Première installation
php artisan migrate --seed

# Repartir de zéro (développement uniquement — détruit toutes les données)
php artisan migrate:fresh --seed
```

### Lancer un seeder précis

```bash
php artisan db:seed --class=MultiSchoolSeeder
```

### Ordre des seeders

Les seeders sont numérotés pour garantir l'ordre des dépendances :

| # | Seeder | Contenu |
|---|--------|---------|
| 01 | `SettingsSeeder` | Configuration globale (id=1, requis par `periods`) |
| 02 | `SchoolSeeder` | 3 écoles de démo (Lycée, Collège, Primaire) |
| 03 | `RolesAndPermissionsSeeder` | 5 rôles + ~100 permissions Spatie |
| 04 | `SuperAdminSeeder` | Compte super administrateur global |
| 05 | `WeekSeeder` | 6 jours de la semaine (emploi du temps) |
| 06 | `LeaveTypesSeeder` | 7 types de congés par défaut |
| 07 | `MultiSchoolSeeder` | Admins, profs, élèves, parents + classes + matières |
| 08 | `PeriodsSeeder` | 3 trimestres 2025-2026 |
| 09 | `StaffAndEventsSeeder` | Fiches personnel, demandes de congés, événements |
| 10 | `EvaluationsSeeder` | Évaluations et notes de démo |

### Schéma simplifié des tables

```
schools ──┬── users ──┬── class_teacher
          │           ├── class_subject
          │           ├── attendances
          │           ├── feescollections
          │           ├── staff
          │           ├── grades
          │           └── bulletins
          │
settings ─┴── periods ──┬── exams ── schedules
                        ├── evaluations ── grades
                        └── bulletins ── bulletin_subjects

class ────┬── class_subject ── subject
          ├── class_teacher
          ├── class_timetable ── week
          └── works ── homework
                    └── work_attachments

leave_types ── staff_leaves ── staff
staff_events (indépendant)
communicates ── noticeboard_messages
chats, notifications, deletion_logs, marks_grade, marks_register
```

---

## 6. Lancer le projet

### Mode développement (2 terminaux)

**Terminal 1 — Serveur Laravel**
```bash
php artisan serve
```
> L'application est disponible sur `http://127.0.0.1:8000`

**Terminal 2 — Vite (hot reload)**
```bash
npm run dev
```

### Mode production

```bash
# Build des assets
npm run build

# Optimisations Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Vider le cache Spatie (après modification des permissions)
php artisan permission:cache-reset
```

### Scheduler (tâches planifiées)

Pour activer les commandes planifiées (ex : réinitialisation des évaluations, envoi d'emails) :

**Linux / Mac** — Ajouter au crontab :
```bash
* * * * * cd /chemin/vers/le/projet && php artisan schedule:run >> /dev/null 2>&1
```

**Windows** — Utiliser le fichier fourni :
```bash
run-scheduler.bat
```
Ou créer une tâche planifiée Windows pointant vers `php artisan schedule:run`.

---

## 7. Comptes de démo

Après `php artisan migrate --seed`, les comptes suivants sont disponibles :

### Super Administrateur (accès global à toutes les écoles)

| Champ | Valeur |
|-------|--------|
| Email | `superadmin@sms.local` |
| Mot de passe | `SuperAdmin@2025` |
| Rôle | `super_admin` |

> ⚠️ Changez ce mot de passe dès la première connexion.

### Administrateurs d'école

| École | Email | Mot de passe |
|-------|-------|--------------|
| Lycée Moderne de Cotonou | `admin@lmc.bj` | `Admin@LMC2025` |
| Collège Saint-Michel | `admin@csm.bj` | `Admin@CSM2025` |
| École Primaire Les Étoiles | `admin@epe.bj` | `Admin@EPE2025` |

### Autres utilisateurs (même mot de passe pour tous)

| Rôle | Exemple d'email | Mot de passe |
|------|-----------------|--------------|
| Professeur | `prof1@lmc.bj`, `prof2@lmc.bj`, ... | `Prof@1234` |
| Élève | `eleve1@lmc.bj`, `eleve2@lmc.bj`, ... | `Eleve@1234` |
| Parent | `parent1@lmc.bj`, `parent2@lmc.bj`, ... | `Parent@1234` |

> Les mêmes patterns s'appliquent aux écoles `@csm.bj` et `@epe.bj`.

---

## 8. Architecture du projet

```
schoolManagementSystem/
│
├── app/
│   ├── Console/
│   │   └── Commands/          # Commandes artisan personnalisées
│   │       ├── ResetIncompleteEvaluations.php
│   │       ├── SendNoticeBoardEmails.php
│   │       └── SyncSuperAdminPermissions.php
│   │
│   ├── Exports/               # Exports Excel (Maatwebsite)
│   │   ├── ExportStudent.php
│   │   ├── ExportTeacher.php
│   │   └── ...
│   │
│   ├── Http/
│   │   ├── Controllers/       # 26 contrôleurs métier
│   │   └── Middleware/        # AdminMiddleware, CheckPermission, CommonMiddleware...
│   │
│   └── Models/                # 25 modèles Eloquent
│
├── database/
│   ├── migrations/            # 36 migrations consolidées (une par table)
│   └── seeders/               # 10 seeders numérotés + DatabaseSeeder
│
├── resources/
│   ├── js/                    # Vue 3 + Inertia (composants, pages, stores Pinia)
│   └── views/                 # Layouts Blade (point d'entrée Inertia)
│
├── routes/
│   └── api.php                # Routes API (Sanctum)
│
├── config/
│   └── permission.php         # Configuration Spatie Laravel Permission
│
├── public/
│   └── upload/                # Fichiers uploadés (logos, avatars, documents)
│
└── storage/
    └── app/public/            # Lien symbolique via `php artisan storage:link`
```

### Flux de rendu

```
Requête HTTP
    → Laravel Router
    → Middleware (auth, CheckPermission...)
    → Controller (retourne Inertia::render())
    → Inertia.js transmet les props à Vue 3
    → Composant Vue rendu côté client
```

---

## 9. Fonctionnalités

### Gestion multi-écoles
- Création et gestion de plusieurs établissements depuis une interface super admin
- Isolation complète des données par école (`school_id` sur toutes les tables principales)
- Chaque école a ses propres clés de paiement, logo, favicon et configuration académique

### Gestion des utilisateurs
- 5 types : Super Admin, Admin, Professeur, Élève, Parent
- Création, modification, suppression douce (`is_delete`), réinitialisation de mot de passe
- Liaison parent ↔ élève
- Export Excel des listes

### Académique
- Gestion des classes et des matières
- Affectation des matières aux classes avec coefficients
- Affectation des professeurs aux classes
- Emploi du temps (par classe, jour, matière, salle, créneau horaire)

### Périodes & Examens
- 3 trimestres ou semestres configurables par école
- Planification des examens avec salles et horaires
- Registre de notes (marks_register — ancien système)

### Évaluations & Notes (nouveau système)
- Types : Interrogation, Devoir surveillé, Travail maison, Examen blanc
- Statuts : Brouillon → Ouvert → Fermé → Validé → Annulé
- Workflow de validation professeur → admin
- Coefficient par évaluation
- Score absent géré (null avec observation)

### Bulletins
- Génération automatique par élève par période
- Moyenne générale, rang, taux de réussite de classe
- Détail par matière (moyenne, points pondérés, appréciation)
- Publication (draft → published)

### Présences
- Saisie par classe et date
- Statuts : Présent, En retard, Absent, Demi-journée
- Rapports de présence

### Devoirs
- Création de travaux par classe et matière (avec pièces jointes multiples)
- Soumission par les élèves avec statut de suivi

### Frais de scolarité
- Suivi des paiements par élève et classe
- Montants total, versé, restant
- Intégration passerelles de paiement (KkiaPay, Stripe, FedaPay, PayPal)
- Statuts de paiement

### Communication
- Tableau d'affichage (noticeboard) avec ciblage par type d'utilisateur
- Envoi d'emails groupés
- Horodatage de l'envoi email

### Messagerie interne
- Chat temps réel entre utilisateurs
- Support des emojis (utf8mb4)
- Marquage lu/non-lu

### Ressources humaines (Personnel)
- Fiches personnel (directeur, enseignant, comptable, secrétaire...)
- Types de congés configurables
- Gestion des demandes de congés (en attente / approuvé / rejeté)
- Calendrier des événements scolaires

### RBAC (Contrôle d'accès basé sur les rôles)
- Interface d'administration des rôles et permissions
- Spatie Laravel Permission — permissions granulaires
- Attribution de permissions directement sur un utilisateur (en plus de son rôle)

### Authentification
- Connexion email + mot de passe
- Connexion sociale via Google (OAuth2)
- Réinitialisation de mot de passe obligatoire configurable
- Journalisation de la dernière connexion

### Journal de suppression
- Toute suppression est archivée dans `deletion_logs` avec snapshot des données
- Traçabilité complète (qui a supprimé quoi et quand)

---

## 10. Rôles et permissions

### Hiérarchie des rôles

```
super_admin (user_type = 0)
│   Accès total — gère toutes les écoles
│   Seul à pouvoir gérer les rôles et permissions (RBAC)
│
└── admin (user_type = 1)
│   Gère son école — crée et administre tous les utilisateurs
│
    ├── teacher (user_type = 2)
    │   Saisit les notes, Présences, devoirs, emploi du temps
    │
    ├── student (user_type = 3)
    │   Consulte ses notes, bulletin, devoirs, Présences
    │
    └── parent (user_type = 4)
        Suit les résultats et la scolarité de son enfant
```

### Permissions clés

| Catégorie | Exemple de permission |
|-----------|----------------------|
| Navigation | `view.dashboard.admin`, `view.bulletins.list` |
| Utilisateurs | `action.teachers.create`, `action.students.reset_password` |
| Académique | `action.classes.create`, `action.subjects.edit` |
| Évaluations | `action.exams.create`, `action.marks.manage` |
| Bulletins | `action.bulletins.generate`, `action.bulletins.publish` |
| Présences | `action.attendance.save` |
| Frais | `action.fees.collect`, `action.fees.delete` |
| Communication | `action.noticeboard.manage`, `action.mail.send` |
| Personnel | `action.staff.create`, `action.staff.leaves` |
| RBAC | `roles.view`, `permissions.assign` *(super_admin uniquement)* |
| Paramètres | `action.settings.manage` |

### Middleware de vérification

Les routes protégées utilisent `CheckPermission` :

```php
Route::middleware(['auth', 'checkPermission:action.classes.create'])->group(function () {
    // ...
});
```

---

## 11. Structure des migrations

Les migrations sont consolidées — **une seule migration par table**, avec la structure finale complète. Plus aucun fichier `add_column_to_*`.

```
database/migrations/
├── 2000_01_01_000001_create_password_reset_tokens_table.php
├── 2000_01_01_000002_create_failed_jobs_table.php
├── 2000_01_01_000003_create_personal_access_tokens_table.php
├── 2000_01_01_000004_create_schools_table.php          ← multi-tenant
├── 2000_01_01_000005_create_settings_table.php         ← rétrocompat
├── 2000_01_01_000006_create_users_table.php            ← dépend de schools
├── 2000_01_01_000007_create_permissions_tables.php     ← Spatie (roles, permissions...)
├── 2000_01_01_000008_create_class_table.php
├── 2000_01_01_000009_create_subject_table.php
├── 2000_01_01_000010_create_week_table.php
├── 2000_01_01_000011_create_class_subject_table.php    ← unique(class_id, subject_id)
├── 2000_01_01_000012_create_class_teacher_table.php
├── 2000_01_01_000013_create_class_timetable_table.php
├── 2000_01_01_000014_create_periods_table.php          ← dépend de settings
├── 2000_01_01_000015_create_exams_table.php            ← dépend de periods
├── 2000_01_01_000016_create_schedules_table.php
├── 2000_01_01_000017_create_marks_grade_table.php
├── 2000_01_01_000018_create_marks_register_table.php
├── 2000_01_01_000019_create_attendances_table.php
├── 2000_01_01_000020_create_communicates_table.php
├── 2000_01_01_000021_create_noticeboard_messages_table.php
├── 2000_01_01_000022_create_works_table.php
├── 2000_01_01_000023_create_work_attachments_table.php
├── 2000_01_01_000024_create_homework_table.php
├── 2000_01_01_000025_create_feescollections_table.php
├── 2000_01_01_000026_create_chats_table.php
├── 2000_01_01_000027_create_notifications_table.php
├── 2000_01_01_000028_create_leave_types_table.php
├── 2000_01_01_000029_create_staff_table.php
├── 2000_01_01_000030_create_staff_leaves_table.php
├── 2000_01_01_000031_create_staff_events_table.php
├── 2000_01_01_000032_create_evaluations_table.php      ← dépend de exams, periods
├── 2000_01_01_000033_create_grades_table.php           ← dépend de evaluations
├── 2000_01_01_000034_create_bulletins_table.php        ← dépend de periods
├── 2000_01_01_000035_create_bulletin_subjects_table.php
└── 2000_01_01_000036_create_deletion_logs_table.php
```

> **Note settings vs schools** : La table `settings` est conservée pour la rétrocompatibilité du code existant (`SettingModel`, `PeriodModel`). La table `schools` est la source de vérité pour le multi-tenant. Une migration future fusionnera les deux.

---

## 12. Structure des seeders

```
database/seeders/
├── DatabaseSeeder.php          ← Orchestrateur (appelle tous les seeders dans l'ordre)
├── 01_SettingsSeeder.php       ← settings id=1
├── 02_SchoolSeeder.php         ← 3 écoles de démo
├── 03_RolesAndPermissionsSeeder.php
├── 04_SuperAdminSeeder.php
├── 05_WeekSeeder.php
├── 06_LeaveTypesSeeder.php
├── 07_MultiSchoolSeeder.php    ← users + classes + matières
├── 08_PeriodsSeeder.php
├── 09_StaffAndEventsSeeder.php
└── 10_EvaluationsSeeder.php
```

Chaque seeder est **idempotent** : il vérifie l'existence avant d'insérer et peut être relancé sans créer de doublons.

---

## 13. Variables d'environnement

Liste complète des variables utilisées par le projet :

```env
# Application
APP_NAME=schoolManagementSystem
APP_ENV=local
APP_KEY=                          # généré par php artisan key:generate
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=schoolManagementSystem
DB_USERNAME=root
DB_PASSWORD=

# Cache / Session / Queue
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

# KkiaPay (paiement mobile Bénin/Afrique)
KKIAPAY_PUBLIC_KEY=
KKIAPAY_PRIVATE_KEY=
KKIAPAY_SECRET_KEY=

# Stripe (paiement international)
STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=

# FedaPay (paiement mobile Afrique)
FEDAPAY_PUBLIC_KEY=
FEDAPAY_SECRET_KEY=

# PayPal
PAYPAL_EMAIL=

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Facebook OAuth (optionnel)
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"

# Pusher (notifications temps réel — optionnel)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

---

## 14. Commandes artisan utiles

### Base de données

```bash
# Migrer + seeder en une commande (première installation)
php artisan migrate --seed

# Repartir de zéro (dev uniquement — supprime toutes les données)
php artisan migrate:fresh --seed

# Lancer un seeder précis
php artisan db:seed --class=MultiSchoolSeeder

# Vérifier l'état des migrations
php artisan migrate:status
```

### Cache

```bash
# Vider tous les caches
php artisan optimize:clear

# Vider uniquement le cache Spatie Permission (après modif rôles/permissions)
php artisan permission:cache-reset

# Reconstruire les caches en production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Commandes personnalisées

```bash
# Réinitialiser les évaluations incomplètes bloquées
php artisan evaluations:reset-incomplete

# Envoyer les emails du tableau d'affichage en attente
php artisan noticeboard:send-emails

# Synchroniser les permissions du super admin (après ajout de nouvelles permissions)
php artisan permissions:sync-super-admin
```

### Développement

```bash
# Lancer le serveur de développement
php artisan serve

# Lancer Vite (hot reload assets)
npm run dev

# Build de production
npm run build

# Vérification TypeScript
npm run type-check

# Ouvrir le REPL Laravel
php artisan tinker

# Lister toutes les routes
php artisan route:list

# Créer le lien symbolique storage → public
php artisan storage:link
```

---

## 15. Paiements en ligne

Le projet intègre 4 passerelles de paiement configurables par école.

### KkiaPay (paiement mobile — Bénin / Afrique de l'Ouest)

```env
KKIAPAY_PUBLIC_KEY=tpk_xxxxxxxxxxxx
KKIAPAY_PRIVATE_KEY=tpvk_xxxxxxxxxxxx
KKIAPAY_SECRET_KEY=xxxxxxxxxxxxxxxxxx
```

Obtenir les clés sur [kkiapay.me](https://kkiapay.me) → Tableau de bord → API Keys.

### FedaPay (paiement mobile — Afrique)

```env
FEDAPAY_PUBLIC_KEY=pk_live_xxxxxxxxxxxx
FEDAPAY_SECRET_KEY=sk_live_xxxxxxxxxxxx
```

Obtenir les clés sur [fedapay.com](https://fedapay.com) → Mon compte → API.

### Stripe (paiement international par carte)

```env
STRIPE_PUBLIC_KEY=pk_live_xxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_live_xxxxxxxxxxxx
```

Obtenir les clés sur [dashboard.stripe.com](https://dashboard.stripe.com) → Developers → API Keys.

### PayPal

```env
PAYPAL_EMAIL=votre_compte@paypal.com
```

> En mode développement, utilisez les clés **sandbox/test** de chaque passerelle pour ne pas débiter de vrais comptes.

---

## 16. Authentification sociale

### Google OAuth

1. Rendez-vous sur [console.cloud.google.com](https://console.cloud.google.com)
2. Créez un projet ou sélectionnez le vôtre
3. Menu **APIs & Services → Credentials → Create Credentials → OAuth 2.0 Client ID**
4. Type d'application : **Web application**
5. Ajoutez l'URI de redirection autorisée : `http://127.0.0.1:8000/auth/google/callback`
6. Copiez le **Client ID** et le **Client Secret** dans le `.env` :

```env
GOOGLE_CLIENT_ID=594636074255-xxxxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxx
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

> En production, remplacez `http://127.0.0.1:8000` par votre domaine réel et mettez à jour l'URI dans la console Google.

### Facebook OAuth (optionnel)

1. Rendez-vous sur [developers.facebook.com](https://developers.facebook.com)
2. Créez une application de type **Consumer**
3. Ajoutez le produit **Facebook Login**
4. URI de redirection : `http://votre-domaine.com/auth/facebook/callback`
5. Copiez l'**App ID** et l'**App Secret** :

```env
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"
```

---

## 17. Emails

Le projet utilise SMTP pour l'envoi des emails (tableau d'affichage, notifications, réinitialisation de mot de passe).

### Configuration Gmail (recommandée pour le développement)

1. Activez la **validation en deux étapes** sur votre compte Google
2. Allez dans **Compte Google → Sécurité → Mots de passe des applications**
3. Créez un mot de passe d'application pour "Autre (nom personnalisé)" → `SMS`
4. Utilisez ce mot de passe de 16 caractères dans le `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx    # mot de passe d'application (sans espaces)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Tester l'envoi d'email

```bash
php artisan tinker
>>> Mail::raw('Test SMS', fn($m) => $m->to('test@exemple.com')->subject('Test'));
```

### Mode log (développement sans SMTP)

Pour voir les emails dans les logs sans les envoyer :

```env
MAIL_MAILER=log
```

Les emails apparaîtront dans `storage/logs/laravel.log`.

---

## 18. Contribuer

### Processus de contribution

1. **Forkez** le dépôt
2. Créez une branche depuis `main` :
   ```bash
   git checkout -b feature/ma-fonctionnalite
   ```
3. Développez et committez vos changements :
   ```bash
   git add .
   git commit -m "feat: description claire de la fonctionnalité"
   ```
4. Poussez votre branche :
   ```bash
   git push origin feature/ma-fonctionnalite
   ```
5. Ouvrez une **Pull Request** vers `main`

### Conventions

- **Migrations** : une seule migration par table avec la structure finale complète — pas de fichiers `add_column_to_*`
- **Seeders** : numérotés (`01_`, `02_`...), idempotents (vérifier avant d'insérer), sans données dépendantes dans les migrations
- **Nommage** : `snake_case` pour les colonnes, `PascalCase` pour les classes PHP, `camelCase` pour les variables JS/TS
- **Commits** : respecter le format [Conventional Commits](https://www.conventionalcommits.org/fr) (`feat:`, `fix:`, `refactor:`, `docs:`)

### Ajouter une nouvelle table

1. Créer une migration consolidée :
   ```bash
   # Nommer manuellement en continuant la numérotation 2000_01_01_0000XX
   # Ex : 2000_01_01_000037_create_ma_table.php
   ```
2. Créer le modèle Eloquent dans `app/Models/`
3. Créer le seeder correspondant dans `database/seeders/` avec la numérotation suivante
4. L'ajouter dans `DatabaseSeeder.php` au bon endroit dans la chaîne
5. Créer le contrôleur dans `app/Http/Controllers/`

### Ajouter une permission

1. Ajouter le nom dans `03_RolesAndPermissionsSeeder.php` → tableau `ALL_PERMISSIONS`
2. L'assigner au(x) rôle(s) concerné(s) dans le même seeder
3. Relancer le seeder : `php artisan db:seed --class=RolesAndPermissionsSeeder`
4. Puis resynchroniser le super admin : `php artisan permissions:sync-super-admin`

---

## Résolution de problèmes fréquents

### `php artisan migrate` échoue avec une erreur de clé étrangère

Vérifiez que la table parente existe. Avec les nouvelles migrations consolidées, l'ordre `2000_01_01_0000XX` garantit les dépendances. Si vous avez d'anciennes migrations en base, faites :
```bash
php artisan migrate:fresh --seed
```

### `Class "Spatie\Permission\..." not found`

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan config:clear
```

### Page blanche / erreur 500 après `npm run build`

```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
npm run build
```

### Les permissions ne se chargent pas correctement

```bash
php artisan permission:cache-reset
php artisan config:clear
```

### Erreur `SQLSTATE[42S01]: Table already exists`

La base de données contient des anciennes tables. Utilisez :
```bash
php artisan migrate:fresh --seed
```
> ⚠️ Cette commande **supprime toutes les données**. À n'utiliser qu'en développement.

### Erreur d'upload de fichier (logo, avatar)

Vérifiez que le lien symbolique est créé et que le dossier `upload/` a les bonnes permissions :
```bash
php artisan storage:link
# Windows
icacls "public\upload" /grant "Everyone:(OI)(CI)F"
# Linux/Mac
chmod -R 775 public/upload storage
```

---

## Licence

Ce projet est sous licence **MIT**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

*Développé avec ❤️ — Laravel 10 · Vue 3 · Inertia.js · Tailwind CSS*
