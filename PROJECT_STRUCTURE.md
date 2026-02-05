# Project Structure

```
counter-app/
│
├── 📄 Core Files
│   ├── README.md                 # Documentation principale
│   ├── CONTRIBUTING.md           # Guide pour contribuer
│   ├── CHANGELOG.md              # Historique des versions
│   ├── LICENSE                   # MIT License
│   ├── DEPLOYMENT.md             # Guide de déploiement
│   ├── KUBERNETES.md             # Documentation Kubernetes
│   ├── composer.json             # Dépendances PHP
│   ├── package.json              # Dépendances npm
│   └── artisan                   # Laravel CLI
│
├── 🐳 Docker & Container
│   ├── Dockerfile                # Image Docker de production
│   ├── docker-compose.yaml       # Orchestration locale
│   └── .dockerignore             # Fichiers à ignorer en build
│
├── ☸️  Kubernetes
│   └── k8s/
│       ├── namespace.yaml        # Namespace isolé
│       ├── configmap.yaml        # Configuration
│       ├── secret.yaml           # Secrets (env sensibles)
│       ├── mysql-pvc.yaml        # Stockage persistant
│       ├── mysql-service.yaml    # Service MySQL
│       ├── mysql-deployment.yaml # Pod MySQL
│       ├── app-service.yaml      # Service Laravel
│       ├── app-deployment.yaml   # Deployment Laravel
│       ├── migrations-job.yaml   # Job migrations
│       ├── ingress.yaml          # Ingress
│       ├── kustomization.yaml    # Kustomize config
│       └── README.md             # Doc K8s
│
├── 🚀 Application Laravel
│   ├── app/                      # Code applicatif
│   │   ├── Http/
│   │   │   ├── Controllers/      # Controllers (CounterController)
│   │   │   ├── Kernel.php        # HTTP Kernel
│   │   │   └── Middleware/       # Middlewares
│   │   ├── Models/               # Eloquent Models (Counter, User)
│   │   ├── Providers/            # Service Providers
│   │   └── Exceptions/           # Exception handlers
│   │
│   ├── routes/                   # Route definitions
│   │   ├── web.php              # Routes web
│   │   ├── api.php              # Routes API
│   │   ├── channels.php         # WebSocket channels
│   │   └── console.php          # Commandes console
│   │
│   ├── config/                   # Configuration
│   │   ├── app.php
│   │   ├── database.php
│   │   ├── auth.php
│   │   ├── cache.php
│   │   └── ...autres configs
│   │
│   ├── database/                 # Base de données
│   │   ├── migrations/           # Migrations SQL
│   │   │   └── 2023_08_21_130800_create_counters_table.php
│   │   ├── seeders/              # Data seeders
│   │   └── factories/            # Model factories
│   │
│   ├── resources/                # Assets & views
│   │   ├── views/
│   │   │   └── welcome.blade.php # Vue principale (compteur)
│   │   ├── css/
│   │   │   └── app.css          # Styles
│   │   ├── js/
│   │   │   ├── app.js
│   │   │   └── bootstrap.js
│   │   └── lang/                 # Traductions
│   │
│   ├── public/                   # Fichiers publics
│   │   ├── index.php            # Point d'entrée
│   │   ├── robots.txt           # SEO
│   │   └── .htaccess            # Apache config
│   │
│   ├── storage/                  # Fichiers générés
│   │   ├── app/                 # User uploads
│   │   ├── framework/           # Cache, sessions
│   │   └── logs/                # Application logs
│   │
│   ├── bootstrap/                # Bootstrap application
│   │   └── cache/               # Application cache
│   │
│   ├── tests/                    # Tests
│   │   ├── Feature/             # Feature tests
│   │   │   └── CounterTest.php
│   │   └── Unit/                # Unit tests
│   │
│   └── vendor/                   # Dépendances Composer (Git ignored)
│
├── 🔧 Configuration
│   ├── .env.example              # Template .env (à copier)
│   ├── .env                      # Variables d'env (⚠️ Git ignored)
│   ├── .editorconfig             # Éditeur config
│   ├── .gitignore                # Fichiers à ignorer
│   ├── .gitattributes            # Attributs git
│   ├── phpunit.xml               # Config tests PHP
│   ├── server.php                # PHP built-in server
│   └── webpack.mix.js            # Laravel Mix config
│
├── 📝 Documentation
│   ├── mysql-init/               # Scripts init MySQL
│   └── node_modules/             # Dépendances npm (Git ignored)
│
└── .github/                      # GitHub spécifique (optionnel)
    ├── workflows/                # CI/CD Actions
    ├── ISSUE_TEMPLATE/           # Templates issues
    └── PULL_REQUEST_TEMPLATE.md  # Template PR

```

## Organisation Logique

### Par Fonctionnalité (Compteur)
- `app/Http/Controllers/CounterController.php` - Logique
- `app/Models/Counter.php` - Modèle données
- `routes/api.php` - Routes API
- `routes/web.php` - Routes web
- `resources/views/welcome.blade.php` - Vue
- `database/migrations/2023_08_21_130800_create_counters_table.php` - Schéma

### Par Environnement
- **Local**: `docker-compose.yaml` + `.env.example`
- **Minikube**: `k8s/` avec configuration test
- **Production**: Overlays Kustomize (à ajouter)

### Par Déploiement
- **Docker Compose**: Services multi-conteneurs avec Traefik
- **Kubernetes**: 2+ replicas, health checks, PV, Ingress

## Fichiers Git Ignorés

✅ Toujours dans `.gitignore`:
- `.env` - Secrets locaux
- `vendor/` - Dépendances Composer
- `node_modules/` - Dépendances npm
- `storage/` - Fichiers générés
- `.idea/`, `.vscode/` - IDE files
- `*.log` - Logs

## Tailles Recommandées

- Code Laravel: < 5MB
- Image Docker: ~500MB-1GB
- Repository total: ~50MB (sans node_modules, vendor)

---

**Principes appliqués:**
- ✅ PSR-12 pour PHP
- ✅ Separation of concerns
- ✅ Configuration externalisée (.env)
- ✅ Infrastructure as Code (K8s)
- ✅ Documentation incluse
- ✅ Prêt pour GitHub/GitLab
