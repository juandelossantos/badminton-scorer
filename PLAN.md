# Implementation Plan: Badminton Scorer

> **Status:** PLAN READY — Awaiting approval before BUILD phase  
> **Based on:** `spec.md` + `DESIGN.md`  
> **Date:** 2026-05-23

---

## Architecture Decisions

1. **Vertical Slicing** — Cada feature se implementa de punta a punta (DB → API → UI) para tener funcionalidad testeable desde el inicio.
2. **TDD Obligatorio** — Tests antes o junto con código. No excepciones.
3. **Docker Solo Backend** — El entorno de desarrollo local usa Docker para backend (PHP + MySQL + phpMyAdmin). El frontend corre con `npm run dev` nativo.
4. **Deploy Producción** — SHIP incluye instrucciones para crear DB en servidor, subir backend por FTP y frontend por FTP.
4. **ID Corto para URLs** — `/match/7A3F` generado alfanumérico de 8 caracteres.
5. **Primer Nombre Solo** — En UI se muestra solo el primer nombre de cada jugador.

---

## Dependency Graph

```
Phase 1: Foundation (BUILD)
├── Docker Compose (backend + db + phpmyadmin ONLY)
├── Database Schema (matches table)
└── Backend Base (config, router, health check)

Phase 2: Core API
├── POST /matches (create)
├── GET /matches/:id (read)
├── PUT /matches/:id/score (increment/decrement)
└── Business Logic (reglas de bádminton)

Phase 3: Frontend Skeleton
├── Vite + React setup
├── React Router + 5 routes
├── ThemeContext (dark/light)
└── Global CSS (tokens del DESIGN.md)

Phase 4: UI Components
├── Common: Header, SetsBar, PlayersRow, AppFooter
├── CreateMatch form + confirmation screen
├── MatchController: Scoreboard + Controls
├── MatchTV: spectator view
└── Celebration: Fireworks + result display

Phase 5: Integration
├── Polling sync (frontend ↔ backend)
├── Share button logic (clipboard)
├── Sound effects (click, alert, celebration)
└── Auto-redirect on match end

Phase 6: Polish
├── Responsive breakpoints
├── Reduced motion support
├── Error handling
└── Final E2E tests

Phase 7: Ship (Deploy)
├── Production Database Setup
├── Production Frontend Deploy (FTP)
└── Production Backend Deploy (FTP)
```

---

## Task List

### Phase 1: Foundation

#### Task 1: Docker Compose Setup (Backend ONLY)
**Description:** Crear `docker-compose.yml` con 3 servicios: backend (PHP-Apache), db (MySQL), phpMyAdmin. Frontend corre con `npm run dev` nativo (fuera de Docker). Incluir healthchecks y volúmenes para hot reload del backend.

**Acceptance criteria:**
- [ ] `docker-compose up -d` levanta los 3 servicios (backend, db, phpmyadmin) sin errores
- [ ] Backend accesible en `localhost:8000` (devuelve 200 en `/api/health`)
- [ ] MySQL accesible en `localhost:3306`
- [ ] phpMyAdmin accesible en `localhost:8080`
- [ ] Frontend NO está en Docker (se levanta con `npm run dev` por separado)

**Verification:**
```bash
docker-compose ps  # backend, db, phpmyadmin UP
curl http://localhost:8000/api/health  # {"status":"ok"}
cd frontend && npm run dev  # localhost:3000 por separado
```

**Dependencies:** None
**Files touched:** `docker-compose.yml`, `backend/Dockerfile`
**Estimated scope:** Medium

---

#### Task 2: Database Schema
**Description:** Crear `database/schema.sql` con tabla `matches` según spec. ID VARCHAR(8) como PK. Campos para modo, jugadores (JSON), sets, puntos, estado, servidor. Índices optimizados.

**Acceptance criteria:**
- [ ] `mysql -h localhost -P 3306 -u root -prootpass < database/schema.sql` ejecuta sin errores
- [ ] Tabla `matches` existe con todas las columnas del spec
- [ ] Índices `status_updated` y `created_at` existen
- [ ] Se puede hacer INSERT de prueba y SELECT

**Verification:**
```bash
docker-compose exec db mysql -u root -prootpass -e "DESCRIBE badminton_scorer.matches;"
```

