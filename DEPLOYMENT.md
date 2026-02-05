# Deployment Guide

Guide complet pour déployer Counter App en développement et production.

## Déploiement Local (Docker Compose)

### Prérequis
- Docker & Docker Compose
- 2GB RAM disponible
- Port 8081 disponible

### Étapes

1. **Cloner et configurer**
```bash
git clone <repo-url> counter-app
cd counter-app
cp .env.example .env
```

2. **Démarrer les services**
```bash
docker compose up -d
```

3. **Exécuter les migrations**
```bash
docker compose exec -T app php artisan migrate
```

4. **Accéder à l'application**
```
http://localhost:8081
```

### Vérifier la santé

```bash
# Tous les logs
docker compose logs

# Logs spécifiques
docker compose logs app
docker compose logs db

# Statut des services
docker compose ps
```

### Arrêter

```bash
docker compose down

# Avec nettoyage des volumes
docker compose down -v
```

## Déploiement Kubernetes (Minikube)

### Prérequis
- Minikube installé et démarré
- kubectl configuré
- 4GB RAM pour Minikube
- 20GB disque libre

### Étapes

1. **Démarrer Minikube**
```bash
minikube start
```

2. **Construire l'image**
```bash
eval $(minikube docker-env)
docker build -t app:latest .
```

3. **Déployer l'application**
```bash
# Créer le namespace et les ressources
kubectl apply -f k8s/namespace.yaml
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secret.yaml
kubectl apply -f k8s/mysql-pvc.yaml
kubectl apply -f k8s/mysql-service.yaml

# Attendre MySQL
kubectl wait --for=condition=ready pod -l app=mysql -n counter-app --timeout=120s

# Déployer MySQL
kubectl apply -f k8s/mysql-deployment.yaml

# Migrations
kubectl apply -f k8s/migrations-job.yaml
kubectl wait --for=condition=complete job/laravel-migrations -n counter-app --timeout=120s

# Laravel App
kubectl apply -f k8s/app-service.yaml
kubectl apply -f k8s/app-deployment.yaml
```

4. **Accéder à l'application**
```bash
# Port-forward
kubectl port-forward -n counter-app svc/laravel-app 8888:80
# http://localhost:8888

# Ou via Ingress
minikube addons enable ingress
kubectl apply -f k8s/ingress.yaml
minikube ip  # Ajouter à /etc/hosts
# http://counter-app.local
```

### Monitoring

```bash
# Statut complet
kubectl get all -n counter-app

# Pods détails
kubectl describe pod <pod-name> -n counter-app

# Logs
kubectl logs -n counter-app deployment/laravel-app -f
kubectl logs -n counter-app deployment/mysql -f
```

## Déploiement Production (Cloud)

### Architecture recommandée

```
Load Balancer
    ↓
Ingress Controller
    ↓
Kubernetes Cluster (3+ nodes)
├── Laravel App Replicas (3-5)
├── MySQL Pod + PersistentVolume (SSD)
├── Redis Cache (optionnel)
└── Monitoring Stack (Prometheus/Grafana)
```

### Prérequis
- Cluster Kubernetes 1.21+
- StorageClass pour volumes persistants
- Registry privé ou Docker Hub
- Certificats TLS (Let's Encrypt)

### Préparation

1. **Créer les images**
```bash
docker build -t registry.example.com/counter-app:v1.0.0 .
docker push registry.example.com/counter-app:v1.0.0
```

2. **Adapter les manifests**
   - Mettre à jour `imagePullPolicy: Always`
   - Mettre à jour image: `registry.example.com/counter-app:v1.0.0`
   - Configurer les secrets via Vault/Sealed Secrets
   - Adapter le storageClassName

3. **Créer les overlays Kustomize**
```
overlays/
├── dev/
├── staging/
└── production/
```

4. **Déployer**
```bash
kustomize build overlays/production | kubectl apply -f -
```

### Sécurité Production

- [ ] NetworkPolicies activées
- [ ] RBAC configuré
- [ ] Resource quotas définis
- [ ] Pod Security Policies activées
- [ ] Secrets chiffrés (Sealed Secrets/Vault)
- [ ] TLS/HTTPS activé (cert-manager)
- [ ] Image scanning activé (Trivy)
- [ ] Audit logging activé
- [ ] Pod Disruption Budgets définis

### Monitoring & Logging

```bash
# Prometheus
kubectl apply -f https://github.com/prometheus-operator/prometheus-operator/...

# Loki Stack
helm repo add grafana https://grafana.github.io/helm-charts
helm install loki grafana/loki-stack -n counter-app
```

### Backup & Disaster Recovery

```bash
# Backup de la DB
kubectl exec -n counter-app mysql-pod -- mysqldump -u root -p$PASS --all-databases > backup.sql

# Velero pour backups Kubernetes complets
helm repo add vmware-tanzu https://vmware-tanzu.github.io/helm-charts
helm install velero vmware-tanzu/velero -n velero --create-namespace
```

## CI/CD Pipeline

Exemple avec GitHub Actions:

```yaml
# .github/workflows/deploy.yml
name: Deploy

on:
  push:
    branches: [main]
    tags: ['v*']

jobs:
  build-and-push:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Build image
        run: docker build -t registry.example.com/counter-app:${{ github.sha }} .
      
      - name: Push image
        run: docker push registry.example.com/counter-app:${{ github.sha }}
      
      - name: Deploy to K8s
        run: |
          kubectl set image deployment/laravel-app \
            laravel-app=registry.example.com/counter-app:${{ github.sha }} \
            -n counter-app
```

## Rollback

```bash
# Voir l'historique des deployments
kubectl rollout history deployment/laravel-app -n counter-app

# Revenir à la version précédente
kubectl rollout undo deployment/laravel-app -n counter-app

# Revenir à une version spécifique
kubectl rollout undo deployment/laravel-app -n counter-app --to-revision=2
```

## Autoscaling

```bash
# HPA (Horizontal Pod Autoscaler)
kubectl autoscale deployment laravel-app --min=2 --max=5 \
  --cpu-percent=80 -n counter-app

# VPA (Vertical Pod Autoscaler) - optionnel
kubectl apply -f https://github.com/kubernetes/autoscaler/...
```

## Troubleshooting

### Pods ne démarrent pas
```bash
kubectl describe pod <pod-name> -n counter-app
kubectl logs <pod-name> -n counter-app
```

### Erreurs de connexion DB
```bash
# Vérifier que MySQL est ready
kubectl get pod -l app=mysql -n counter-app

# Tester la connexion
kubectl exec -it <app-pod> -n counter-app -- \
  php artisan db:monitor
```

### Performance lente
```bash
# Vérifier les ressources
kubectl top pods -n counter-app
kubectl top nodes

# Vérifier les events
kubectl get events -n counter-app --sort-by='.lastTimestamp'
```

---

Pour plus d'infos, voir [KUBERNETES.md](KUBERNETES.md) et [README.md](README.md)
