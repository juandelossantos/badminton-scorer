# Badminton Scorer

Marcador de bádminton en tiempo real con URL compartible. Diseño estilo hacker/terminal, optimizado para árbitros en cancha y espectadores remotos.

## Características Principales

- 🏸 **Controlador en cancha** — Móvil portrait/landscape con botones grandes para árbitros
- 📺 **Vista TV** — Números gigantes visibles a 20 metros, sincronización en tiempo real
- 🔗 **URL compartible** — Cada partido tiene URL única de 8 caracteres
- 🔒 **Seguridad** — Token de control privado evita que espectadores modifiquen el marcador
- 🎵 **Sonidos 8-bit** — Retro tipo Mario al anotar punto, ganar set y ganar partido
- 🎆 **Celebración** — Fuegos artificiales automáticos al terminar el partido
- 🌙 **Dark/Light** — Toggle de tema con persistencia en localStorage
- 📱 **Responsive** — Portrait (móvil), Landscape (móvil horizontal), TV (pantalla ancha)
- ⚡ **Sin autenticación** — Abrir y jugar, sin registro

## Stack Tecnológico

| Capa | Tecnología |
|---|---|
| Frontend | React 19 + Vite + React Router DOM |
| Backend | PHP 8.1 + PDO + MySQL 8.0 |
| Sync | Polling HTTP cada 3 segundos |
| Animaciones | Partycles (fireworks) |
| Sonidos | Web Audio API (8-bit sintético) |
| Hosting | Servidor propio (PHP + MySQL) |

## Estructura del Proyecto

```
badminton-scorer/
├── frontend/               # React SPA (Vite)
│   ├── src/
│   │   ├── pages/         # Home, CreateMatch, Match, MatchTV, ShareURL, Celebration
│   │   ├── hooks/         # useSound (Web Audio API)
│   │   ├── api/           # matches.js (API client)
│   │   ├── context/       # ThemeContext (dark/light)
│   │   ├── components/    # CanchaBG, Icons
│   │   └── styles/        # global.css (design system)
│   ├── e2e/               # Playwright E2E tests
│   └── .env.production    # API URL para build
├── backend/                # PHP REST API
│   ├── handlers/          # create, read, score, end
│   ├── models/            # MatchModel.php (business logic)
│   ├── config/            # database.php
│   └── .htaccess          # Rewrite rules + CORS
├── database/
│   └── prod-setup.sql     # Schema para producción
├── DESIGN.md              # Sistema de diseño (tokens, colores, tipografía)
├── spec.md                # Especificación funcional
├── DEPLOY.md              # Guía de despliegue paso a paso
└── PLAN.md                # Plan de implementación (22 tareas)
```

## Rutas

| Pantalla | Ruta | Descripción |
|---|---|---|
| Inicio | `/` | Landing, botón "Nuevo Partido" |
| Crear Partido | `/create` | Formulario: modo (individual/dobles), jugadores |
| Compartir URLs | `/share` | Muestra URLs de controlador y TV |
| Controlador | `/match/:id?token=...` | Suma/resta puntos (requiere token) |
| TV Espectador | `/watch/:id` | Solo lectura, sin token |
| Celebración | `/match/:id/celebration` | Ganador + fireworks |

## Instalación Local

### Prerrequisitos
- Docker + Docker Compose
- Node.js 18+
- npm

### Backend (Docker)

```bash
# Levantar backend + MySQL + phpMyAdmin
docker compose up -d

# Verificar salud
curl http://localhost:8000/api/health
# → {"status":"ok","database":"connected"}
```

### Frontend

```bash
cd frontend
npm install
npm run dev        # http://localhost:5173
```

### Tests

```bash
# Backend (PHPUnit)
docker exec test-badminton-scorer-backend-1 php vendor/bin/phpunit

# E2E (Playwright)
cd frontend
npx playwright test
```

## API Endpoints

| Método | Endpoint | Body | Descripción |
|---|---|---|---|
| POST | `/api/matches` | `{mode, player1[], player2[]}` | Crear partido |
| GET | `/api/matches/:id` | — | Obtener partido |
| PUT | `/api/matches/:id/score` | `{player, token, undo?}` | Sumar/restar punto |
| PUT | `/api/matches/:id/end` | `{status, winner, token}` | Finalizar partido |

## Reglas de Bádminton Implementadas

- Sistema de puntos rally (1 punto por rally)
- 21 puntos para ganar un set
- Diferencia mínima de 2 puntos
- Deuce hasta 30 puntos máximo
- Partido al mejor de 3 sets (2 ganados)
- Cambio de servicio según puntaje (par=derecha, impar=izquierda)
- Detección automática de ganador de set y partido

## Seguridad

- `control_token` de 32 caracteres generado aleatoriamente por partido
- Solo quien crea el partido recibe el token (en la URL del controlador)
- El backend rechaza operaciones de puntuación sin token válido (401 Unauthorized)
- Los espectadores ven `/watch/:id` (solo lectura, sin token)
- Los árbitros controlan desde `/match/:id?token=...`

## Despliegue

Ver [DEPLOY.md](DEPLOY.md) para instrucciones completas de despliegue a producción.

Resumen rápido:
1. Importar `database/prod-setup.sql` en MySQL
2. Configurar `.env` con credenciales de DB
3. Subir `backend/` al servidor
4. Compilar frontend con `npm run build`
5. Subir `dist/` a `public_html/`
6. Configurar `.htaccess` para SPA routing

## Decisiones de Arquitectura

- **Sin WebSockets**: Polling HTTP cada 3s en lugar de WebSockets para compatibilidad con hosting compartido
- **Sin autenticación**: Partidos son efímeros (24h), no se guarda historial
- **Token en URL**: Simplifica el flujo sin requerir login/cookies
- **Vertical slicing**: Cada feature implementada de punta a punta (DB → API → UI)
- **TDD obligatorio**: PHPUnit para backend, Playwright para E2E

## Licencia

MIT

---

> **Nota:** Este proyecto fue construido con workflow skill-driven usando especificaciones detalladas en `spec.md` y `DESIGN.md`.