**Dependencies:** Task 1 (Docker debe estar corriendo)
**Files touched:** `database/schema.sql`
**Estimated scope:** Small

---

#### Task 3: Backend Base + Health Check
**Description:** Estructura base del backend PHP: `config/database.php` (PDO), router simple en `index.php`, endpoint `/api/health` que devuelve `{"status":"ok"}`. Manejo de CORS.

**Acceptance criteria:**
- [ ] `GET /api/health` devuelve 200 + JSON
- [ ] CORS headers presentes en toda respuesta
- [ ] Conexión a MySQL funciona desde PHP
- [ ] Errores devuelven JSON, no HTML

**Verification:**
```bash
curl -I http://localhost:8000/api/health  # HTTP/1.1 200
```

**Dependencies:** Task 1
**Files touched:** `backend/config/database.php`, `backend/index.php`, `backend/.htaccess`
**Estimated scope:** Small

---

### Checkpoint 1: Foundation
- [ ] Docker levanta 3 servicios (backend, db, phpmyadmin) — frontend fuera de Docker
- [ ] Database schema importado
- [ ] Backend health check responde 200
- [ ] **Review con humano antes de continuar**

---

### Phase 2: Core API

#### Task 4: API — Create Match
**Description:** Endpoint `POST /api/matches`. Recibe JSON con mode, player1[], player2[], sets_to_win, points_per_set. Genera ID único de 8 chars. Inserta en DB. Devuelve match completo.

**Acceptance criteria:**
- [ ] `POST /api/matches` con body válido devuelve 201 + match object
- [ ] ID generado es alfanumérico de 8 caracteres
- [ ] Datos guardados correctamente en DB
- [ ] Validación: mode debe ser 'singles' o 'doubles', nombres no vacíos
- [ ] Error: 400 para datos inválidos, 500 para errores de DB

**Verification:**
```bash
curl -X POST http://localhost:8000/api/matches \
  -H "Content-Type: application/json" \
  -d '{"mode":"doubles","player1":["Juan","Maria"],"player2":["Pedro","Ana"],"sets_to_win":3,"points_per_set":21}'
```

**Dependencies:** Checkpoint 1
**Files touched:** `backend/api/matches/create.php`, `backend/models/Match.php`
**Estimated scope:** Medium

---

#### Task 5: API — Get Match + Tests
**Description:** Endpoint `GET /api/matches?id=:id`. Devuelve estado completo del partido. Tests unitarios para formateo de datos.

**Acceptance criteria:**
- [ ] `GET /api/matches?id=7A3F` devuelve 200 + match object completo
- [ ] Match no encontrado → 404
- [ ] Tests unitarios: formato de respuesta, sets_data parseado

**Verification:**
```bash
# Primero crear un match, luego:
curl http://localhost:8000/api/matches?id=7A3F
# Debe incluir: id, mode, player1, player2, current_set, current_score, sets, status
```

**Dependencies:** Task 4
**Files touched:** `backend/api/matches/read.php`, `backend/models/Match.php`, `backend/tests/...`
**Estimated scope:** Medium

---

#### Task 6: API — Update Score + Business Logic
**Description:** Endpoint `PUT /api/matches?id=:id/score`. Recibe `{player: 1|2, action: "increment"|"decrement"}`. Implementa TODAS las reglas del bádminton: rally point (1 por rally), 21 pts con diferencia 2, deuce hasta 30, detección de ganador de set, detección de ganador de partido (mayoría de sets), cambio de servicio. NO permitir decrementar si set ya terminó.

**Acceptance criteria:**
- [ ] `increment` suma 1 punto al jugador indicado
- [ ] `decrement` resta 1 punto (mínimo 0), BLOQUEADO si set ya tiene ganador
- [ ] Set gana a 21 con diferencia de 2 (ej: 21-19, 22-20)
- [ ] Deuce: 20-20 continúa hasta diferencia de 2, máximo 30 (30-29 gana)
- [ ] Partido gana quien tenga mayoría de sets (3 sets → 2 ganados)
- [ ] Servicio cambia según reglas: par=derecha, impar=izquierda
- [ ] Status cambia a 'completed' cuando hay ganador
- [ ] Tests unitarios: todos los casos edge (deuce, 30-29, 3 sets, 5 sets)

