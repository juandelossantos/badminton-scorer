# Badminton Scorer

Marcador de bádminton en tiempo real con URL compartible. Estilo hacker/terminal, bilingüe (ES/EN).

## Estructura del Proyecto

```
badminton-scorer/
├── frontend/          # React SPA (Vite)
│   ├── src/
│   │   ├── components/
│   │   │   ├── common/           # Header, SetsBar, PlayersRow, AppFooter
│   │   │   ├── create-match/     # Formulario nuevo partido
│   │   │   ├── match-controller/ # Scoreboard, Controls (+/-, Finalizar)
│   │   │   ├── match-tv/         # TVScoreboard, TVSetsBar, etc.
│   │   │   └── celebration/      # Fireworks, resultados finales
│   │   ├── pages/
│   │   │   ├── HomePage.jsx
│   │   │   ├── CreateMatchPage.jsx
│   │   │   ├── MatchPage.jsx
│   │   │   ├── MatchTVPage.jsx
│   │   │   └── CelebrationPage.jsx
│   │   ├── context/
│   │   │   └── ThemeContext.jsx
│   │   ├── api/
│   │   │   └── matches.js
│   │   └── styles/
│   │       └── global.css
│   ├── package.json
│   ├── vite.config.js
│   └── index.html
├── backend/           # PHP REST API
│   ├── api/
│   │   └── matches/
│   │       ├── create.php
│   │       ├── read.php
│   │       ├── undo.php
│   │       └── end.php
│   ├── config/
│   │   └── database.php
│   ├── models/
│   │   └── Match.php
│   └── .htaccess
├── database/
│   └── schema.sql
├── DESIGN.md          # Sistema de diseño completo
└── README.md
```

## Pantallas / Rutas

| Pantalla | Ruta | Descripción |
|---|---|---|
| Home | `/` | Landing, botón "Nuevo Partido" |
| Crear Partido | `/new` | Formulario: modo, jugadores, sets, puntos |
| Controlador | `/match/:id` | Marcador con controles (+/-, Finalizar) |
| TV Espectador | `/match/:id/tv` | Solo lectura, números grandes, solo dark |
| Celebración | `/match/:id/result` | Fireworks + ganadores + resultados |

## Stack Tecnológico

- **Frontend**: React 18 + React Router DOM + Vite
- **Backend**: PHP 7.4+ + PDO + MySQL 5.7+
- **Sync**: Polling HTTP cada 2-3 segundos
- **Hosting**: Servidor propio con PHP/MySQL

## Instalación

### Frontend

```bash
cd frontend
npm install
npm run dev        # Desarrollo en localhost:3000
npm run build      # Producción en dist/
```

### Backend

1. Copiar `backend/` al servidor web
2. Importar `database/schema.sql` en MySQL
3. Configurar variables de entorno o editar `backend/config/database.php`
4. Asegurar que `.htaccess` esté activo

### Variables de Entorno

Crear `.env` en la raíz del proyecto:

```
DB_HOST=localhost
DB_NAME=badminton_scorer
DB_USER=root
DB_PASS=your_password
```

## API Endpoints

| Método | Endpoint | Descripción |
|---|---|---|
| POST | `/api/matches` | Crear partido |
| GET | `/api/matches?id={id}` | Obtener partido |
| PUT | `/api/matches?id={id}` | Actualizar puntaje |
| PUT | `/api/matches/undo?id={id}` | Deshacer |
| PUT | `/api/matches/end?id={id}` | Finalizar partido |
| DELETE | `/api/matches?id={id}` | Eliminar partido |

## Características

- ✅ Individual y Dobles
- ✅ Marcador en tiempo real
- ✅ Sincronización vía polling
- ✅ URL compartible por partido
- ✅ Vista TV para espectadores (solo dark, números grandes)
- ✅ Cambio de servicio automático
- ✅ Detección de ganador (21 pts, diferencia 2)
- ✅ Botón "Finalizar" + pantalla de celebración con fireworks
- ✅ Dark/Light toggle
- ✅ Bilingüe: Español/Inglés
- ✅ Responsive: Portrait, Landscape, TV

## Reglas Implementadas

- Puntos al 21 (o configurado al crear)
- Diferencia mínima de 2 puntos
- Límite máximo: 30 puntos
- Sets al mejor de 3, 5, etc.
- Cambio de servicio cada 2 puntos
- Cambio de lado cada set

## Licencia

MIT
