  Key .......................................................................................... base64:0cmuJEjzbkMDI7vrIkmNO7EHp6J4CYFCERyBztWXhyY=  
  Cipher ............................................................................................................................... AES-256-CBC  
  Encrypted file .............................................................................. C:\laragon\www\schoolManagementSystem\.env.encrypted  


Pour empêcher les développeurs d'envoyer leur code directement sur la branche `main` sur GitHub, vous pouvez mettre en place une **protection de branche** et une **règle d'interdiction de push**.

### 🚀 **Méthode 1 : Protéger la branche `main` sur GitHub**
1. **Allez sur votre dépôt GitHub**
    - Ouvrez votre projet sur GitHub.
    - Allez dans **"Settings"** (Paramètres).

2. **Accédez aux règles de protection des branches**
    - Dans le menu de gauche, sous **"Code and automation"**, cliquez sur **"Branches"**.
    - Sous **"Branch protection rules"**, cliquez sur **"Add branch protection rule"**.

3. **Configurer la protection de la branche `main`**
    - Dans le champ **"Branch name pattern"**, entrez `main`.
    - Cochez **"Require a pull request before merging"** pour obliger les développeurs à utiliser des Pull Requests.
    - Cochez **"Include administrators"** si vous voulez que les admins soient aussi soumis aux règles.
    - Cochez **"Restrict who can push to matching branches"** et ajoutez uniquement les personnes ou groupes autorisés à pousser directement.

4. **Enregistrer les modifications**
    - Cliquez sur **"Create"** ou **"Save changes"**.

---

### 🚀 **Méthode 2 : Bloquer les push sur `main` avec Git Hooks (localement)**
Vous pouvez aussi empêcher les développeurs d'envoyer du code sur `main` localement en ajoutant un `pre-push` hook.

1. **Créez un fichier `pre-push` dans `.git/hooks/`**
   ```bash
   nano .git/hooks/pre-push
   ```
2. **Ajoutez le script suivant** :
   ```bash
   #!/bin/sh
   branch=$(git rev-parse --abbrev-ref HEAD)
   if [ "$branch" = "main" ]; then
       echo "❌ Pousser directement sur la branche main est interdit !"
       exit 1
   fi
   ```
3. **Rendez le script exécutable** :
   ```bash
   chmod +x .git/hooks/pre-push
   ```

Avec ce hook, si quelqu'un essaie de pousser sur `main`, le push sera bloqué localement. 🔒

---

### 🚀 **Méthode 3 : Utiliser GitHub Actions pour rejeter les push directs**
Si vous souhaitez ajouter une protection supplémentaire, vous pouvez créer une **GitHub Action** qui bloque les push directs sur `main`. Ajoutez ce fichier dans `.github/workflows/protect-main.yml` :

```yaml
name: Block Direct Push to Main

on:
  push:
    branches:
      - main

jobs:
  prevent-direct-push:
    runs-on: ubuntu-latest
    steps:
      - name: Block direct push
        run: |
          echo "❌ Pousser directement sur la branche main est interdit !"
          exit 1
```

---

### ✅ **Résultat :**
Avec ces méthodes :
- Personne ne pourra pousser directement sur `main` sur GitHub.
- Les développeurs devront passer par des **Pull Requests** pour fusionner leur code.
- Un contrôle peut être mis en place **localement** pour éviter des erreurs accidentelles.

🚀 **Solution recommandée** : Activez **la protection de branche sur GitHub** et **ajoutez un hook local** pour bloquer les pushs accidentels !
