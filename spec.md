# Spec: Badminton Scorer

## Objective

**What:** Aplicación web de marcador de bádminton en tiempo real con URL compartible. El árbitro en cancha controla el marcador desde su móvil y los espectadores remotos ven el marcador en tiempo real vía polling HTTP.

**Why:** Reemplazar marcadores manuales de papel o apps genéricas con una solución especializada para bádminton que sea fácil de compartir y ver a distancia.

**Who:**
- Árbitros/jugadores en cancha (controlan desde móvil)
- Espectadores remotos (PC, TV, tablet)
- Clubes de bádminton que transmiten partidos

**Success Criteria:**
- [ ] Crear un partido en < 30 segundos
- [ ] URL generada inmediatamente después de crear (ID corto: `/match/7A3F`)
- [ ] Pantalla post-creación muestra URL del partido + URL TV + botón COMPARTIR
- [ ] Botón COMPARTIR en controlador visible SOLO cuando score es 0-0, desaparece después del primer punto
- [ ] Sincronización en tiempo real (máx 3 segundos de delay)
- [ ] Funciona en móvil (portrait/landscape) y TV
- [ ] Reglas de bádminton implementadas correctamente (21 pts, diferencia 2, cambio de servicio)
- [ ] Botón "−" quita 1 punto por tap (sin historial de undo)
- [ ] No permitir decrementar en set ya terminado
- [ ] Nombres en dobles: solo primer nombre ("Juan / Maria", sin apellidos)
- [ ] TV auto-redirige a celebración cuando partido termina
- [ ] Sonidos: click al punto + alerta al set + celebración al partido
- [ ] Animación de celebración: canvas fireworks en loop para TV, botón INICIO para controlador
- [ ] Docker funcional para desarrollo local (PHP + MySQL + phpMyAdmin)

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Frontend | React SPA | 18.x |
| Build Tool | Vite | 5.x |
| Routing | React Router DOM | 6.x |
| Styling | CSS (no framework) | - |
| Backend | PHP REST API | 7.4+ |
| Database | MySQL | 5.7+ |
| Sync | HTTP Polling | cada 2-3s |

**Hosting:** Servidor propio con PHP/MySQL (no Vercel, no Server Actions).

## Commands

```bash
# Frontend dev
npm run dev          # localhost:3000

# Frontend build
npm run build        # output: dist/

# Database setup
mysql -u root -p < database/schema.sql

# Backend: servir con Apache/nginx + PHP
# .htaccess ya configurado en backend/
```

## Project Structure

```
badminton-scorer/
├── DESIGN.md                  # Sistema de diseño visual
├── spec.md                    # Este archivo — especificación funcional
├── frontend/                  # React SPA (Vite)
│   ├── src/
│   │   ├── pages/             # 5 pantallas: Home, CreateMatch, Match, MatchTV, Celebration
│   │   ├── components/
│   │   │   ├── common/        # Header, SetsBar, PlayersRow, AppFooter
│   │   │   ├── match-controller/  # Scoreboard, Controls (+/-, Finalizar)
│   │   │   ├── match-tv/      # TVScoreboard, TVSetsBar, TVNamesRow, TVFooter
│   │   │   └── celebration/   # Fireworks canvas
│   │   ├── context/
│   │   │   └── ThemeContext.jsx   # Dark/Light toggle
│   │   ├── api/
│   │   │   └── matches.js     # Cliente HTTP para API
│   │   └── styles/
│   │       └── global.css     # Todo el CSS del sistema
│   ├── package.json
│   ├── vite.config.js
│   └── index.html
├── backend/                   # PHP REST API
│   ├── api/matches/
│   │   ├── create.php         # POST /matches
│   │   ├── read.php           # GET /matches/:id
│   │   ├── score.php          # PUT /matches/:id/score (increment/decrement)
│   │   └── end.php            # PUT /matches/:id/end
│   ├── config/
│   │   └── database.php       # Conexión PDO
│   ├── models/
│   │   └── Match.php          # Lógica de negocio
│   └── .htaccess
└── database/
    └── schema.sql             # Tablas matches + events
```

## Code Style

