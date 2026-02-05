# Contributing to Counter App

Merci de votre intérêt pour contribuer à Counter App! Ce document fournit les guidelines pour contribuer au projet.

## Code de Conduite

Soyez respectueux et professionnel. Toute forme de harcèlement ou discrimination n'est pas tolérée.

## Comment contribuer

### Signaler un Bug

1. Vérifiez que le bug n'a pas déjà été reporté
2. Utilisez le titre descriptif pour la issue
3. Décrivez le comportement observé vs attendu
4. Fournissez des exemples pour reproduire
5. Spécifiez votre environnement (OS, PHP version, etc.)

### Proposer une Amélioration

1. Utilisez un titre clair et descriptif
2. Fournissez une description détaillée
3. Listez les bénéfices potentiels
4. Mentionnez les alternatives considérées

### Pull Requests

1. Fork le repository
2. Créez une branche feature (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Pushez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

#### Standards pour les PRs

- Décrivez clairement les changements apportés
- Référencez les issues concernées (`Fixes #123`)
- Testez votre code avant de soumettre
- Respectez le style de code existant
- Ajoutez des tests si applicable

## Standards de Code

### PHP/Laravel

- Suivez les [PSR-12](https://www.php-fig.org/psr/psr-12/) standards
- Utilisez type hints
- Écrivez du code testable et découplé
- Documentez les méthodes complexes

```php
/**
 * Ajoute un au compteur
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function add(): JsonResponse
{
    // ...
}
```

### Frontend

- Utilisez du code vanilla (pas de frameworks) sauf si justifié
- Responsive design par défaut
- Validation côté client pour UX

### Configuration

- Documentez les variables d'env
- Utilisez `.env.example` pour les templates
- Ne commitez jamais `.env`

## Processus de Review

1. Vérifiez que le code fonctionne
2. Vérifiez que les tests passent
3. Vérifiez la documentation
4. Vérifiez les impacts sur la sécurité

## Commits

Format recommandé:

```
type(scope): description courte

Description plus longue si nécessaire, expliquant
le pourquoi du changement.

Fixes #123
```

Types: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `chore`

Exemples:
- `feat(api): add reset endpoint`
- `fix(ui): fix button hover state`
- `docs(readme): update installation steps`

## Testing

- Écrivez des tests pour les nouvelles features
- Les bugs fixes doivent inclure un test
- Assurez-vous que tous les tests passent

```bash
php artisan test
```

## Documentation

- Mettez à jour le README si nécessaire
- Documentez les API changes
- Ajoutez des examples si applicable

## Questions?

Ouvrez une issue avec le tag `question` ou consultez la documentation existante.

---

Merci de contribuer! 🙌
