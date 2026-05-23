# Resumen del Proyecto Badminton Scorer

## Último Estado
**Fase Completada:** Core API (Tasks 4-7 de 22)
**Fase Siguiente:** Frontend Foundation (Tasks 8-9)
**Commit Actual:** `5cdf5f3` en rama `main`

## Progreso
- [x] Phase 1: Foundation (Docker, DB Schema, Health Check)
- [x] Phase 2: Core API (CRUD + Scoring Logic)
- [ ] Phase 3: Frontend Foundation (Vite + React + Router)
- [ ] Phase 4: Frontend Core UI (Scoreboard, Cancha, Theme Toggle, Sonidos)
- [ ] Phase 5: Integration (Polling, Compartir, QR)
- [ ] Phase 6: Polish (Celebración, Undo, Cleanup)
- [ ] Phase 7: Ship (Build + Deploy)

## Endpoints API Completados y Verificados
| Método | Endpoint | Estado | Tests |
|---|---|---|---|
| POST | `/api/matches` | ✅ Funcionando | 5/5 pass |
| GET | `/api/matches/:id` | ✅ Funcionando | 2/2 pass |
| PUT | `/api/matches/:id/score` | ✅ Funcionando | 9/9 pass |
| PUT | `/api/matches/:id/end` | ✅ Funcionando | Manual OK |

## Reglas de Scoring Implementadas
- Sistema rally point (gana quien gana el rally)
- 21 puntos para ganar set, diferencia de 2, límite 30
- Server cambia al ganador del punto
- Lado de servicio: par = derecha, impar = izquierda
- Detección automática de fin de set y fin de partido
- Undo: quita 1 punto, no permite en sets terminados

## Estructura de Archivos Relevantes
- `backend/handlers/matches/create.php` - POST /api/matches
- `backend/handlers/matches/read.php` - GET /api/matches/:id
- `backend/handlers/matches/score.php` - PUT /api/matches/:id/score
- `backend/handlers/matches/end.php` - PUT /api/matches/:id/end
- `backend/models/MatchModel.php` - Lógica de negocio + persistencia
- `backend/tests/MatchCreateTest.php` - Tests creación
- `backend/tests/MatchReadTest.php` - Tests lectura
- `backend/tests/MatchScoreTest.php` - Tests scoring (9 tests)

## Docker Backend
- Levantado: `docker-compose up -d`
- Backend: http://localhost:8000
- phpMyAdmin: http://localhost:8080 (root/rootpass)
- Base de datos: `badminton_scorer` con tabla `matches`

## Repositorio Git
- Privado: https://github.com/juandelossantos/badminton-scorer
- Branch: `main`
- Commits atómicos por task