### JavaScript/React
```javascript
// Componentes: PascalCase, funciones explícitas
function Scoreboard({ score }) {
  return (
    <div className="scoreboard-area">
      <div className="score-col">
        <div className="big-number p1">{score.p1}</div>
      </div>
    </div>
  );
}

// Hooks custom: use[Name]
function useMatchPolling(matchId) {
  // ...
}

// API: async/await, try/catch
async function getMatch(id) {
  try {
    const res = await fetch(`/api/matches?id=${id}`);
    return await res.json();
  } catch (err) {
    throw new Error(`Failed to fetch match: ${err.message}`);
  }
}
```

### PHP
```php
<?php
// Namespaces no disponibles en hosting compartido — usar prefijos
class MatchModel {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($data) {
        // Prepared statements obligatorios
        $stmt = $this->db->prepare("INSERT INTO matches (...) VALUES (...)");
        $stmt->execute([...]);
        return $this->getById($id);
    }
}
```

### CSS
```css
/* BEM-like naming sin ser estricto */
.scoreboard-area { /* block */ }
.big-number { /* element */ }
.big-number.p1 { /* modifier via clase */ }

/* Variables CSS con prefijo --bs- */
:root {
  --bs-bg-primary: #0c0c0c;
  --bs-accent-p1: #10b981;
}
```

## Testing Strategy

| Level | Framework | Location | Coverage |
|---|---|---|---|
| Unit | Vitest | `frontend/src/**/*.test.js` | Components puros, hooks |
| Integration | Vitest + MSW | `frontend/src/**/*.test.js` | API client, polling |
| E2E | Playwright | `e2e/*.spec.js` | Flujos completos: crear → controlar → terminar |

**Reglas:**
- Toda lógica de negocio (puntuación, sets, ganador) debe tener tests unitarios
- Los componentes de UI deben probarse con testing-library (interacciones, no estilos)
- E2E debe validar el flujo completo de creación a celebración

### Tests Críticos

```javascript
// Ejemplo de tests obligatorios
describe('Score Rules', () => {
  test('set winner at 21 with 2 point difference', () => {
    // P1: 21, P2: 18 → P1 wins set
  });
  
  test('deuce continues until 2 point difference', () => {
    // 20-20 → must reach 22-20 or 30-29
  });
  
  test('max points is 30', () => {
    // 30-29 → winner regardless of difference
  });
  
  test('minus button decrements by 1', () => {
    // Tap "−" → score decreases by 1. Tap again → decreases by 1 more.
    // No undo history. Simple decrement.
  });
});
```

## Boundaries

### Always Do
- [ ] Escribir tests antes o junto con el código (TDD)
- [ ] Validar inputs en frontend y backend
- [ ] Usar prepared statements en PHP (prevenir SQL injection)
- [ ] Manejar errores de API con mensajes claros al usuario
- [ ] Respetar `prefers-reduced-motion` para animaciones
- [ ] Limpiar partidos antiguos (24h para completados, 2h para abandonados)

### Ask First
- [ ] Agregar nuevas dependencias al proyecto
- [ ] Cambiar el stack tecnológico
- [ ] Modificar reglas de puntuación del bádminton
- [ ] Agregar persistencia de historial (MVP no guarda historial)
- [ ] Implementar autenticación

### Never Do
- [ ] Commit credenciales de base de datos
- [ ] Usar WebSockets (MVP usa polling por requisito del usuario)
- [ ] Agregar publicidad o tracking
- [ ] Modificar el DESIGN.md sin aprobación visual
- [ ] Hacer requests HTTP sin manejo de errores

## Routes / Pantallas

| Ruta | Pantalla | Props/Params | Acceso | Notas |
|---|---|---|---|---|
| `/` | Home | - | Público | Botón "NUEVO PARTIDO" |
| `/new` | Create Match | - | Público | Formulario: modo, nombres, sets, puntos |
| `/match/:id` | Match Controller | `id` | Público (creador) | Marcador con controles + botón COMPARTIR (0-0) + FINALIZAR |
| `/match/:id/tv` | Match TV | `id` | Público (espectador) | Solo lectura, dark, números grandes. Auto-redirect a `/result` al terminar |
| `/match/:id/result` | Celebration | `id` | Público | Fireworks + resultado + botón INICIO (controlador) / loop infinito (TV) |

