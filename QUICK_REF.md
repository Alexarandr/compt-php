# Quick Reference (One Pager)

Got 30 seconds? This is what you need.

## ⚡ Setup
```bash
# Docker (fastest)
docker compose up --build && docker compose exec app php artisan migrate

# Local
composer install && cp .env.example .env && php artisan key:generate && php artisan migrate && php artisan serve
```

## 📡 API (3 Endpoints)
```bash
# Get it
GET /api/counter/count

# Add to it
POST /api/counter/increment        # Body: {"amount": 1}

# Reset it
DELETE /api/counter/reset
```

## 🧪 Test
```bash
php artisan test tests/Feature/CounterTest.php  # 7 tests, all pass ✅
```

## 📚 Docs
- [README.md](README.md) - Setup & overview
- [API.md](API.md) - Endpoint details
- [CHANGES.md](CHANGES.md) - What changed & why
