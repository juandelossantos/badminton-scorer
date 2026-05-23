# Resumen del Proyecto Badminton Scorer

## Último Estado
**Fase Completada:** Frontend Core UI alineado con DESIGN.md (Tasks 8-11 de 22)
**Commit Actual:** `5951871` en rama `main`

## Progreso
- [x] Phase 1: Foundation (Docker, DB Schema, Health Check)
- [x] Phase 2: Core API (CRUD + Scoring Logic, 17 tests pass)
- [x] Phase 3: Frontend Foundation (Vite + React + Router + Theme)
- [x] Phase 4 (parcial): Core UI básico alineado con DESIGN.md
  - [x] Grid sutil de fondo (1px cada 40px)
  - [x] Cancha SVG como background (12% dark / 8% light)
  - [x] Match Controller: header, sets bar, players row, scoreboard, controls, footer tabs
  - [x] Match TV: sin controles, glow intenso, badge set verde
  - [x] Create Match: form con focus verde + glow
  - [x] Iconos SVG (sol/luna) — sin emojis
- [ ] Phase 4 (resto): Sonidos 8-bit, undo visual, responsive landscape, celebration canvas
- [ ] Phase 5: Integration (Polling, Compartir, QR)
- [ ] Phase 6: Polish (Cleanup, prefers-reduced-motion)
- [ ] Phase 7: Ship (Build + Deploy)

## Pruebas con Playwright Realizadas ✅

### Home Page
- ✅ Carga con grid sutil y tema oscuro
- ✅ Tipografía JetBrains Mono correcta
- ✅ Botón "NUEVO PARTIDO" funcional

### Create Match Form
- ✅ Navegación desde Home
- ✅ Toggle Individual/Dobles
- ✅ Inputs con focus verde + glow sutil
- ✅ Botón CREAR PARTIDO full-width verde
- ✅ POST a backend exitoso

### Match Controller (tema oscuro)
- ✅ Grid sutil visible en #0c0c0c
- ✅ Header: EN VIVO (izq, dot pulsante) + INDIVIDUAL (der)
- ✅ Sets bar: "SETS" label, score "0—0", badge "Set 1/5"
- ✅ Players row: Juan (izq) vs Pedro (der), VS centrado
- ✅ Status servidor: "SERVICIO → IZQUIERDA" / "RECIBE"
- ✅ Cancha SVG como background sutil
- ✅ Marcador: 0 verde (glow) vs 0 ámbar (glow)
- ✅ Dot rojo (5px) indicando servidor
- ✅ Labels "PTS" debajo de cada número
- ✅ Botones +: verde #059669 y ámbar #d97706, 44×36px exactos
- ✅ Botones −: fondo elevated #1e293b, borde sutil
- ✅ Botón "COMPARTIR URL TV" visible en 0-0
- ✅ Botón "FINALIZAR PARTIDO" sutil, rojo en hover
- ✅ Footer tabs: MARCADOR (verde, activo) | DETALLES | icono sol SVG
- ✅ Toggle tema: oscuro ↔ claro

### Match Controller (tema claro)
- ✅ Fondo blanco #ffffff con grid sutil gris
- ✅ Sin glow en números (solo dark tiene glow)
- ✅ Colores de acento mantenidos
- ✅ Cancha SVG con 8% opacity

### Match TV (solo dark)
- ✅ Fondo #0c0c0c con grid
- ✅ Header: EN VIVO + badge verde semi-transparente "Set 1/5"
- ✅ Marcadores grandes (7rem) con glow intenso (0.4)
- ✅ Nombres debajo del marcador
- ✅ Footer: modo + puntos al 21 | servidor + lado
- ✅ Sin controles
- ✅ Cancha SVG background

### Responsive
- ✅ Desktop (1280×720): layout completo
- ✅ Móvil (375×812): layout adaptado

### Funcionalidad API
- ✅ POST crear partido
- ✅ PUT score +1 punto
- ✅ PUT undo −1 punto
- ✅ GET leer partido
- ✅ Polling cada 3s
- ✅ Auto-redirect a celebration al terminar

## Estructura de Archivos
```
frontend/
├── src/
│   ├── api/matches.js           # Cliente HTTP
│   ├── components/
│   │   └── common/
│   │       ├── CanchaBG.jsx     # SVG cancha background
│   │       └── Icons.jsx        # SunIcon, MoonIcon SVG
│   ├── context/ThemeContext.jsx  # Dark/Light toggle
│   ├── pages/
│   │   ├── Home.jsx              # Pantalla de inicio
│   │   ├── CreateMatch.jsx       # Formulario
│   │   ├── Match.jsx             # Controlador (DESIGN.md compliant)
│   │   ├── MatchTV.jsx           # Vista TV (DESIGN.md compliant)
│   │   └── Celebration.jsx       # Fin de partido
│   └── styles/global.css         # Design system completo
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
3. QR code para compartir
4. Landscape responsive layout
5. Delete/cleanup de partidos antiguos (cron)
6. Build de producción y deploy
