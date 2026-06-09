# School Management System

Plateforme de gestion scolaire multi-rôles construite avec Laravel 10, Inertia.js et Vue 3.

## Stack technique

| Couche      | Technologie                          |
|-------------|--------------------------------------|
| Backend     | Laravel 10, PHP 8.1+                 |
| Frontend    | Vue 3, TypeScript, Inertia.js        |
| Style       | Tailwind CSS (couleur primaire : Violet `#7C3AED`) |
| Auth        | Laravel Auth + Spatie Permission     |
| Build       | Vite                                 |
| Base de données | MySQL                            |

## Rôles utilisateurs

| Rôle | Accès |
|------|-------|
| Admin (1) | Gestion complète de l'établissement |
| Professeur (2) | Classes, présences, notes, devoirs |
| Apprenant (3) | Résultats, devoirs, présences, contributions |
| Parent (4) | Suivi des enfants |

## Installation

```bash
# 1. Cloner et installer les dépendances
composer install
npm install

# 2. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 3. Base de données
php artisan migrate
# Importer le fichier SQL si disponible :
# mysql -u root schoolManagementSystem < schoolManagementSystem.sql

# 4. Lancer le développement
php artisan serve
npm run dev
```

php artisan superadmin:sync-permissions


## Structure frontend

```
resources/js/
├── app.ts                  # Point d'entrée Inertia
├── types/                  # Types TypeScript globaux
├── Layouts/
│   ├── AppLayout.vue       # Layout principal (header + sous-menu)
│   └── GuestLayout.vue     # Layout pages publiques (login)
├── Components/
│   ├── UI/                 # Composants réutilisables
│   │   ├── AppButton.vue
│   │   ├── AppInput.vue
│   │   ├── AppSelect.vue
│   │   ├── AppModal.vue
│   │   ├── AppTable.vue
│   │   ├── AppBadge.vue
│   │   ├── AppCheckbox.vue
│   │   ├── AppRadio.vue
│   │   ├── AppAlert.vue
│   │   └── AppMultiSelect.vue
│   ├── Layout/
│   │   ├── AppHeader.vue   # Header horizontal avec sous-menus dropdown
│   │   ├── SubMenuBar.vue  # Barre de sous-navigation contextuelle
│   │   └── NavIcon.vue     # Icônes SVG Heroicons
│   └── Dashboard/
│       └── StatCard.vue    # Carte de statistique
├── Composables/
│   └── useNavigation.ts    # Navigation dynamique par rôle
├── Data/
│   └── navigation.ts       # Configuration des menus par rôle
└── Pages/
    ├── Auth/Login.vue
    └── Dashboard/
        ├── Admin.vue
        ├── Teacher.vue
        ├── Student.vue
        └── Parent.vue
```

## Composants UI — Usage rapide

```vue
<!-- Bouton -->
<AppButton variant="primary" size="md" :loading="false">Enregistrer</AppButton>

<!-- Input -->
<AppInput v-model="form.name" label="Nom" required :error="form.errors.name" />

<!-- Select -->
<AppSelect v-model="form.status" label="Statut" :options="[{value:1,label:'Actif'},{value:0,label:'Inactif'}]" />

<!-- MultiSelect -->
<AppMultiSelect v-model="form.subjects" label="Matières" :options="subjectOptions" />

<!-- Modal (formulaire) -->
<AppModal v-model="showModal" title="Créer un admin" size="md">
    <!-- formulaire ici -->
    <template #footer>
        <AppButton variant="ghost" @click="showModal = false">Annuler</AppButton>
        <AppButton type="submit" :loading="form.processing">Enregistrer</AppButton>
    </template>
</AppModal>

<!-- Table -->
<AppTable :columns="cols" :rows="data" :pagination="pagination">
    <template #cell-status="{ value }">
        <AppBadge :variant="value ? 'success' : 'danger'">{{ value ? 'Actif' : 'Inactif' }}</AppBadge>
    </template>
    <template #actions="{ row }">
        <AppButton size="xs" variant="ghost">Éditer</AppButton>
    </template>
</AppTable>
```

## Conventions de code

- Tous les formulaires (create/edit) sont dans des **modals** (`AppModal`)
- Les composants UI sont importés depuis `@/Components/UI`
- Les pages Inertia sont dans `resources/js/Pages/`
- Chaque page reçoit ses données via les **props Inertia** (pas d'appels API séparés)
- La navigation est définie dans `resources/js/Data/navigation.ts`

## Roadmap

- [x] Migration Blade → Vue 3 + Inertia.js
- [x] Header horizontal avec sous-menus (style projet 2)
- [x] Bibliothèque de composants UI réutilisables
- [x] Tailwind config propre (palette violet)
- [ ] Système de rôles/permissions Spatie (Phase 2)
- [ ] Migration de toutes les pages CRUD en Vue + modals
- [ ] Architecture Repository + Service (Phase 3)
- [ ] Multi-tenant / multi-école (Phase 4)
