# Oganity CMS

![Oganity Logo](https://via.placeholder.com/150x50?text=Oganity)

Oganity CMS est un système de gestion de contenu moderne et intuitif développé avec Laravel et Vue.js. Il permet de créer et gérer facilement des pages web avec un éditeur visuel, différents templates, et des fonctionnalités SEO avancées.

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

## Fonctionnalités

- **Éditeur de page visuel** : Interface drag-and-drop intuitive pour créer des pages
- **Blocs de contenu modulaires** : Titres, textes, images, et plus à venir
- **Gestion des styles** : Personnalisation des couleurs, tailles, alignements
- **Gestion des templates** : Différents modèles de page pour diverses utilisations
- **SEO intégré** : Optimisation des méta-données pour les moteurs de recherche
- **Responsive design** : Adaptation automatique à tous les appareils
- **Administration sécurisée** : Gestion des utilisateurs et des permissions

## Captures d'écran

![Éditeur de page](/public/images/screenshots/editor.png)

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Node.js et NPM
- Docker et Docker Compose (pour l'environnement de développement avec Laravel Sail)

## Installation

### Avec Docker (recommandé)

1. Clonez le dépôt :
```bash
git clone https://github.com/sylvain-schmitt/Oganity.git
cd Oganity
```

2. Copiez le fichier d'environnement :
```bash
cp .env.example .env
```

3. Configurez les variables d'environnement dans le fichier `.env` selon vos besoins.

4. Lancez l'environnement Docker avec Laravel Sail :
```bash
./vendor/bin/sail up -d
```

5. Installez les dépendances PHP et générez la clé d'application :
```bash
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
```

6. Exécutez les migrations et les seeders :
```bash
./vendor/bin/sail artisan migrate --seed
```

7. Installez les dépendances JavaScript et compilez les assets :
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

8. Accédez à l'application à l'adresse http://localhost

### Installation manuelle (sans Docker)

1. Clonez le dépôt :
```bash
git clone https://github.com/sylvain-schmitt/Oganity.git
cd Oganity
```

2. Copiez le fichier d'environnement :
```bash
cp .env.example .env
```

3. Configurez les variables d'environnement dans le fichier `.env` selon vos besoins.

4. Installez les dépendances PHP et générez la clé d'application :
```bash
composer install
php artisan key:generate
```

5. Exécutez les migrations et les seeders :
```bash
php artisan migrate --seed
```

6. Installez les dépendances JavaScript et compilez les assets :
```bash
npm install
npm run dev
```

7. Lancez le serveur de développement :
```bash
php artisan serve
```

8. Accédez à l'application à l'adresse http://localhost:8000

## Utilisation

1. Connectez-vous à l'interface d'administration avec les identifiants par défaut :
   - Email : admin@example.com
   - Mot de passe : password

2. Explorez le tableau de bord pour accéder aux différentes fonctionnalités :
   - Gestion des pages
   - Gestion des médias
   - Paramètres du site
   - Gestion des utilisateurs

## Structure du projet

- `app/` - Code PHP de l'application Laravel
- `resources/js/` - Composants Vue.js et logique frontend
- `resources/js/Components/Admin/` - Composants d'administration
- `resources/js/Components/Admin/Page/` - Éditeur de page et ses composants
- `database/migrations/` - Migrations de base de données
- `routes/` - Définition des routes de l'application

## Développement

### Commandes utiles avec Laravel Sail

```bash
# Démarrer l'environnement Docker
./vendor/bin/sail up -d

# Arrêter l'environnement Docker
./vendor/bin/sail down

# Exécuter les tests
./vendor/bin/sail test

# Exécuter une commande Artisan
./vendor/bin/sail artisan [commande]

# Exécuter une commande Composer
./vendor/bin/sail composer [commande]

# Exécuter une commande NPM
./vendor/bin/sail npm [commande]
```

### Développement frontend

```bash
# Lancer le serveur de développement Vite
./vendor/bin/sail npm run dev

# Construire pour la production
./vendor/bin/sail npm run build
```

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou à soumettre une pull request.

1. Forkez le projet
2. Créez votre branche de fonctionnalité (`git checkout -b feature/amazing-feature`)
3. Committez vos changements (`git commit -m 'feat: add some amazing feature'`)
4. Poussez vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrez une Pull Request

## Roadmap

- [ ] Ajout de nouveaux types de blocs (vidéo, carrousel, etc.)
- [ ] Système de gestion des médias centralisé
- [ ] Fonctionnalités de blog intégrées
- [ ] Système de plugins extensible
- [ ] Amélioration des performances et optimisations

## Contact

Sylvain Schmitt - [sylvain.schmitt70@gmail.com](mailto:sylvain.schmitt70@gmail.com)

Lien du projet : [https://github.com/sylvain-schmitt/Oganity](https://github.com/sylvain-schmitt/Oganity)

## Licence

Distribué sous la licence MIT. Voir `LICENSE` pour plus d'informations.

## Remerciements

- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [Inertia.js](https://inertiajs.com)
- [Tailwind CSS](https://tailwindcss.com)
