# Déploiement Kubernetes - Counter App

Ce dossier contient tous les manifests Kubernetes nécessaires pour déployer l'application Counter App.

## Architecture

```
┌─────────────────────────────────────┐
│        Ingress (nginx)              │
└──────────────────┬──────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
   ┌────▼──────┐      ┌──────▼──────┐
   │ Laravel   │      │  Laravel    │
   │ App Pod 1 │      │  App Pod 2  │
   └────┬──────┘      └──────┬──────┘
        │                    │
        └──────────┬─────────┘
                   │
              ┌────▼────────┐
              │ Service:    │
              │ laravel-app │
              └────┬────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
   ┌────▼──────┐      ┌──────▼──────┐
   │  MySQL    │      │  Job:       │
   │  Pod      │      │  Migrations │
   │           │      │  (one-time) │
   └───────────┘      └─────────────┘
```

## Prérequis

- Kubernetes 1.19+ 
- `kubectl` installé et configuré
- Pour le développement local : minikube ou kind
- L'image Docker `app:latest` doit être disponible (voir instructions ci-dessous)

## Pour le développement local avec Minikube

### 1. Démarrer Minikube
```bash
minikube start
```

### 2. Construire l'image Docker dans Minikube
```bash
# Utiliser l'environnement Docker de Minikube
eval $(minikube docker-env)

# Construire l'image
docker build -t app:latest .
```

### 3. Déployer l'application
```bash
# Appliquer tous les manifests
kubectl apply -f k8s/

# Ou individuellement en cet ordre :
kubectl apply -f k8s/namespace.yaml
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secret.yaml
kubectl apply -f k8s/mysql-pvc.yaml
kubectl apply -f k8s/mysql-service.yaml
kubectl apply -f k8s/mysql-deployment.yaml

# Attendre que MySQL soit prêt
kubectl wait --for=condition=ready pod -l app=mysql -n counter-app --timeout=300s

# Exécuter les migrations
kubectl apply -f k8s/migrations-job.yaml

# Attendre les migrations
kubectl wait --for=condition=complete job/laravel-migrations -n counter-app --timeout=300s

# Déployer l'app Laravel
kubectl apply -f k8s/app-service.yaml
kubectl apply -f k8s/app-deployment.yaml
```

### 4. Accéder à l'application

#### Option A : Port-forward
```bash
kubectl port-forward -n counter-app svc/laravel-app 8080:80
# Accéder à http://localhost:8080
```

#### Option B : Ingress
```bash
# Ajouter une entrée hosts (sur Linux/Mac)
echo "$(minikube ip) counter-app.local" | sudo tee -a /etc/hosts

# Sur Windows (cmd admin):
echo $(minikube ip) counter-app.local >> %WINDIR%\System32\Drivers\etc\hosts

# Accéder à http://counter-app.local
```

## Pour la production

### 1. Pousser l'image sur un registre
```bash
# Modifier l'image dans les manifests (app-deployment.yaml et migrations-job.yaml)
docker build -t votreregistre/counter-app:v1 .
docker push votreregistre/counter-app:v1
```

### 2. Adapter les manifests
- Remplacer `imagePullPolicy: Never` par `imagePullPolicy: Always`
- Mettre à jour l'image: `image: votreregistre/counter-app:v1`
- Adapter le storageClassName selon votre infrastructure
- Configurer des Secrets pour les mots de passe sensibles

### 3. Déployer
```bash
kubectl apply -f k8s/
```

## Commandes utiles

### Vérifier le statut
```bash
# Voir tous les pods
kubectl get pods -n counter-app

# Voir les logs de l'app
kubectl logs -n counter-app deployment/laravel-app

# Voir les logs de MySQL
kubectl logs -n counter-app deployment/mysql

# Voir les logs du job de migration
kubectl logs -n counter-app job/laravel-migrations
```

### Accéder à la base de données
```bash
# Pod MySQL
kubectl exec -it -n counter-app $(kubectl get pod -n counter-app -l app=mysql -o jsonpath='{.items[0].metadata.name}') -- mysql -u root -p
```

### Redémarrer l'app
```bash
kubectl rollout restart deployment/laravel-app -n counter-app
```

### Supprimer tout
```bash
kubectl delete namespace counter-app
```

## Fichiers de configuration

- **namespace.yaml** : Crée le namespace `counter-app`
- **configmap.yaml** : Configuration non-sensible (variables d'env)
- **secret.yaml** : Données sensibles (clés, mots de passe)
- **mysql-pvc.yaml** : Stockage persistant pour MySQL
- **mysql-service.yaml** : Service sans IP pour MySQL (StatefulSet style)
- **mysql-deployment.yaml** : Pod MySQL avec health checks
- **migrations-job.yaml** : Job pour exécuter `php artisan migrate`
- **app-service.yaml** : Service pour l'app Laravel
- **app-deployment.yaml** : Deployment avec 2 replicas et health checks
- **ingress.yaml** : Ingress pour accéder à l'app

## Notes de sécurité

⚠️ **Pour la production** :
- Utiliser des Secrets gérés par Kubernetes (Vault, sealed-secrets, etc.)
- Ne pas commiter les secrets en clair
- Utiliser des images signées
- Configurer RBAC appropriés
- Utiliser des NetworkPolicies
- Configurer des quotas de ressources
- Implémenter un PodDisruptionBudget

## Troubleshooting

### Les pods ne démarrent pas
```bash
kubectl describe pod <pod-name> -n counter-app
```

### MySQL ne se connecte pas
Vérifier que le pod MySQL est prêt:
```bash
kubectl get pods -n counter-app -o wide
```

### Les migrations échouent
```bash
kubectl logs -n counter-app job/laravel-migrations
```

### Réinitialiser tout et recommencer
```bash
kubectl delete namespace counter-app
kubectl apply -f k8s/namespace.yaml
# ... puis redéployer étape par étape
```