**Verification:**
```bash
# Test cases manuales:
curl -X PUT "http://localhost:8000/api/matches?id=7A3F/score" -d '{"player":1,"action":"increment"}'
# Hacer 21 veces para P1 y verificar set winner
curl http://localhost:8000/api/matches?id=7A3F
# Verificar: sets[0].winner = 1, current_set = 2, status = 'active'
```

**Dependencies:** Task 5
**Files touched:** `backend/api/matches/score.php`, `backend/models/Match.php`
**Estimated scope:** **LARGE** (reglas complejas, muchos tests)

---

#### Task 7: API — End Match
**Description:** Endpoint `PUT /api/matches?id=:id/end`. Finaliza partido manualmente. Cambia status a 'completed'. Devuelve match final.

**Acceptance criteria:**
- [ ] `PUT /api/matches?id=7A3F/end` cambia status a 'completed'
- [ ] Si ya está completed → 409 Conflict
- [ ] Devuelve match completo con winner (si hay) o null

**Verification:**
```bash
curl -X PUT "http://localhost:8000/api/matches?id=7A3F/end"
```

**Dependencies:** Task 6
**Files touched:** `backend/api/matches/end.php`
**Estimated scope:** Small

---

### Checkpoint 2: Core API
- [ ] Todos los endpoints responden correctamente
- [ ] Tests unitarios de reglas de bádminton pasan
- [ ] Business logic validado manualmente (crear + jugar + ganar)
- [ ] **Review con humano antes de continuar**

---

### Phase 3: Frontend Skeleton

#### Task 8: Vite + React + Router Setup
**Description:** Configurar frontend con Vite, React, React Router DOM. Crear 5 rutas vacías (Home, CreateMatch, Match, MatchTV, Celebration). ThemeContext con dark/light toggle (persiste en localStorage). `global.css` con variables CSS del DESIGN.md.

**Acceptance criteria:**
- [ ] `npm run dev` levanta en `localhost:3000`
- [ ] Navegar a `/`, `/new`, `/match/123`, `/match/123/tv`, `/match/123/result` muestra rutas distintas
- [ ] Theme toggle funciona y persiste en localStorage
- [ ] Variables CSS aplicadas correctamente (fondo oscuro/claro)

**Verification:**
```bash
cd frontend && npm run dev
# Visitar http://localhost:3000 y probar rutas
```

**Dependencies:** Checkpoint 1
**Files touched:** `frontend/package.json`, `frontend/vite.config.js`, `frontend/index.html`, `frontend/src/main.jsx`, `frontend/src/App.jsx`, `frontend/src/context/ThemeContext.jsx`, `frontend/src/styles/global.css`
**Estimated scope:** Medium

---

### Checkpoint 3: Frontend Skeleton
- [ ] Frontend levanta sin errores
- [ ] 5 rutas navegables
- [ ] Theme toggle funciona
- [ ] **Review con humano antes de continuar**

---

### Phase 4: UI Components (Vertical Slicing)

#### Task 9: Home + Create Match Form
**Description:** Pantalla Home con botón "NUEVO PARTIDO". Pantalla Create Match con formulario: select mode (singles/dobles), inputs nombres (1 o 2 según modo), select sets (1/3/5), select points (15/21). Al crear, muestra pantalla de confirmación con URL del partido y botón COMPARTIR.

**Acceptance criteria:**
- [ ] Home muestra título y botón CTA
- [ ] Formulario valida: nombres no vacíos, modo seleccionado
- [ ] Al crear: POST a API, muestra ID y URL TV, botón COMPARTIR copia al clipboard
- [ ] Navegación a `/match/:id` después de confirmar

**Verification:**
```bash
# Manual: crear partido, verificar URL mostrada, copiar al clipboard
```

**Dependencies:** Checkpoint 2 + 3
**Files touched:** `frontend/src/pages/HomePage.jsx`, `frontend/src/pages/CreateMatchPage.jsx`, `frontend/src/api/matches.js`
**Estimated scope:** Medium

---

#### Task 10: Common Components
**Description:** Implementar componentes compartidos: Header (EN VIVO + modo), SetsBar (SETS X-Y, badge set actual), PlayersRow (nombres + VS), AppFooter (tabs + theme toggle).