**Nota:** Después de crear partido (`/new` → POST `/matches`), el backend devuelve el ID y el frontend muestra una pantalla de confirmación con la URL antes de redirigir al controlador.

## Flujos de Usuario

### Flujo 1: Crear y Controlar Partido
1. Usuario abre app → Home (`/`)
2. Toca "NUEVO PARTIDO" → Create Match (`/new`)
3. Llena formulario: modo (individual/dobles), nombres (primer nombre solo), sets (1/3/5), puntos (15/21)
4. Toca "CREAR PARTIDO" → muestra URL del partido (`/match/:id`) y URL TV (`/match/:id/tv`) con botón **COMPARTIR** → copia URL TV al clipboard
5. Redirige a Match Controller (`/match/:id`)
6. En controlador, botón **COMPARTIR** visible SOLO cuando score es **0-0** (antes del primer punto)
7. Al anotar primer punto (1-0 o 0-1), botón **COMPARTIR** desaparece permanentemente
8. Controla puntos con botones +/− (cada tap suma/resta 1 punto)
9. Toca "FINALIZAR" → confirma → **todas las vistas** redirigen a Celebration (`/match/:id/result`)
10. En Celebration, controlador muestra botón **INICIO** (crear nuevo partido). TV queda en loop de animación.

### Flujo 2: Espectador Remoto
1. Recibe URL TV (ej: `https://app.com/match/7A3F/tv`)
2. Abre URL → ve marcador en tiempo real (solo dark, sin controles)
3. Cuando partido termina → TV auto-redirige a Celebration (`/match/:id/result`)
4. TV queda en loop de animación con jugadores y marcador por set

### Flujo 3: Compartir
1. En Create Match (después de crear): muestra URL TV + botón **COMPARTIR** (copia al clipboard)
2. En Match Controller (0-0, antes de primer punto): botón **COMPARTIR** disponible (copia URL TV)
3. Después del primer punto: **NO hay** botón de compartir
4. Después de terminar partido: **NO hay** compartir resultado
5. En Celebration: **NO hay** compartir, solo botón INICIO para nuevo partido

## Reglas del Bádminton (Oficial)

> **Fuente:** badmintoncolombiano.com — Sistema de puntuación rally point (desde 2006)

### Estructura del Partido
- **Partido:** Al mejor de 3 juegos (sets) — configurable a 1 o 5
- **Juego (set):** A 21 puntos — configurable a 15
- **Ganador del partido:** Quien gane la mayoría de sets (ej: 3 sets → gana quien llegue a 2)

### Puntuación
- **Sistema rally point:** Cada rally ganado = 1 punto para el ganador del rally
- **Ganar juego:** Llegar a 21 puntos con diferencia mínima de 2
- **Deuce (20-20):** El juego continúa hasta que un lado logre 2 puntos de ventaja (ej: 22-20, 23-21, 24-22, etc.)
- **Límite máximo:** 30 puntos. Si el marcador llega a 29-29, el primero en llegar a 30 gana (30-29)
- **Intervalos:** Descanso de 1 minuto cuando un jugador/equipo llega a 11 puntos (solo una vez por juego)
- **Entre juegos:** Descanso de 2 minutos y cambio de lado
- **Tercer juego (si aplica):** Cambio de lado a los 11 puntos

### Servicio — Individual
- **Lado:** Derecho cuando el puntaje del servidor es par (0, 2, 4, 6...), izquierdo cuando es impar (1, 3, 5...)
- **Continuidad:** El mismo jugador sigue sirviendo hasta que pierde un rally
- **Cambio:** Cuando el lado receptor gana un rally, pasa a servir

### Servicio — Dobles
- **Lado:** Derecho cuando el puntaje del lado que sirve es par, izquierdo cuando es impar
- **Rotación:** Solo un jugador por pareja sirve en cada punto
- **Si pareja sirviente gana rally:** El mismo jugador sigue sirviendo pero **cambia de lado con su pareja** (uno pasa a la izquierda, el otro a la derecha)
- **Si pareja receptora gana rally:** Pasan a servir. El lado desde donde sirven lo determina su nuevo puntaje (par = derecha, impar = izquierda)
- **Receptores:** Los jugadores que reciben **mantienen su posición** en la cancha, no cambian de lado. Solo cambian de lado los jugadores que sirven cuando su pareja gana el rally.

