# ✅ Déploiement Kubernetes - Counter App

## Status: 🟢 OPÉRATIONNEL

L'application Counter App est maintenant déployée et fonctionnelle sur Kubernetes!

## Vue d'ensemble du déploiement

```
Minikube Cluster
└── Namespace: counter-app
    ├── MySQL Pod (1 replica)
    │   ├── PersistentVolume (5Gi)
    │   ├── ConfigMap pour config
    │   └── Secret pour credentials
    │
    ├── Laravel App (2 replicas)
    │   ├── Health checks (readiness & liveness)
    │   ├── Requests/Limits configurés
    │   └── Service ClusterIP
    │
    └── Job: Migrations
        └── Exécuté une fois au démarrage
```

## Ressources déployées

### Namespaces
- ✅ `counter-app` - Namespace dédié pour l'application

### Configurations
- ✅ `app-config` ConfigMap - Variables d'env non-sensibles
- ✅ `app-secrets` Secret - Données sensibles (clés, mots de passe)

### Base de données
- ✅ `mysql-pvc` PersistentVolumeClaim - Stockage 5Gi
- ✅ `mysql-service` Service - Accès à la DB
- ✅ `mysql` Deployment - 1 replica MySQL 8.0

### Application
- ✅ `laravel-app` Service - ClusterIP pour accès interne
- ✅ `laravel-app` Deployment - 2 replicas de l'app
- ✅ `laravel-migrations` Job - Migrations de base de données

## Comment accéder à l'application

### Méthode 1: Port-Forward (Actuel)
```bash
kubectl port-forward -n counter-app svc/laravel-app 8888:80
# Accédez à: http://localhost:8888
```

### Méthode 2: Ingress (à configurer)
1. Installer nginx-ingress:
```bash
minikube addons enable ingress
```

2. Appliquer l'ingress:
```bash
kubectl apply -f k8s/ingress.yaml
```

3. Obtenir l'IP:
```bash
minikube ip
# Ajouter dans /etc/hosts: <IP> counter-app.local
```

4. Accédez à: http://counter-app.local

## Commandes utiles

### Statut global
```bash
kubectl get all -n counter-app
```

### Logs
```bash
# App Laravel
kubectl logs -n counter-app -l app=laravel-app --tail=100 -f

# MySQL
kubectl logs -n counter-app -l app=mysql --tail=50

# Migrations
kubectl logs -n counter-app job/laravel-migrations
```

### Accès à la base de données
```bash
kubectl exec -it -n counter-app $(kubectl get pod -n counter-app -l app=mysql -o jsonpath='{.items[0].metadata.name}') -- mysql -u root -p app_database
```

### Redémarrer l'app
```bash
kubectl rollout restart deployment/laravel-app -n counter-app
```

### Vérifier les replicas
```bash
kubectl get deployment -n counter-app
kubectl get pods -n counter-app -o wide
```

## Tester l'API

### Via port-forward
```bash
# Ajouter un compteur
curl -s http://localhost:8888/api/counter/add | jq .

# Récupérer le compteur
curl -s http://localhost:8888/api/counter/count | jq .

# Réinitialiser
curl -s http://localhost:8888/api/counter/reset | jq .
```

## Montée en charge

Le déploiement est configuré avec:
- **2 replicas** de l'app pour haute disponibilité
- **Requests**: CPU 100m, Mémoire 128Mi
- **Limits**: CPU 500m, Mémoire 512Mi
- **Health checks**: Readiness & Liveness probes

Pour autoscaler:
```bash
kubectl autoscale deployment laravel-app --min=2 --max=5 -n counter-app
```

## Nettoyage

Pour supprimer tout:
```bash
kubectl delete namespace counter-app
```

## Architecture de sécurité

✅ Déjà configuré:
- Namespace isolé
- Secrets pour données sensibles
- Health checks
- Resource limits

⚠️ À faire pour la production:
- Utiliser un registre privé
- Configurer RBAC
- NetworkPolicies
- Signed images
- Secret management (Vault, sealed-secrets)
- PodDisruptionBudget
- Resource quotas

## Fichiers de configuration

```
k8s/
├── namespace.yaml           # Namespace dédié
├── configmap.yaml           # Configuration
├── secret.yaml              # Secrets
├── mysql-pvc.yaml           # Stockage persistant
├── mysql-service.yaml       # Service MySQL
├── mysql-deployment.yaml    # Deployment MySQL
├── app-service.yaml         # Service Laravel
├── app-deployment.yaml      # Deployment Laravel
├── migrations-job.yaml      # Job pour migrations
├── ingress.yaml             # Ingress
├── kustomization.yaml       # Kustomize
└── README.md                # Documentation complète
```

## Statistiques

- **Pods actifs**: 3 (2 Laravel + 1 MySQL)
- **Replicas Laravel**: 2
- **MySQL Replicas**: 1
- **Migrations**: ✅ Exécutées
- **Stockage alloué**: 5Gi
- **Namespace**: counter-app

## Prochaines étapes

1. **Configurer Ingress**:
   ```bash
   kubectl apply -f k8s/ingress.yaml
   minikube addons enable ingress
   ```

2. **Monitoring** (optionnel):
   - Installer Prometheus
   - Configurer Grafana

3. **Production**:
   - Pousser l'image sur registre
   - Adapter les manifests
   - Configurer les secrets

## Support

Pour plus d'information, voir `k8s/README.md`