**Acceptance criteria:**
- [ ] Header: dot verde parpadeante + modo (DOBLES/INDIVIDUAL)
- [ ] SetsBar: muestra sets ganados y set actual
- [ ] PlayersRow: nombres primer nombre solo, VS centrado
- [ ] AppFooter: tabs MARCADOR/DETALLES + icono tema
- [ ] Estilos dark/light correctos según DESIGN.md

**Verification:**
```bash
# Ver en navegador con ambos temas
```

**Dependencies:** Task 8
**Files touched:** `frontend/src/components/common/Header.jsx`, `SetsBar.jsx`, `PlayersRow.jsx`, `AppFooter.jsx`
**Estimated scope:** Medium

---

#### Task 11: Match Controller (Vertical Slice)
**Description:** Pantalla de control completa: Scoreboard (números grandes + cancha SVG), Controls (+/− por jugador, FINALIZAR), Share button (solo visible cuando 0-0), polling cada 2s. Integra todos los common components.

**Acceptance criteria:**
- [ ] Scoreboard: números grandes 5.5rem con glow, cancha SVG background
- [ ] Controls: +/− funcionan, llaman a PUT /score
- [ ] Share button: visible SOLO cuando score es 0-0, copia URL TV
- [ ] Finalizar: pregunta confirmación, llama PUT /end, redirige a /result
- [ ] Polling: actualiza score cada 2 segundos desde backend
- [ ] Responsive: portrait y landscape auto-detect

**Verification:**
```bash
# Crear partido, controlar puntos, verificar sincronización
# Probar share button en 0-0 y verificar que desaparece después de primer punto
```

**Dependencies:** Task 9 + 10
**Files touched:** `frontend/src/pages/MatchPage.jsx`, `frontend/src/components/match-controller/Scoreboard.jsx`, `Controls.jsx`
**Estimated scope:** **LARGE**

---

#### Task 12: Match TV (Spectator View)
**Description:** Vista solo dark para espectadores. Números grandes (7rem), set destacado, sin controles. Polling cada 3s. Auto-redirect a /result cuando status = 'completed'.

**Acceptance criteria:**
- [ ] Siempre dark, sin toggle de tema
- [ ] Números 7rem con glow intenso
- [ ] Set actual destacado con badge verde
- [ ] Sin botones de control
- [ ] Polling cada 3s
- [ ] Detecta status 'completed' → redirige a `/match/:id/result`

**Verification:**
```bash
# Abrir TV URL en navegador, simular partido terminado, verificar redirect
```

**Dependencies:** Task 11
**Files touched:** `frontend/src/pages/MatchTVPage.jsx`, `frontend/src/components/match-tv/*.jsx`
**Estimated scope:** Medium

---

#### Task 13: Celebration Screen
**Description:** Canvas fireworks animation. Muestra ganadores, resultado final (sets detallados). Botón INICIO para controlador. Loop infinito para TV.

**Acceptance criteria:**
- [ ] Fireworks: partículas verdes y ámbar, canvas 2D
- [ ] Muestra nombres ganadores, resultado final, detalle de cada set
- [ ] Controlador: botón INICIO redirige a `/` (home)
- [ ] TV: sin botones, animación en loop
- [ ] Reduced motion: versión estática sin animación

**Verification:**
```bash
# Simular partido terminado, verificar celebration en ambas vistas
```

**Dependencies:** Task 12
**Files touched:** `frontend/src/pages/CelebrationPage.jsx`, `frontend/src/components/celebration/Fireworks.jsx`
**Estimated scope:** Medium

---

### Checkpoint 4: UI Complete
- [ ] Todas las 5 pantallas funcionan
- [ ] Flujo completo: crear → controlar → finalizar → celebración
- [ ] TV auto-redirect funciona
- [ ] Share button aparece/desaparece correctamente
- [ ] **Review con humano antes de continuar**

---

### Phase 5: Integration & Polish

#### Task 14: Sound Effects
**Description:** Agregar sonidos estilo 8-bit retro (tipo Mario): click corto al punto, alerta al ganar set, celebración al ganar partido. Usar Web Audio API para generar sonidos sintéticos (square wave, triangle wave) sin archivos externos. Respetar `prefers-reduced-motion` (silencio si usuario prefiere reducir).

