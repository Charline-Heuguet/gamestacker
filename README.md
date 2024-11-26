# GAME STACKER

Game Stacker est un site web développé avec les technologies suivantes :

- **Back-End** : PHP 8.2 avec le framework Symfony 7.1.6.
- **Base de données** : MySQL, gérée via l'interface graphique PHPMyAdmin.
- **Front-End** : Nuxt, un framework JavaScript basé sur Vue.js, associé à la librairie TailwindCSS.

---

## Présentation du projet

Game Stacker propose une vision complète des dernières actualités du monde du jeu vidéo, un forum interactif dédié à ce domaine, ainsi qu'un espace pour publier et rejoindre des annonces de parties entre joueurs.

Le site est conçu principalement pour une utilisation sur ordinateur, car cette cible constitue notre public prioritaire, mais il est également entièrement **responsive** pour s'adapter à tous les appareils.

---

## Fonctionnalités principales

### 🖥️ Gestion utilisateur

- Création d'un compte via la route `/inscription`.
- Gestion et personnalisation du profil utilisateur via `/profile`.
- Historique des interactions (commentaires, annonces, inscriptions).

### 💬 Interactions sociales

- Commentaires sur les actualités et les forums.
- Votes (UpVote) pour réagir aux commentaires des utilisateurs.
- Signalement de contenus inappropriés pour modération.

### 🎮 Annonces de parties privées

- Création et gestion d'annonces de parties privées.
- Inscription à des parties créées par d'autres utilisateurs.

### 📩 Système de contact

- Page `/contact` et pop-up pour envoyer des messages.
- Emails gérés via **Mailtrap** pour tester l'envoi sécurisé.

### 🎨 Personnalisation

- Bouton pour basculer entre un thème clair et un thème sombre.

---

## 🚀 Démarrage du projet

### Prérequis

Avant de commencer, assurez-vous d'avoir :

- **PHP 8.2 ou supérieur**
- **Composer** pour gérer les dépendances PHP
- **Node.js et npm** ou **Yarn** pour gérer les dépendances JavaScript
- Un environnement de développement web local, tel que :
  - **WAMP**, **XAMPP**, ou **Laragon**
- **Symfony CLI** pour gérer le projet Symfony
- **MySQL** ou un autre système de base de données
- **PHPMyAdmin** pour gérer la base de données

#### Commandes pour vérifier les versions :

```
php -v
composer -v
node -v
npm -v
```

#### 1. Cloner le projet

Clonez le dépôt GitHub dans votre environnement local :

```bash
git clone https://github.com/Charline-Heuguet/gamestacker.git
cd gamestacker
```

## 2. Configuration du Back-End

1. Accédez au répertoire `back` :

   ```bash
   cd back
   ```

2. Configurez la connexion à votre base de données en modifiant le fichier .env avec vos informations de connexion :

```
DATABASE_URL="mysql://<user>:<password>@127.0.0.1:3306/<database_name>"
```

3. Installez les dépendances Composer :

```
composer install
```

4. Lancez le serveur Symfony :

```
symfony server:start
```

5. Créez et appliquez les migrations :

````
php bin/console make:migration
php bin/console doctrine:migrations:migrate```

## 3. Configuration du Front-End

Prérequis spécifiques à Nuxt :
Installez globalement nuxt et vue-cli pour les outils de développement :

```
npm install -g nuxt
npm install -g @vue/cli
```


1. Accédez au répertoire front :

````

cd ../front

```

2. Installez les dépendances avec Yarn ou npm :

```

yarn install

```

3. Lancez le serveur de développement Nuxt :

```

npm run dev

```

##🌐 Accès au site##

Back-End Symfony : http://127.0.0.1:8000
Front-End Nuxt : http://localhost:3000

```