### Ejemplo de Servicio en Dobles
- Inicio del juego: Pareja A, Jugador 1 sirve desde derecha (0 puntos = par)
- Pareja A gana rally: Jugador 1 cambia de lado con Jugador 2, Jugador 1 sigue sirviendo
- Pareja A gana otro rally: Jugador 1 cambia de lado con Jugador 2 nuevamente
- Pareja B gana rally: Pareja B pasa a servir. Su puntaje es 2 (par) → sirven desde derecha. El jugador de Pareja B que está en derecha sirve.

### Estado del Partido
- `active`: en progreso, se pueden sumar puntos
- `completed`: alguien ganó o se terminó manualmente
- `abandoned`: sin actividad >2 horas

## Data Flow

```
Frontend (React)
  │ POST /matches {player1, player2, mode, sets, points}
  ▼
Backend (PHP)
  │ INSERT INTO matches
  ▼
Database (MySQL)
  │
  ▼
Frontend polling: GET /matches?id=X every 2s
  │
  ▼
Score update: PUT /matches?id=X {player, action}
  │
  ▼
Backend: validate → update → detect winner → return state
```

## Entorno de Desarrollo (Docker)

### Contenedores

| Servicio | Puerto | Imagen |
|---|---|---|
| Frontend (dev) | `3000` | Node 18 + Vite (hot reload) |
| Backend (PHP) | `8000` | PHP 8.1 Apache |
| MySQL | `3306` | MySQL 8.0 |
| phpMyAdmin | `8080` | phpMyAdmin latest |

### Docker Compose

```yaml
# docker-compose.yml
version: '3.8'
services:
  frontend:
    build: ./frontend
    ports:
      - "3000:3000"
    volumes:
      - ./frontend:/app
    command: npm run dev

  backend:
    build: ./backend
    ports:
      - "8000:80"
    volumes:
      - ./backend:/var/www/html
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: badminton_scorer
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql
      - ./database/schema.sql:/docker-entrypoint-initdb.d/schema.sql

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8080:80"
    environment:
      PMA_HOST: db
      PMA_USER: root
      PMA_PASSWORD: rootpass

volumes:
  db_data:
```

### Comandos Docker

```bash
# Levantar todo
docker-compose up -d

# Ver logs
docker-compose logs -f backend

# Acceder a phpMyAdmin
open http://localhost:8080

# Reconstruir
docker-compose down && docker-compose up --build
```

## Aclaraciones del Usuario (CERRADAS)

| # | Pregunta | Respuesta | Impacto en Spec |
|---|---|---|---|
| 1 | **Sonido** | A) Click al punto + alerta al set + celebración al partido | Agregar sonidos en fase de implementación |
| 2 | **URL** | B) ID corto `/match/7A3F` — nombres son metadata, no van en URL | Flujos actualizados |
| 3 | **Undo en set terminado** | A) No permitir "−" en set terminado. Set anterior inmutable. | Botón − bloqueado en sets completados |
| 4 | **Nombres en dobles** | A) "Juan / Maria" — primer nombre solo, sin apellidos | UI: solo primer nombre |
| 5 | **Compartir resultado** | B) Texto formateado, PERO: solo disponible antes de iniciar partido. Después de terminar NO hay compartir | Flujo de compartir redefinido |
| 6 | **Celebración** | A) Canvas fireworks | Componente Fireworks con canvas 2D |
| 7 | **Offline** | A) Sin conexión = no funciona | Requiere conexión para todo |
| 8 | **URL generation** | Después de tocar "CREAR PARTIDO" | Muestra URL post-creación |
| 9 | **Share button timing** | Desaparece después del primer punto anotado | Botón compartir visible 0-0, oculto ≥1-0 |

## Preguntas Pendientes

**Estado: TODAS RESPONDIDAS — Ver tabla "Aclaraciones del Usuario (CERRADAS)" arriba.**

---

**SPEC LOCK** — Este documento está listo para la fase de PLANNING.
**Última actualización:** 2026-05-23
**Estado:** Aprobado por el usuario

---

**Status:** `SPEC LOCK` — Ready for Planning Phase
**Last Updated:** 2026-05-23
**Approved by:** [PENDING USER REVIEW]