**Acceptance criteria:**
- [ ] Sonido al incrementar punto: beep corto 8-bit (square wave, ~200ms)
- [ ] Sonido al ganar set: alerta melodía corta 8-bit (ascendente)
- [ ] Sonido de celebración al ganar partido: fanfarria 8-bit completa
- [ ] Todos los sonidos generados via Web Audio API (sin archivos .mp3/.wav)
- [ ] Silencio total si usuario tiene `prefers-reduced-motion`

**Verification:**
```bash
# Manual: jugar partido completo y escuchar sonidos
```

**Dependencies:** Checkpoint 4
**Files touched:** `frontend/src/utils/sounds.js`
**Estimated scope:** Small

---

#### Task 15: Error Handling & Edge Cases
**Description:** Manejo de errores: API caída, partido no encontrado, network timeout, validación de inputs. Pantallas de error amigables.

**Acceptance criteria:**
- [ ] API error → mensaje claro al usuario (no consola)
- [ ] Match no encontrado → "Partido no existe o ya fue eliminado"
- [ ] Timeout → "Conexión lenta, reintentando..."
- [ ] Input vacío → validación inline
- [ ] Reintentar polling automático después de error

**Verification:**
```bash
# Matar backend y verificar mensaje de error en frontend
```

**Dependencies:** Checkpoint 4
**Files touched:** Múltiples archivos frontend (error boundaries, API client)
**Estimated scope:** Medium

---

#### Task 16: E2E Tests
**Description:** Tests end-to-end con Playwright. Flujo completo: crear partido, anotar puntos hasta ganar, verificar celebration.

**Acceptance criteria:**
- [ ] Test: crear partido → aparece en DB
- [ ] Test: anotar 21 puntos → detecta ganador de set
- [ ] Test: ganar mayoría de sets → partido completed
- [ ] Test: TV view → redirige a celebration
- [ ] Test: share button → desaparece después del primer punto
- [ ] Test: celebration → fireworks visibles

**Verification:**
```bash
npm run test:e2e
```

**Dependencies:** Task 14 + 15
**Files touched:** `e2e/create-match.spec.js`, `e2e/play-match.spec.js`, `e2e/celebration.spec.js`
**Estimated scope:** **LARGE**

---

### Checkpoint 5: Integration Complete
- [ ] Sonidos funcionan
- [ ] Errores manejados gracefulmente
- [ ] E2E tests pasan
- [ ] **Review con humano antes de continuar**

---

### Phase 6: Final Polish

#### Task 17: Responsive & Accessibility
**Description:** Probar responsive en múltiples breakpoints. Asegurar touch targets mínimos. Verificar contrast ratios WCAG.

**Acceptance criteria:**
- [ ] Portrait: 375px funciona correctamente
- [ ] Landscape: 480px funciona correctamente
- [ ] TV: 640px+ funciona correctamente
- [ ] Touch targets ≥ 44px
- [ ] Contrast ratios pasan WCAG AA

**Verification:**
```bash
# Manual: probar en Chrome DevTools con diferentes device sizes
```

**Dependencies:** Checkpoint 5
**Files touched:** `frontend/src/styles/global.css` (media queries)
**Estimated scope:** Small

---

#### Task 18: Performance Optimization
**Description:** Optimizar bundle size, lazy loading de rutas, minimizar re-renders. Verificar Core Web Vitals.

**Acceptance criteria:**
- [ ] Bundle size < 200KB gzipped
- [ ] LCP < 2.5s
- [ ] CLS < 0.1
- [ ] React Router lazy loading implementado

**Verification:**
```bash
npm run build
# Analizar bundle con `vite-bundle-visualizer`
```

**Dependencies:** Task 17
**Files touched:** `frontend/src/App.jsx` (lazy imports)
**Estimated scope:** Small

---

#### Task 19: Final Review & Documentation
**Description:** Revisar README.md. Actualizar DESIGN.md si hubo cambios visuales. Verificar que todo el código sigue el spec y el DESIGN.md.

**Acceptance criteria:**
- [ ] README.md tiene instrucciones claras de instalación
- [ ] DESIGN.md refleja lo implementado
- [ ] spec.md está marcado como COMPLETE
- [ ] Código sin console.log, sin credenciales hardcodeadas

**Verification:**
```bash
# Code review manual
```

**Dependencies:** Task 18
**Files touched:** `README.md`, `DESIGN.md`, `spec.md`
**Estimated scope:** Small

