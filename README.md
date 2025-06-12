# Utsu Site

## Description
Utsu Site est une plateforme communautaire moderne développée avec Symfony 7.2, conçue pour offrir une expérience utilisateur riche et intuitive. Cette application web met l'accent sur la création de contenu, l'interaction sociale et la modération communautaire. Inspirée des meilleures pratiques des réseaux sociaux modernes, elle combine les fonctionnalités essentielles d'un forum traditionnel avec les avantages d'une interface utilisateur contemporaine.

La plateforme est construite avec une architecture robuste utilisant :
- Symfony 7.2 comme framework backend
- Doctrine ORM pour la gestion de la base de données
- Twig pour le templating
- Stimulus.js pour l'interactivité côté client
- PostgreSQL comme système de gestion de base de données
- JWT pour l'authentification sécurisée

---

## 🗄️ Schéma et Description de la Base de Données

![Schéma de la base de données](./docs/db-schema.png)

La base de données du projet Utsu Site est conçue pour gérer efficacement les interactions sociales, la modération et la gestion de contenu. Voici une description des principales tables :

- **user** :
  - Gère les comptes utilisateurs (email, rôles, mot de passe, image de profil, type, nom d'utilisateur, statut de dangerosité).

- **posts** :
  - Contient les publications des utilisateurs, associées à une catégorie et à un utilisateur. Supporte le contenu texte, les images, la date de publication et un indicateur de contenu dangereux.

- **commentaires** :
  - Gère les commentaires sur les posts, avec support des réponses imbriquées (com_parent_id), images, vidéos, date de création et auteur.

- **categories** :
  - Liste les différentes catégories de posts, avec un indicateur de dangerosité.

- **likes** :
  - Permet aux utilisateurs d'aimer ou de disliker des posts. Stocke l'utilisateur, le post et le type d'interaction.

- **abonnement** :
  - Gère les abonnements entre utilisateurs et/ou catégories (suivi d'utilisateurs ou de catégories).

- **notification** :
  - Stocke les notifications envoyées aux utilisateurs (liées à un commentaire, état de lecture, date de création).

- **doctrine_migration_versions** :
  - Historique des migrations Doctrine (version, date d'exécution, temps d'exécution).

- **messenger_messages** :
  - Messages utilisés par le composant Messenger de Symfony (file d'attente, corps, en-têtes, dates).

Ce schéma relationnel permet de garantir l'intégrité des données, la performance des requêtes et la flexibilité pour l'évolution future de la plateforme.

---

## Prérequis
- PHP 8.2 ou supérieur
- Composer
- PostgreSQL 16
- Node.js et npm
- Docker et Docker Compose

## Installation

1. Cloner le repository
```bash
git clone [URL_DU_REPO]
cd utsu-site
```

2. Installer les dépendances PHP
```bash
composer install
```

3. Installer les dépendances JavaScript
```bash
npm install
```

4. Configurer l'environnement
```bash
cp .env .env.local
```
Modifier les variables d'environnement dans `.env.local` selon vos besoins.

5. Démarrer les conteneurs Docker
```bash
docker compose up -d
```

6. Créer la base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

7. Charger les fixtures (optionnel)
```bash
php bin/console doctrine:fixtures:load
```

8. Compiler les assets
```bash
npm run dev
```

## Structure du Projet

### Backend (src/)
- `Controller/` - Contrôleurs de l'application
- `Entity/` - Entités Doctrine
- `Repository/` - Repositories pour les requêtes personnalisées
- `Form/` - Formulaires Symfony
- `Security/` - Configuration de sécurité
- `EventListener/` - Écouteurs d'événements
- `DataFixtures/` - Données de test

### Frontend (templates/)
- `base.html.twig` - Template de base
- `account/` - Pages de gestion de compte
- `posts/` - Gestion des publications
- `categories/` - Gestion des catégories
- `commentaires/` - Système de commentaires
- `notification/` - Système de notifications
- `search/` - Fonctionnalités de recherche
- `security/` - Pages d'authentification
- `share/` - Fonctionnalités de partage

### Assets (assets/)
- `styles/` - Fichiers CSS
- `controllers/` - Contrôleurs Stimulus
- `icon/` - Icônes et images

## Fonctionnalités Principales

### Authentification et Sécurité
- **Système d'authentification JWT**
  - Authentification sécurisée avec JSON Web Tokens
  - Gestion des sessions utilisateur
  - Protection contre les attaques CSRF
  - Validation des tokens et gestion des expirations

- **Gestion des rôles utilisateurs**
  - Hiérarchie de rôles (Admin, Modérateur, Utilisateur)
  - Permissions granulaires par fonctionnalité
  - Système de promotion/dégradation automatique basé sur le comportement

- **Sécurité avancée**
  - Protection CSRF sur tous les formulaires
  - Validation des entrées utilisateur
  - Protection contre les injections SQL
  - Chiffrement des données sensibles
  - Système de rate limiting pour prévenir les abus

### Gestion de Contenu
- **Système de Posts**
  - Création de posts avec support multimédia
  - Édition et suppression de posts
  - Système de brouillon
  - Historique des modifications
  - Support du markdown
  - Système de tags et catégories

- **Système de Commentaires**
  - Commentaires imbriqués (réponses aux commentaires)
  - Édition et suppression de commentaires
  - Système de modération des commentaires
  - Notifications pour les réponses
  - Support du markdown dans les commentaires

- **Gestion des Catégories**
  - Création et gestion de catégories
  - Sous-catégories
  - Modération par catégorie
  - Statistiques par catégorie
  - Système de suivi de catégories

### Interface Utilisateur
- **Design Responsive**
  - Interface adaptative pour tous les appareils
  - Thème clair/sombre
  - Personnalisation de l'interface utilisateur
  - Animations fluides et transitions

- **Système de Notifications**
  - Notifications en temps réel
  - Notifications par email
  - Centre de notifications
  - Préférences de notification personnalisables
  - Filtrage des notifications

- **Recherche Avancée**
  - Recherche full-text
  - Filtres multiples
  - Historique de recherche
  - Suggestions de recherche
  - Recherche dans les commentaires

- **Fonctionnalités Sociales**
  - Système de suivi entre utilisateurs
  - Profils utilisateurs personnalisables
  - Système de réputation
  - Badges et récompenses
  - Activité récente des utilisateurs suivis

### Modération et Administration
- **Panneau de Modération**
  - Interface de modération intuitive
  - File d'attente de modération
  - Historique des actions de modération
  - Système de rapports
  - Actions en masse

- **Statistiques et Analytics**
  - Tableau de bord administrateur
  - Statistiques d'utilisation
  - Rapports d'activité
  - Métriques de performance
  - Analyse du comportement utilisateur

### API et Intégration
- **API RESTful**
  - Documentation complète avec OpenAPI
  - Authentification JWT
  - Rate limiting
  - Versioning de l'API
  - Endpoints pour toutes les fonctionnalités principales

- **Intégrations**
  - Partage sur les réseaux sociaux
  - Import/Export de données
  - Webhooks personnalisables
  - Intégration avec des services tiers

## CI/CD avec GitLab

### Pipeline d'Intégration Continue
Le projet utilise GitLab CI/CD pour l'automatisation des tests et la vérification de la qualité du code. La configuration se trouve dans `.gitlab-ci.yml`.

#### Étapes de la Pipeline
1. **SecurityChecker**
   - Vérification des vulnérabilités de sécurité
   - Analyse du code avec phpcs pour la sécurité

2. **CodingStandards**
   - Vérification des standards de code avec phpcs
   - Analyse statique avec phpstan

3. **UnitTests**
   - Exécution des tests unitaires avec PHPUnit

### Gestion de Projet
- Utilisation des issues GitLab pour le suivi des tâches
- Milestones pour la planification des versions
- Labels pour la catégorisation des tâches
- Tableaux Kanban pour le suivi visuel

### Planning des Commits
- Utilisation des branches feature pour le développement
- Merge requests pour la revue de code
- Tags pour les versions
- Documentation des changements dans les commits

## Développement

### Commandes Utiles
```bash
# Démarrer le serveur de développement
symfony server:start

# Vérifier les routes
php bin/console debug:router

# Créer une migration
php bin/console make:migration

# Vider le cache
php bin/console cache:clear
```

### Tests
```bash
# Exécuter les tests
php bin/phpunit

# Vérifier les standards de code
./vendor/bin/phpcs
```

## Contribution
1. Créer une branche pour votre fonctionnalité
2. Commiter vos changements
3. Pousser vers la branche
4. Créer une Merge Request

## Licence
Propriétaire - Tous droits réservés