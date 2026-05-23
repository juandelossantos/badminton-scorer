# Resumen del Proyecto Badminton Scorer

## Último Estado
**Fase Completada:** Frontend Core UI + ShareURL (Tasks 8-12 de 22)
**Commit Actual:** `f564f31` en rama `main`

## Progreso
- [x] Phase 1: Foundation (Docker, DB Schema, Health Check)
- [x] Phase 2: Core API (CRUD + Scoring Logic, 17 tests pass)
- [x] Phase 3: Frontend Foundation (Vite + React + Router + Theme)
- [x] Phase 4 (parcial): Core UI alineado con DESIGN.md
  - [x] Grid sutil de fondo (1px cada 40px)
  - [x] Cancha SVG responsive (horizontal landscape, vertical portrait)
  - [x] Match Controller: header, sets bar, players row, scoreboard, controls, footer tabs
  - [x] Match TV: sin controles, glow intenso, badge set verde
  - [x] Create Match: form con focus verde + glow
  - [x] Share URL: post-creation page con URLs + QR + Start button
  - [x] Landscape layout: side-by-side para móvil horizontal
  - [x] Marcadores responsive con clamp()
- [ ] Phase 4 (resto): Sonidos 8-bit, celebration canvas, QR real, undo visual
- [ ] Phase 5: Integration (Polling optimizado, cleanup antiguos)
- [ ] Phase 6: Polish (prefers-reduced-motion, error boundaries)
- [ ] Phase 7: Ship (Build + Deploy)

## Endpoints API Completados y Verificados
| Método | Endpoint | Tests | Estado |
|---|---|---|---|
| GET | `/api/health` | — | ✅ Funcionando |
| POST | `/api/matches` | 5/5 pass | ✅ Funcionando |
| GET | `/api/matches/:id` | 2/2 pass | ✅ Funcionando |
| PUT | `/api/matches/:id/score` | 10/10 pass | ✅ Funcionando |
| PUT | `/api/matches/:id/end` | Manual OK | ✅ Funcionando |

## Pruebas E2E con Playwright (3 vistas obligatorias)

### Desktop (1280×720)
- ✅ Home: grid sutil, botón NUEVO PARTIDO
- ✅ Create Match: form completo, toggle Individual/Dobles
- ✅ Share URL: URLs visibles, botón Copiar, QR placeholder, INICIAR PARTIDO
- ✅ Match Controller: layout vertical, cancha horizontal, marcadores grandes
- ✅ Match TV: solo dark, glow intenso, badge set verde

### Mobile Portrait (375×812)
- ✅ Home: layout adaptado
- ✅ Create Match: form scrollable, inputs con focus verde
- ✅ Share URL: URLs con wrap, QR centrado
- ✅ Match Controller: layout vertical, cancha vertical, controles abajo
- ✅ Match TV: marcadores grandes, sin controles

### Mobile Landscape (812×375)
- ✅ Match Controller: layout horizontal!
  - Jugadores a la izquierda
  - Marcador al centro
  - Controles a la derecha
  - Cancha horizontal
- ✅ Marcadores responsive con clamp()
- ✅ Footer tabs visibles

### Funcionalidad verificada en todas las vistas
- ✅ POST crear partido → redirección a /share
- ✅ PUT score +1 punto
- ✅ PUT undo −1 punto
- ✅ GET leer partido
- ✅ Polling cada 3s
- ✅ Botón Compartir visible solo en 0-0
- ✅ Toggle tema oscuro/claro

## Cambios Recientes

### Cancha Responsive
- Portrait: SVG vertical (220×400 viewBox)
- Landscape/Desktop: SVG horizontal (400×220 viewBox)
- Detección via `window.innerWidth > window.innerHeight` + resize/orientationchange listeners

### Marcadores Responsive
- `font-size: clamp(3.5rem, 15vw, 7rem)` para controller
- `font-size: clamp(4rem, 18vw, 9rem)` para TV
- Se ajustan automáticamente al ancho del viewport

### Landscape Layout
- CSS `@media (orientation: landscape) and (max-height: 500px)`
- Players row → 30% width, vertical
- Scoreboard → 40% width, centrado
- Controls → 30% width, columna
- VS mini oculto

### Share URL Page
- Muestra URL del controlador + botón Copiar
- Muestra URL TV + botón Copiar
- QR placeholder (150×150px dashed border)
- Botón "INICIAR PARTIDO" verde full-width
- Navega a /match/:id al iniciar

## Estructura de Archivos
```
frontend/
├── src/
│   ├── api/matches.js           # Cliente HTTP
│   ├── components/
│   │   └── common/
│   │       ├── CanchaBG.jsx     # SVG cancha responsive (H/V)
│   │       └── Icons.jsx        # SunIcon, MoonIcon SVG
│   ├── context/ThemeContext.jsx  # Dark/Light toggle
│   ├── pages/
│   │   ├── Home.jsx              # Pantalla de inicio
│   │   ├── CreateMatch.jsx       # Formulario → /share
│   │   ├── ShareURL.jsx          # Post-creation URLs + QR + Start
│   │   ├── Match.jsx             # Controller (responsive)
│   │   ├── MatchTV.jsx           # TV view (solo dark)
│   │   └── Celebration.jsx       # Fin de partido
│   └── styles/global.css         # Design system + landscape MQ
backend/
├── handlers/
│   ├── health.php                # GET /api/health
│   └── matches/
│       ├── create.php            # POST /api/matches
│       ├── read.php              # GET /api/matches/:id
│       ├── score.php             # PUT /api/matches/:id/score
│       └── end.php               # PUT /api/matches/:id/end
├── models/MatchModel.php         # Lógica de negocio + reglas badminton
└── tests/                        # PHPUnit (17 tests, 57 assertions)
```

## Servidores de Desarrollo
- Frontend: `http://localhost:5173`
- Backend: `http://localhost:8000`
- phpMyAdmin: `http://localhost:8080`

## Repositorio
- Privado: https://github.com/juandelossantos/badminton-scorer
- Branch: `main`
- Commits atómicos por task

## Qué falta para MVP
1. Sonidos 8-bit (Web Audio API) — click, set, celebración
2. Animación celebration (canvas fireworks)
3. QR code real (qrcode.js)
4. Responsive: ajustes finos de tamaño de texto
5. Delete/cleanup de partidos antiguos (cron)
6. Build de producción y deploy
