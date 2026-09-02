# School Management System

Système de gestion scolaire multi-établissements développé avec **Laravel 10**, **Vue 3**, **Inertia.js** et **Tailwind CSS**.

Conçu pour héberger plusieurs écoles depuis une seule plateforme — chaque établissement dispose de ses propres données isolées (utilisateurs, classes, matières, évaluations, bulletins, frais) via un champ `school_id` (UUID) présent sur toutes les tables principales.

---

## Table des matières

1. [Stack technique](#1-stack-technique)
2. [Prérequis](#2-prérequis)
3. [Installation locale](#3-installation-locale)
4. [Configuration Supabase (PostgreSQL)](#4-configuration-supabase-postgresql)
5. [Base de données — migrations et seed](#5-base-de-données--migrations-et-seed)
6. [Lancer le projet](#6-lancer-le-projet)
7. [Comptes de démo](#7-comptes-de-démo)
8. [Architecture du projet](#8-architecture-du-projet)
9. [Fonctionnalités](#9-fonctionnalités)
10. [Rôles et permissions](#10-rôles-et-permissions)
11. [Structure des migrations](#11-structure-des-migrations)
12. [Structure des seeders](#12-structure-des-seeders)
13. [Variables d'environnement](#13-variables-denvironnement)
14. [Commandes artisan utiles](#14-commandes-artisan-utiles)
15. [Déploiement (Railway + Supabase)](#15-déploiement-railway--supabase)
16. [Paiements en ligne](#16-paiements-en-ligne)
17. [Authentification sociale](#17-authentification-sociale)
18. [Emails](#18-emails)
19. [Résolution de problèmes fréquents](#19-résolution-de-problèmes-fréquents)

---

## 1. Stack technique

| Couche | Technologie | Version |
|--------|-------------|---------|
| Backend | Laravel | 10.x |
| Frontend | Vue 3 + Inertia.js | Vue 3.5 / Inertia 2.0 |
| CSS | Tailwind CSS + Flowbite | 3.x |
| Build | Vite | 5.x |
| Base de données | **PostgreSQL** (Supabase) | 15+ |
| Identifiants | **UUID v4** sur toutes les tables | — |
| Auth & RBAC | Spatie Laravel Permission | 6.x |
| Exports | Maatwebsite Excel | 3.1 |
| Paiements | KkiaPay · Stripe · FedaPay · PayPal | — |
| Auth sociale | Laravel Socialite | 5.x |
| Éditeur riche | TipTap | 3.x |
| Langage | PHP 8.1+ / TypeScript | — |

> **Important** : ce projet utilise **PostgreSQL** exclusivement. MySQL n'est plus supporté depuis la migration vers UUID v4.

---

## 2. Prérequis

- **PHP** ≥ 8.1 avec les extensions : `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- **Composer** ≥ 2.x → [getcomposer.org](https://getcomposer.org)
- **Node.js** ≥ 18.x et **npm** ≥ 9.x → [nodejs.org](https://nodejs.org)
- **Git** → [git-scm.com](https://git-scm.com)
- Un projet **Supabase** (gratuit) → [supabase.com](https://supabase.com)

Vérifier les versions :

```bash
php --version
composer --version
node --version
npm --version
```

---

## 3. Installation locale

### Étape 1 — Cloner le dépôt

```bash
git clone <URL_DU_DEPOT> schoolManagementSystem
cd schoolManagementSystem
```

### Étape 2 — Installer les dépendances PHP

```bash
composer install
```

En production :
```bash
composer install --no-dev --optimize-autoloader
```

### Étape 3 — Installer les dépendances JavaScript

```bash
npm install
```

### Étape 4 — Créer le fichier d'environnement

Copiez le fichier d'exemple :

```bash
cp .env.example .env
```

Puis générez la clé d'application :

```bash
php artisan key:generate
```

### Étape 5 — Configurer la connexion Supabase

Éditez `.env` avec les informations de votre base Supabase (voir [section 4](#4-configuration-supabase-postgresql)).

### Étape 6 — Migrer et peupler la base

```bash
php artisan migrate:fresh --seed
```

### Étape 7 — Créer le lien symbolique storage

```bash
php artisan storage:link
```

---

## Configuration Supabase Storage

### 1. Créer le bucket

Dans ton projet Supabase → **Storage** → **New bucket** :
- Nom : `schoolms`
- Accès : **Public**

### 2. Obtenir les clés S3

Supabase Storage expose une API S3-compatible. Dans **Settings → Storage → S3 Connection** :
- Copie **Access Key ID** → `SUPABASE_S3_KEY`
- Copie **Secret Access Key** → `SUPABASE_S3_SECRET`

### 3. Renseigner le `.env`

```env
FILESYSTEM_DISK=supabase

SUPABASE_S3_KEY=ton_access_key_id
SUPABASE_S3_SECRET=ton_secret_access_key
SUPABASE_S3_REGION=eu-central-1
SUPABASE_S3_BUCKET=schoolms
SUPABASE_S3_ENDPOINT=https://ymyyjryfauneudznxetb.supabase.co/storage/v1/s3
SUPABASE_STORAGE_URL=https://ymyyjryfauneudznxetb.supabase.co/storage/v1/object/public
```

### Organisation des dossiers dans le bucket

| Dossier | Contenu |
|---------|---------|
| `profiles/` | Photos de profil (admins, profs, apprenants, parents) |
| `schools/` | Logos et favicons des écoles |
| `settings/` | Logo, favicon et backgrounds de la page de connexion |
| `works/` | Pièces jointes des devoirs (admin/prof) |
| `homeworks/` | Soumissions des apprenants |
| `chats/` | Fichiers partagés dans le chat |

---

### Obtenir les informations de connexion

1. Connectez-vous sur [supabase.com](https://supabase.com)
2. Ouvrez votre projet → **Settings → Database**
3. Dans la section **Connection string**, choisissez **Direct connection** (pas le pooler — important pour les migrations)
4. Copiez les paramètres

### Variables `.env` à renseigner

```env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe_supabase
DB_SSLMODE=require
```

> **DB_SSLMODE=require** est obligatoire sur Supabase en production. En développement local avec PostgreSQL, utilisez `prefer`.

### Pooler Supabase (pour l'application en production)

En production sur Railway ou Render, utilisez le pooler PgBouncer de Supabase pour les connexions applicatives (port 6543) :

```env
DB_HOST=aws-0-xx-xxxx-x.pooler.supabase.com
DB_PORT=6543
DB_USERNAME=postgres.xxxxxxxxxxxxxx
```

> Pour les migrations (`php artisan migrate`), utilisez toujours la connexion directe (port 5432), pas le pooler.

---

## 5. Base de données — migrations et seed

### Lancer les migrations et le seed

```bash
# Première installation — crée toutes les tables et insère les données de démo
php artisan migrate:fresh --seed

# Ajouter de nouvelles migrations sans réinitialiser
php artisan migrate --seed
```

### Lancer un seeder précis

```bash
php artisan db:seed --class=EvaluationsSeeder
```

### Ordre des seeders

| # | Seeder | Contenu |
|---|--------|---------|
| 1 | `SettingsSeeder` | Configuration globale (bigInt id=1, référencé par `periods.settings_id`) |
| 2 | `SchoolSeeder` | 3 écoles de démo avec UUIDs fixes |
| 3 | `RolesAndPermissionsSeeder` | 5 rôles + ~100 permissions Spatie |
| 4 | `SuperAdminSeeder` | Compte super administrateur (UUID fixe) |
| 5 | `WeekSeeder` | 6 jours de la semaine avec UUIDs fixes |
| 6 | `LeaveTypesSeeder` | 7 types de congés par défaut |
| 7 | `MultiSchoolSeeder` | Admins, profs, apprenants, parents + classes + matières + emploi du temps |
| 8 | `PeriodsSeeder` | 3 trimestres 2025-2026 (globaux + scopés par école) |
| 9 | `StaffAndEventsSeeder` | Fiches RH, demandes de congés, événements scolaires |
| 10 | `EvaluationsSeeder` | Évaluations, notes, bulletins, présences, frais, devoirs, annonces |

Chaque seeder est **idempotent** : il vérifie l'existence avant d'insérer et peut être relancé sans créer de doublons.

### Note sur les UUIDs dans les seeders

Toutes les insertions via `DB::table()->insert()` incluent un UUID explicite :

```php
DB::table('class')->insert([
    'id'        => (string) Str::uuid(),
    'school_id' => SchoolSeeder::LMC_ID,
    // ...
]);
```

Les insertions via le modèle Eloquent (`User::firstOrCreate()`) bénéficient du trait `HasUuids` automatiquement.

---

## 6. Lancer le projet

### Mode développement (2 terminaux)

**Terminal 1 — Serveur Laravel**
```bash
php artisan serve
```
L'application est disponible sur `http://127.0.0.1:8000`

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

**Linux / Mac** — Ajouter au crontab :
```bash
* * * * * cd /chemin/vers/le/projet && php artisan schedule:run >> /dev/null 2>&1
```

**Windows** — Exécuter périodiquement :
```bash
php artisan schedule:run
```
Ou créer une tâche planifiée Windows pointant vers ce script.

---

## 7. Comptes de démo

Après `php artisan migrate:fresh --seed` :

### Super Administrateur (accès global à toutes les écoles)

| Champ | Valeur |
|-------|--------|
| Email | `schoolmanagementsystem00@gmail.com` |
| Mot de passe | `SuperAdmin@2025` |
| Rôle | `super_admin` |

> ⚠️ Changez ce mot de passe dès la première connexion.

### Administrateurs d'école

| École | Email | Mot de passe |
|-------|-------|--------------|
| Lycée Moderne de Cotonou | `admin@lmc.bj` | `Admin@LMC2025` |
| Collège Saint-Michel | `admin@csm.bj` | `Admin@CSM2025` |
| École Primaire Les Étoiles | `admin@epe.bj` | `Admin@EPE2025` |

### Autres utilisateurs

| Rôle | Exemple d'email | Mot de passe |
|------|-----------------|--------------|
| Professeur | `prof1@lmc.bj`, `prof1@csm.bj`, `prof1@epe.bj` | `Prof@1234` |
| Apprenant | `eleve1@lmc.bj`, `eleve2@lmc.bj`, ... | `Eleve@1234` |
| Parent | `parent1@lmc.bj`, `parent2@lmc.bj`, ... | `Parent@1234` |

---

## 8. Architecture du projet

```
schoolManagementSystem/
│
├── app/
│   ├── Console/Commands/        # 3 commandes artisan personnalisées
│   ├── Exports/                 # 7 exporteurs Excel (Maatwebsite)
│   ├── Http/
│   │   ├── Controllers/         # 28 contrôleurs métier
│   │   └── Middleware/          # AdminMiddleware, CheckPermission, EnsureSchoolActive...
│   └── Models/                  # 27 modèles Eloquent (tous avec HasUuids sauf SettingModel)
│
├── database/
│   ├── migrations/              # 37 migrations consolidées (une par table, UUID v4)
│   └── seeders/                 # 10 seeders + DatabaseSeeder
│
├── resources/
│   ├── js/                      # Vue 3 + Inertia + TypeScript (Pages, Components, Composables)
│   └── views/                   # app.blade.php (shell Inertia) + bulletin_print.blade.php
│
├── routes/
│   └── web.php                  # Toutes les routes groupées par rôle
│
├── config/
│   ├── database.php             # Connexion pgsql par défaut
│   └── permission.php           # model_morph_key = 'model_uuid' (UUID Spatie)
│
└── public/upload/               # Fichiers uploadés (logos, avatars, documents)
```

### Flux de rendu

```
Requête HTTP
  → Router Laravel
  → Middleware (auth, role, CheckPermission, EnsureSchoolActive...)
  → Controller → Inertia::render('Page/Name', $props)
  → Inertia.js transmet les props au composant Vue 3
  → Rendu côté client
```

---

## 9. Fonctionnalités

### Multi-écoles
- Plusieurs établissements depuis une seule instance Laravel
- Isolation complète des données par `school_id` (UUID)
- Chaque école a ses propres clés de paiement, logo, fond de connexion et configuration académique
- Le super admin gère toutes les écoles ; chaque admin ne voit que son école

### Utilisateurs
- 5 rôles : Super Admin · Admin · Professeur · Apprenant · Parent
- CRUD complet, désactivation (soft delete via `is_delete`), réinitialisation de mot de passe
- Liaison parent ↔ apprenant
- Export Excel
- Connexion sociale Google / Facebook (Socialite)
- Changement de mot de passe forcé (flag `must_change_password`)

### Académique
- Gestion des classes et matières avec coefficients
- Affectation matières ↔ classes, professeurs ↔ classes
- Emploi du temps par classe, jour, créneau, salle

### Évaluations & Notes (nouveau système)
- Types : Interrogation · Devoir surveillé · Travail maison · Examen blanc
- Workflow : Brouillon → Ouvert → Fermé → Validé / Annulé
- Coefficient et score max configurables
- Gestion des absences (score null avec observation)

### Bulletins
- Génération automatique par apprenant par période
- Calcul moyenne générale, rang, taux de réussite de classe
- Détail par matière (moyenne pondérée, appréciation)
- Statuts : Brouillon → Publié
- Impression via vue Blade dédiée

### Présences
- Saisie par classe et date — statuts : Présent · En retard · Absent · Demi-journée
- Rapports avec export

### Devoirs
- Création par classe et matière, pièces jointes multiples
- Suivi des soumissions apprenants

### Frais de scolarité
- Suivi total / versé / restant par apprenant
- Intégration KkiaPay · Stripe · FedaPay · PayPal

### Communication
- Tableau d'affichage (noticeboard) ciblé par type d'utilisateur
- Envoi d'emails groupés
- Messagerie interne (chat temps réel par polling)
- Notifications in-app

### Ressources humaines
- Fiches personnel, types de congés configurables
- Gestion des demandes de congés (en attente / approuvé / rejeté)
- Calendrier des événements scolaires (types personnalisables par école)

### RBAC
- Interface complète de gestion des rôles et permissions (super admin)
- Spatie Permission v6 — permissions granulaires par route
- Attribution de permissions directement sur un utilisateur

### Journal d'audit
- Toute suppression enregistrée dans `deletion_logs` (snapshot JSON + auteur + horodatage)

---

## 10. Rôles et permissions

### Hiérarchie

```
super_admin (user_type = 0)
│  Accès total — gère toutes les écoles, RBAC
│
└── admin (user_type = 1)
    │  Gère son école
    │
    ├── teacher (user_type = 2)   — notes, présences, devoirs
    ├── student (user_type = 3)   — consultation résultats
    └── parent  (user_type = 4)   — suivi enfant
```

### Exemples de permissions

| Catégorie | Permission |
|-----------|------------|
| Navigation | `view.dashboard.admin`, `view.bulletins.list` |
| Utilisateurs | `action.teachers.create`, `action.students.reset_password` |
| Académique | `action.classes.create`, `action.subjects.edit` |
| Évaluations | `action.exams.create`, `action.marks.manage` |
| Bulletins | `action.bulletins.generate`, `action.bulletins.publish` |
| Frais | `action.fees.collect`, `action.fees.delete` |
| Personnel | `action.staff.create`, `action.staff.leaves` |
| RBAC | `roles.view`, `permissions.assign` *(super_admin uniquement)* |

---

## 11. Structure des migrations

37 migrations consolidées — **une seule migration par table**, structure finale complète, toutes les clés primaires en **UUID v4**.

```
database/migrations/
├── 2000_01_01_000001_create_password_reset_tokens_table.php
├── 2000_01_01_000002_create_failed_jobs_table.php
├── 2000_01_01_000004_create_schools_table.php          ← uuid PK, multi-tenant root
├── 2000_01_01_000005_create_settings_table.php         ← bigIncrements (singleton id=1)
├── 2000_01_01_000006_create_users_table.php            ← uuid PK, FK → schools
├── 2000_01_01_000007_create_permissions_tables.php     ← Spatie (model_morph_key = uuid)
├── 2000_01_01_000008_create_class_table.php
├── 2000_01_01_000009_create_subject_table.php
├── 2000_01_01_000010_create_week_table.php
├── 2000_01_01_000011_create_class_subject_table.php    ← unique(class_id, subject_id)
├── 2000_01_01_000012_create_class_teacher_table.php
├── 2000_01_01_000013_create_class_timetable_table.php
├── 2000_01_01_000014_create_periods_table.php          ← FK → settings (bigInt) + schools
├── 2000_01_01_000015_create_exams_table.php
├── 2000_01_01_000016_create_schedules_table.php
├── 2000_01_01_000017_create_marks_grade_table.php
├── 2000_01_01_000018_create_marks_register_table.php   ← ancien système (conservé)
├── 2000_01_01_000019_create_attendances_table.php
├── 2000_01_01_000020_create_communicates_table.php
├── 2000_01_01_000021_create_noticeboard_messages_table.php
├── 2000_01_01_000022_create_works_table.php
├── 2000_01_01_000023_create_work_attachments_table.php
├── 2000_01_01_000024_create_homework_table.php
├── 2000_01_01_000025_create_feescollections_table.php
├── 2000_01_01_000026_create_chats_table.php
├── 2000_01_01_000027_create_notifications_table.php    ← uuidMorphs (Laravel std)
├── 2000_01_01_000028_create_leave_types_table.php
├── 2000_01_01_000029_create_staff_table.php
├── 2000_01_01_000030_create_staff_leaves_table.php
├── 2000_01_01_000031_create_staff_events_table.php
├── 2000_01_01_000032_create_event_type_customs_table.php  ← types personnalisés par école
├── 2000_01_01_000033_create_evaluations_table.php
├── 2000_01_01_000034_create_grades_table.php
├── 2000_01_01_000035_create_bulletins_table.php         ← unique(student_id, period_id)
├── 2000_01_01_000036_create_bulletin_subjects_table.php
└── 2000_01_01_000037_create_deletion_logs_table.php     ← record_id = string(36) uuid
```

### Particularités UUID

- Toutes les clés primaires : `$table->uuid('id')->primary()`
- Toutes les clés étrangères : `$table->uuid('xxx_id')->nullable()` + `$table->foreign(...)`
- Exception : `settings.id` reste `bigIncrements` (singleton référencé en dur par `settings_id = 1`)
- Table `notifications` : utilise `uuidMorphs('notifiable')` (standard Laravel)
- Table `deletion_logs` : `record_id` est `string(36)` car il pointe vers n'importe quelle table UUID
- Spatie : `config/permission.php` → `model_morph_key = 'model_uuid'`

---

## 12. Structure des seeders

```
database/seeders/
├── DatabaseSeeder.php             ← Orchestrateur
├── SettingsSeeder.php
├── SchoolSeeder.php               ← UUIDs fixes : LMC_ID, CSM_ID, EPE_ID
├── RolesAndPermissionsSeeder.php
├── SuperAdminSeeder.php           ← UUID fixe : SUPER_ADMIN_ID
├── WeekSeeder.php                 ← UUIDs fixes par jour (WEEK_IDS[])
├── LeaveTypesSeeder.php
├── MultiSchoolSeeder.php          ← Str::uuid() pour toutes les insertions DB::table()
├── PeriodsSeeder.php              ← UUIDs fixes : T1_ID, T2_ID, T3_ID
├── StaffAndEventsSeeder.php
└── EvaluationsSeeder.php          ← Évaluations + notes + bulletins + présences + frais + devoirs + annonces
```

---

## 13. Variables d'environnement

```env
# ── Application ───────────────────────────────────────────────
APP_NAME="School Management System"
APP_ENV=local
APP_KEY=                          # php artisan key:generate
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# ── Base de données PostgreSQL (Supabase) ─────────────────────
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe
DB_SSLMODE=require                # "require" sur Supabase, "prefer" en local

# ── Cache / Session / Queue ───────────────────────────────────
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# ── Mail ──────────────────────────────────────────────────────
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=xxxx_xxxx_xxxx_xxxx  # mot de passe d'application Gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# ── Paiements ─────────────────────────────────────────────────
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=

STRIPE_PUBLIC_KEY=
STRIPE_SECRET_KEY=

KKIAPAY_PUBLIC_KEY=
KKIAPAY_PRIVATE_KEY=
KKIAPAY_SECRET_KEY=

FEDAPAY_PUBLIC_KEY=
FEDAPAY_SECRET_KEY=

# ── Auth sociale ──────────────────────────────────────────────
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"

# ── Vite ──────────────────────────────────────────────────────
VITE_APP_NAME="${APP_NAME}"
```

---

## 14. Commandes artisan utiles

### Base de données

```bash
# Repartir de zéro + seed complet (développement)
php artisan migrate:fresh --seed

# Appliquer les nouvelles migrations uniquement
php artisan migrate

# Lancer un seeder précis
php artisan db:seed --class=EvaluationsSeeder

# État des migrations
php artisan migrate:status
```

### Cache

```bash
# Tout vider
php artisan optimize:clear

# Cache Spatie uniquement (après modif rôles/permissions)
php artisan permission:cache-reset

# Caches de production
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

### Commandes personnalisées

```bash
# Réinitialiser les évaluations bloquées en état incomplet
php artisan evaluations:reset-incomplete

# Envoyer les emails du tableau d'affichage en attente
php artisan noticeboard:send-emails

# Synchroniser les permissions du super admin
php artisan permissions:sync-super-admin
```

### Développement

```bash
php artisan serve          # Serveur local
npm run dev                # Vite hot reload
npm run build              # Build de production
php artisan tinker         # REPL Laravel
php artisan route:list     # Liste des routes
php artisan storage:link   # Lien symbolique storage → public
```

---

## 15. Déploiement (Railway + Supabase)

### Architecture recommandée

```
Railway (App Laravel)  ──────→  Supabase (PostgreSQL)
        │
        └──→  Cloudinary ou Supabase Storage (uploads)
```

### Étapes sur Railway

1. Créez un nouveau projet sur [railway.app](https://railway.app)
2. Connectez votre dépôt GitHub
3. Railway détecte automatiquement Laravel via Nixpacks
4. Ajoutez les variables d'environnement dans Railway → **Variables** (reprendre le contenu du `.env.example` avec les valeurs de production)
5. Ajoutez une commande de release :

```
php artisan migrate --force && php artisan config:cache && php artisan route:cache
```

### Variables Railway essentielles

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-app.railway.app

DB_CONNECTION=pgsql
DB_HOST=db.xxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe
DB_SSLMODE=require

QUEUE_CONNECTION=sync
SESSION_DRIVER=cookie
CACHE_DRIVER=file
```

### Uploads en production

Les fichiers uploadés (`public/upload/`) ne persistent pas entre les déploiements Railway. Configurez un stockage externe :

- **Cloudinary** — pour les images (logos, photos de profil)
- **Supabase Storage** — pour les documents (devoirs, pièces jointes)
- **Backblaze B2** — alternative S3-compatible

---

## 16. Paiements en ligne

### KkiaPay (mobile money — Bénin / Afrique de l'Ouest)

```env
KKIAPAY_PUBLIC_KEY=tpk_xxxxxxxxxxxx
KKIAPAY_PRIVATE_KEY=tpvk_xxxxxxxxxxxx
KKIAPAY_SECRET_KEY=xxxxxxxxxxxxxxxxxx
```

Obtenir les clés : [kkiapay.me](https://kkiapay.me) → Tableau de bord → API Keys

### FedaPay (mobile money — Afrique)

```env
FEDAPAY_PUBLIC_KEY=pk_live_xxxxxxxxxxxx
FEDAPAY_SECRET_KEY=sk_live_xxxxxxxxxxxx
```

Obtenir les clés : [fedapay.com](https://fedapay.com) → API

### Stripe (carte internationale)

```env
STRIPE_PUBLIC_KEY=pk_live_xxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_live_xxxxxxxxxxxx
```

Obtenir les clés : [dashboard.stripe.com](https://dashboard.stripe.com) → Developers → API Keys

### PayPal

```env
PAYPAL_MODE=sandbox       # sandbox ou live
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=
```

> Utilisez les clés **sandbox/test** de chaque passerelle en développement.

---

## 17. Authentification sociale

### Google OAuth

1. [console.cloud.google.com](https://console.cloud.google.com) → **APIs & Services → Credentials → OAuth 2.0 Client ID**
2. Type : **Web application**
3. URI de redirection autorisée : `https://votre-domaine.com/auth/google/callback`
4. Copiez les clés dans `.env` :

```env
GOOGLE_CLIENT_ID=594636074255-xxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxx
GOOGLE_REDIRECT_URI=https://votre-domaine.com/auth/google/callback
```

### Facebook OAuth

1. [developers.facebook.com](https://developers.facebook.com) → Application **Consumer** → **Facebook Login**
2. URI de redirection : `https://votre-domaine.com/auth/facebook/callback`

```env
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"
```

---

## 18. Emails

### Configuration Gmail (développement)

1. Activez la validation en deux étapes sur votre compte Google
2. **Compte Google → Sécurité → Mots de passe des applications** → créez un mot de passe pour `SMS`
3. Renseignez le `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=xxxxxxxxxxxxxxxx    # mot de passe d'application (16 caractères)
MAIL_ENCRYPTION=tls
```

### Mode log (développement sans SMTP)

```env
MAIL_MAILER=log
```

Les emails apparaissent dans `storage/logs/laravel.log`.

### Tester l'envoi

```bash
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('test@exemple.com')->subject('Test'));
```

---

## 19. Résolution de problèmes fréquents

### `php artisan migrate` échoue avec une erreur de clé étrangère

Assurez-vous de partir d'une base vide. Les migrations sont ordonnées pour respecter les dépendances :
```bash
php artisan migrate:fresh --seed
```

### Erreur `SQLSTATE[42P01]: undefined table` sur Supabase

La connexion utilise peut-être le pooler (port 6543) pour les migrations. Utilisez la connexion directe (port 5432) pour `migrate`.

### `Class "Spatie\Permission\..." not found`

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan config:clear
```

### Les permissions ne se chargent pas

```bash
php artisan permission:cache-reset
php artisan config:clear
```

### Erreur UUID dans les seeders (`insertGetId` retourne un int)

Les seeders utilisent `DB::table()->insert(['id' => (string) Str::uuid(), ...])` — ne pas utiliser `insertGetId()` qui ne fonctionne pas avec les UUIDs PostgreSQL. Si vous ajoutez un seeder personnalisé, suivez ce pattern.

### Page blanche / 500 après déploiement

```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
npm run build
```

### Erreur `EXTRACT(MONTH FROM ...)` sur PostgreSQL

Toutes les requêtes utilisent `EXTRACT(MONTH FROM colonne)` (standard SQL). `MONTH()` (MySQL uniquement) a été remplacé dans tout le code.

### Fichiers uploadés absents en production

Configurez un stockage externe (Cloudinary, Supabase Storage, S3) — les fichiers dans `public/upload/` ne persistent pas sur les hébergeurs sans disque persistant (Railway, Render).

---

## Licence

Ce projet est sous licence **MIT**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

*Développé avec ❤️ — Laravel 10 · Vue 3 · Inertia.js · Tailwind CSS · PostgreSQL / Supabase*
