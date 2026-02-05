# API Reference

Pretty straightforward — just 3 endpoints to manage a counter.

## The Endpoints

| Method | URL | What it does |
|--------|-----|--------------|
| `GET` | `/api/counter/count` | Read the current value |
| `POST` | `/api/counter/increment` | Add to the counter |
| `DELETE` | `/api/counter/reset` | Set it back to 0 |

## Get Counter

Just read the current value. No side effects.

```bash
curl http://localhost:8081/api/counter/count

# Returns: {"value": 42}
```

---

## Increment Counter

Add to the counter. You can specify how much, or it defaults to 1.

```bash
# Add 1 (default)
curl -X POST http://localhost:8081/api/counter/increment

# Add 5 specifically
curl -X POST http://localhost:8081/api/counter/increment \
  -H "Content-Type: application/json" \
  -d '{"amount": 5}'

# Returns: {"value": 47, "message": "Counter incremented by 5"}
```

**Rules for `amount`:**
- Must be a number
- Minimum: 1
- Maximum: 1000

**Oops, you broke the rules?** You'll get:
```json
{
  "error": "Invalid input",
  "messages": {"amount": ["The amount must be at least 1."]}
}
```

---

## Reset Counter

Wipe it clean. Sets it back to 0.

```bash
curl -X DELETE http://localhost:8081/api/counter/reset

# Returns: {"value": 0, "message": "Counter has been reset"}
```

---

## Response Codes

- `200` → You're good ✅
- `422` → You sent bad data (check the error message)
- `500` → Uh oh, something broke on our end

---

## Examples

```bash
# Start fresh
curl http://localhost:8081/api/counter/count
# {"value": 0}

# Add some
curl -X POST http://localhost:8081/api/counter/increment -d '{"amount": 10}'
# {"value": 10, "message": "..."}

# Check it
curl http://localhost:8081/api/counter/count
# {"value": 10}

# Reset
curl -X DELETE http://localhost:8081/api/counter/reset
# {"value": 0, "message": "..."}
```

---

That's it. Three endpoints. Easy.