---

### Checkpoint 6: Polish Complete
- [ ] Performance ok
- [ ] Documentación actualizada
- [ ] Código limpio
- [ ] **Review con humano antes de continuar**

---

### Phase 7: Ship (Deploy a Producción)

#### Task 20: Production Database Setup
**Description:** Instrucciones para crear la base de datos en el servidor de producción. Script SQL para crear DB, usuario, tabla. Configuración de `backend/config/database.php` con credenciales de producción (variables de entorno).

**Acceptance criteria:**
- [ ] Script `database/prod-setup.sql` crea DB + usuario + tabla en servidor MySQL
- [ ] `backend/config/database.php` lee credenciales de variables de entorno
- [ ] Documentación clara: cómo crear DB en cPanel/phpMyAdmin del hosting

**Verification:**
```bash
# En servidor de producción:
mysql -u usuario -p < database/prod-setup.sql
```

**Dependencies:** Checkpoint 6
**Files touched:** `database/prod-setup.sql`, `backend/config/database.php`, `README.md`
**Estimated scope:** Small

---

#### Task 21: Production Frontend Deploy
**Description:** Instrucciones para compilar y subir frontend a producción. `npm run build` genera `dist/`. Subir por FTP/SFTP al directorio público del hosting.

**Acceptance criteria:**
- [ ] `npm run build` genera `frontend/dist/` optimizado
- [ ] Archivos estáticos listos para FTP
- [ ] README.md tiene pasos: compilar → subir dist/ → configurar .htaccess para SPA routing
- [ ] API base URL configurable por variable de entorno

**Verification:**
```bash
cd frontend && npm run build
# Subir dist/ por FTP al public_html/
```

**Dependencies:** Checkpoint 6
**Files touched:** `frontend/.env.production`, `README.md`
**Estimated scope:** Small

---

#### Task 22: Production Backend Deploy
**Description:** Instrucciones para subir backend PHP por FTP. Incluir `.htaccess` para routing, configuración CORS para dominio de producción.

**Acceptance criteria:**
- [ ] Backend subido por FTP a directorio del hosting
- [ ] `.htaccess` configurado para rewrites de API
- [ ] CORS configurado para dominio de producción (no `*`)
- [ ] phpMyAdmin del hosting accesible para gestionar DB

**Verification:**
```bash
# Subir backend/ por FTP
# Verificar: curl https://tudominio.com/api/health
```

**Dependencies:** Task 20 + 21
**Files touched:** `backend/.htaccess`, `README.md`
**Estimated scope:** Small

---

### Checkpoint 7: Ready to Ship
- [ ] DB creada en servidor producción
- [ ] Backend funcionando en producción
- [ ] Frontend funcionando en producción
- [ ] README.md tiene instrucciones completas de deploy
- [ ] **APROBACIÓN FINAL DEL HUMANO**

---

## Risks and Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Reglas de bádminton mal implementadas | **HIGH** | Tests exhaustivos para todos los casos edge (deuce, 30-29, 5 sets) |
| Polling consume mucho ancho de banda | **MED** | Intervalo 3s para TV, 2s para controlador. Cleanup de partidos antiguos |
| Canvas fireworks no funcionan en todos los navegadores | **LOW** | Fallback a CSS animation simple. Respetar prefers-reduced-motion |
| Docker no funciona en el entorno del usuario | **MED** | Documentar requisitos (Docker Desktop). Proporcionar alternativa manual |
| CORS issues entre frontend y backend | **MED** | Configurar CORS explícitamente. Usar proxy en dev (vite.config.js) |

---

## Total Estimated Effort

| Phase | Tasks | Scope Total |
|-------|-------|-------------|
| Foundation | 3 | Medium |
| Core API | 4 | **Large** |
| Frontend Skeleton | 1 | Medium |
| UI Components | 5 | **Very Large** |
| Integration | 3 | **Large** |
| Polish | 3 | Medium |
| Ship | 3 | Small |
| **Total** | **22** | **~18-22 horas de agente** |

---

## Open Items

- [ ] Usuario debe aprobar este plan antes de comenzar BUILD
- [ ] Confirmar: Task 16 (E2E tests) puede ejecutarse en el entorno Docker
- [ ] Confirmar: Sonidos se implementan con archivos .mp3/.wav o Web Audio API generado
