![status](https://img.shields.io/badge/status-d%C3%A9mo-yellow)
![php](https://img.shields.io/badge/PHP-8.1-blue)
![license](https://img.shields.io/badge/license-MIT-lightgrey)

# 🚀 DiffusionsQuestionnairesDemo — Application complète PHP (Projet professionnel en version démo)

> 🧩 Application interne de gestion de **questionnaires °** développée durant un **stage de 5 mois**.  
> Cette version publique est une **démo complète** permettant d’explorer toutes les fonctionnalités principales sans données réelles.

---

## ⚠️⚠️⚠️ IMPORTANT — POUR TESTER LA DÉMO ⚠️⚠️⚠️

# 🚀 Demo Relais Managers

Bienvenue dans la démo de l’application **Relais Managers**.  
Ce guide vous explique comment installer, configurer et explorer la démo étape par étape.

---

## ⚙️ Prérequis

Avant de commencer, assurez-vous d’avoir installé et lancé :
- **Apache**
- **MySQL**
- **PHP**

---

Avant toute chose, **clonez le projet en local** :

```bash
git clone https://github.com/Chakraaaa/DiffusionsQuestionnairesDemo.git
cd DiffusionsQuestionnairesDemo
```
## 🗃️ Installation de la base de données

1. Lancez **Apache** et **MySQL**.  
2. Rendez-vous à la racine du projet.  
3. Importez la base de données en exécutant les fichiers suivants :
   ```bash
   sql/maj_bdd.sql
   sql/reprise_bdd.sql
   ```
4. Créez le compte démo en exécutant le script suivant :
   ```bash
   php scripts/seed_demo_user.php
   ```

---

## 🌐 Lancement de l’application

Ouvrez votre navigateur et accédez à :

👉 [http://localhost/relais-managers-services](http://localhost/relais-managers-services)

Connectez-vous avec le compte démo :
```
Email : demo@demo.com
Mot de passe : demo123
```

---

## 🧭 Visite guidée (démo complète)

Suivez ces étapes pour une démonstration fluide comme si vous étiez un client réel :

---

### 1️⃣ Importation des répondants

1. Allez dans la rubrique **“Répondants”**.  
2. Cliquez sur **“Importer des répondants”**.  
3. Importez le fichier suivant depuis la racine du projet :
   ```
   assets/respondants_demo.xlsx
   ```
4. **Assignez les répondants à un groupe de votre choix.**  
   → Notez bien le nom du groupe, il servira plus tard !

---

### 2️⃣ Création du questionnaire 360

1. Rendez-vous dans la rubrique **“Questionnaires”**.  
2. Cliquez sur **“+360”** pour créer un nouveau questionnaire.  
3. Donnez-lui le nom :
   ```
   Demo360
   ```
4. Cliquez sur les **trois engrenages (Options)** à droite de la ligne *Demo360*.  
5. Remplissez les options avec des données aléatoires, sauf :
   - **Assigner à un groupe** → Choisir le groupe de l’étape 4  
   - **Ne pas activer la diffusion anonyme**
6. Validez les options.

---

### 3️⃣ Publication du questionnaire

1. Cliquez sur l’icône **avion en papier (Publication)** à droite du bouton Options.  
2. Remplissez les options comme précédemment :
   - Données aléatoires OK  
   - **Même groupe que l’étape 4**
3. Cliquez sur **Envoyer**.

> Une alerte bleue apparaîtra en haut :
> ```
> Mode démo : les mails ne sont pas activés. Liens générés :
> ```
> Vous pouvez utiliser n’importe quel lien :  
> chacun correspond à un des trois répondants (dont un pour l’autoévaluation).

---

### 4️⃣ Répondre au questionnaire

- Répondez à toutes les questions si vous souhaitez valider les questionnaires (facultatif).

---

### 5️⃣ Suivi des questionnaires

1. Retournez dans la rubrique **“Questionnaires”**.  
2. Cliquez sur l’icône **tableau** à droite de la publication pour consulter le suivi.

> ⚠️ Le rapport après réponses n’est **pas disponible** en mode démo.

---

## ✅ Fin de la démo

Vous avez maintenant une vue complète du fonctionnement de **Relais Managers** !  
Ce mode démo vous permet de comprendre la logique du système sans envoi de mails ni génération de rapports réels.

---

### 💡 Astuce

Pour une présentation client, suivez exactement les étapes ci-dessus afin d’obtenir une démo fluide et réaliste.

