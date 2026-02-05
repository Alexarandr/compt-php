# Changelog

Tous les changements notables de ce projet seront documentés dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
et ce projet respecte [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Fonctionnalité de réinitialisation du compteur
- Interface web avec design minimaliste
- Déploiement Kubernetes avec manifests complets
- Health checks (readiness & liveness probes)
- Documentation Kubernetes et production
- Tests unitaires et feature tests

### Changed
- Améliorations UI/UX de l'interface web
- Optimisation des images Docker

### Fixed
- Correction de la configuration APP_ENV en production

## [1.0.0] - 2026-02-05

### Added
- Première version stable
- API REST pour compter: GET /api/counter/add, /api/counter/count, /api/counter/reset
- Interface web simple avec bouton +1
- Docker Compose pour déploiement local
- MySQL comme base de données
- Migrations Laravel automatiques
- CSRF protection
- Configuration via variables d'environnement

### Infrastructure
- Dockerfile pour production
- Docker Compose avec 3 services (Traefik, App, MySQL)
- Manifests Kubernetes pour minikube et production
- Scripts d'initialisation MySQL

---

Format:
- `Added` pour les nouvelles fonctionnalités
- `Changed` pour les changements dans les fonctionnalités existantes
- `Deprecated` pour les fonctionnalités qui seront supprimées prochainement
- `Removed` pour les fonctionnalités supprimées
- `Fixed` pour les corrections de bugs
- `Security` pour les patches de sécurité
