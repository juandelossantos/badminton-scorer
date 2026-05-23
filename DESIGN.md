---
name: Badminton Scorer
description: Marcador de bádminton en tiempo real con estilo hacker/terminal. Diseñado para árbitros en cancha y espectadores remotos. Soporta modos individual y dobles, sincronización en tiempo real, y vistas adaptativas (portrait, landscape, TV).
version: alpha

colors:
  # Dark Theme (default)
  bg-dark: "#0c0c0c"
  surface-dark: "#1a1a1a"
  elevated-dark: "#1e293b"
  text-primary-dark: "#e2e8f0"
  text-secondary-dark: "#94a3b8"
  text-muted-dark: "#475569"
  border-subtle-dark: "rgba(255,255,255,0.06)"
  border-default-dark: "rgba(255,255,255,0.08)"
  
  # Light Theme
  bg-light: "#ffffff"
  surface-light: "#f3f4f6"
  elevated-light: "#e5e7eb"
  text-primary-light: "#1f2937"
  text-secondary-light: "#6b7280"
  text-muted-light: "#9ca3af"
  border-subtle-light: "rgba(0,0,0,0.06)"
  border-default-light: "rgba(0,0,0,0.08)"
  
  # Accent Colors (consistent across themes via semantic mapping)
  accent-p1: "#10b981"
  accent-p1-light: "#059669"
  accent-p2: "#f59e0b"
  accent-p2-light: "#d97706"
  accent-live: "#10b981"
  accent-live-light: "#059669"
  accent-danger: "#ef4444"
  accent-danger-light: "#dc2626"
  
  # Glow Effects (dark only)
  glow-p1: "rgba(16,185,129,0.25)"
  glow-p2: "rgba(245,158,11,0.25)"
  glow-p1-tv: "rgba(16,185,129,0.4)"
  glow-p2-tv: "rgba(245,158,11,0.4)"
  
  # TV-Only Highlights
  tv-set-highlight-bg: "rgba(16,185,129,0.15)"
  tv-set-highlight-border: "rgba(16,185,129,0.3)"
  tv-set-highlight-text: "#10b981"

typography:
  display-tv:
    fontFamily: "JetBrains Mono"
    fontSize: "7rem"
    fontWeight: 800
    lineHeight: "1.0"
    letterSpacing: "-4px"
  
  display-portrait:
    fontFamily: "JetBrains Mono"
    fontSize: "5.5rem"
    fontWeight: 800
    lineHeight: "1.0"
    letterSpacing: "-4px"
  
  display-landscape:
    fontFamily: "JetBrains Mono"
    fontSize: "4rem"
    fontWeight: 800
    lineHeight: "1.0"
    letterSpacing: "-3px"
  
  h1:
    fontFamily: "JetBrains Mono"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: "1.2"
    letterSpacing: "3px"
  
  h2-tv-names:
    fontFamily: "JetBrains Mono"
    fontSize: "0.8rem"
    fontWeight: 700
    lineHeight: "1.2"
    letterSpacing: "1px"
  
  body:
    fontFamily: "JetBrains Mono"
    fontSize: "0.65rem"
    fontWeight: 700
    lineHeight: "1.3"
    letterSpacing: "0.5px"
  
  label:
    fontFamily: "JetBrains Mono"
    fontSize: "0.55rem"
    fontWeight: 700
    lineHeight: "1.2"
    letterSpacing: "1px"
  
  caption:
    fontFamily: "JetBrains Mono"
    fontSize: "0.5rem"
    fontWeight: 600
    lineHeight: "1.2"
    letterSpacing: "2px"

rounded:
  sm: "4px"
  md: "6px"
  lg: "24px"
  xl: "32px"
  tv: "8px"

spacing:
  xs: "4px"
  sm: "6px"
  md: "8px"
  lg: "12px"
  xl: "16px"
  xxl: "24px"
  tv: "40px"
  tv-lg: "60px"

components:
  # Header
  header-live-dot:
    size: "5px"
    backgroundColor: "{colors.accent-live}"
    borderRadius: "50%"
  
  header-live-text:
    typography: "{typography.label}"
    color: "{colors.accent-live}"
  
  header-mode:
    typography: "{typography.label}"
    color: "{colors.text-muted-dark}"
  
  # Sets Bar
  sets-bar:
    padding: "6px 12px"
    backgroundColor: "{colors.surface-dark}"
    borderBottom: "1px solid {colors.border-subtle-dark}"
  
  sets-label:
    typography: "{typography.caption}"
    color: "{colors.text-muted-dark}"
    textTransform: "uppercase"
  
  sets-num:
    typography: "{typography.body}"
    color: "{colors.text-primary-dark}"
  
  sets-num-won:
    color: "{colors.accent-p1}"
  
  sets-divider:
    typography: "{typography.caption}"
    color: "{colors.text-muted-dark}"
  
  current-set-badge:
    typography: "{typography.caption}"
    color: "{colors.text-secondary-dark}"
    backgroundColor: "{colors.border-subtle-dark}"
    borderRadius: "{rounded.sm}"
    padding: "2px 6px"
  
  # TV Sets Highlight (unique to TV view)
  tv-current-set-highlight:
    typography:
      fontFamily: "JetBrains Mono"
      fontSize: "0.7rem"
      fontWeight: 700
      letterSpacing: "1px"
    color: "{colors.tv-set-highlight-text}"
    backgroundColor: "{colors.tv-set-highlight-bg}"
    border: "1px solid {colors.tv-set-highlight-border}"
    borderRadius: "{rounded.sm}"
    padding: "3px 10px"
  
  # Players Row
  player-names:
    typography: "{typography.body}"
    color: "{colors.text-primary-dark}"
  
  player-status:
    typography: "{typography.caption}"
    color: "{colors.text-muted-dark}"
  
  vs-mini:
    typography: "{typography.caption}"
    color: "{colors.text-muted-dark}"
    letterSpacing: "2px"
  
  # Scoreboard
  big-number-p1:
    typography: "{typography.display-portrait}"
    color: "{colors.accent-p1}"
    textShadow: "0 0 40px {colors.glow-p1}"
  
  big-number-p2:
    typography: "{typography.display-portrait}"
    color: "{colors.accent-p2}"
    textShadow: "0 0 40px {colors.glow-p2}"
  
  points-label:
    typography: "{typography.caption}"
    color: "{colors.text-muted-dark}"
    textTransform: "uppercase"
  
  # TV Score Overrides
  tv-big-number-p1:
    typography: "{typography.display-tv}"
    color: "{colors.accent-p1}"
    textShadow: "0 0 50px {colors.glow-p1-tv}"
  
  tv-big-number-p2:
    typography: "{typography.display-tv}"
    color: "{colors.accent-p2}"
    textShadow: "0 0 50px {colors.glow-p2-tv}"
  
  # Control Buttons
  btn-plus-p1:
    backgroundColor: "#059669"
    color: "#ffffff"
    borderRadius: "{rounded.md}"
    size: "44px × 36px"
    fontWeight: 700
  
  btn-plus-p2:
    backgroundColor: "#d97706"
    color: "#ffffff"
    borderRadius: "{rounded.md}"
    size: "44px × 36px"
    fontWeight: 700
  
  btn-minus:
    backgroundColor: "{colors.elevated-dark}"
    color: "{colors.text-secondary-dark}"
    border: "1px solid {colors.border-default-dark}"
    borderRadius: "{rounded.md}"
    size: "44px × 36px"
    fontWeight: 700
  
  btn-end-match:
    backgroundColor: "transparent"
    color: "{colors.text-muted-dark}"
    border: "1px solid {colors.border-default-dark}"
    borderRadius: "{rounded.sm}"
    typography: "{typography.caption}"
    padding: "4px 8px"
  
  btn-end-match-hover:
    color: "{colors.accent-danger}"
    borderColor: "{colors.accent-danger}"
  
  # App Footer
  app-footer:
    borderTop: "1px solid {colors.border-subtle-dark}"
    padding: "8px 12px"
  
  footer-active:
    color: "{colors.accent-live}"
  
  footer-inactive:
    color: "{colors.text-muted-dark}"
  
  # TV Names Row
  tv-team-names:
    typography: "{typography.h2-tv-names}"
    color: "{colors.text-primary-dark}"
  
  tv-team-info:
    typography: "{typography.caption}"
    color: "{colors.text-muted-dark}"
  
  # TV Footer
  tv-footer:
    borderTop: "1px solid {colors.border-subtle-dark}"
    padding: "8px 16px"
    typography: "{typography.caption}"
    color: "{colors.text-muted-dark}"
  
  # Inputs (Create Match)
  input:
    backgroundColor: "{colors.surface-dark}"
    border: "1px solid {colors.border-default-dark}"
    borderRadius: "{rounded.md}"
    padding: "10px 12px"
    typography:
      fontFamily: "JetBrains Mono"
      fontSize: "0.7rem"
      fontWeight: 600
    color: "{colors.text-primary-dark}"
  
  input-focus:
    borderColor: "{colors.accent-p1}"
    boxShadow: "0 0 0 2px rgba(16,185,129,0.1)"
  
  input-placeholder:
    color: "{colors.text-muted-dark}"
  
  # Primary Button (CTA)
  button-primary:
    backgroundColor: "{colors.accent-p1}"
    color: "#ffffff"
    borderRadius: "{rounded.md}"
    padding: "12px"
    typography:
      fontFamily: "JetBrains Mono"
      fontSize: "0.7rem"
      fontWeight: 700
      letterSpacing: "1px"
  
  # Cancha Background
  cancha-bg:
    opacity: "0.12"
    color: "#6b7280"
    pointerEvents: "none"
    position: "absolute"
  
  cancha-bg-light:
    opacity: "0.08"
    color: "#9ca3af"
---

## Overview

Badminton Scorer es una aplicación web de marcador de bádminton en tiempo real con URL compartible, diseñada para árbitros en cancha y espectadores remotos. El estilo visual es **hacker/terminal** con influencias de dashboards deportivos (FlashScore) y aplicaciones de bádminton (Goodminton, CourtCQ).

### Filosofía Visual

**"Poder sin distracción."** La interfaz muestra la información esencial — puntos, sets, nombres — con tipografía monoespaciada (JetBrains Mono) que evoca terminales y sistemas de control. El fondo oscuro absorbe la luz como una pantalla de radar. Los números de puntaje brillan con un glow sutil que solo aparece en el tema dark, dando un toque dramático sin ser recargado.

### Temas

- **Dark** (default): fondo `#0c0c0c`, números con glow sutil, grid sutil de fondo
- **Light** (alternativo): fondo `#ffffff`, cancha en gris claro, sin glow, colores más oscuros para contraste

### Vistas

1. **Portrait** — Móvil vertical, controlador en cancha
2. **Landscape** — Móvil volteado horizontal, controlador en cancha
3. **TV Espectador** — Pantalla ancha para espectadores a distancia. **Solo Dark.** Números grandes y set en juego destacado.

## Colors

### Paleta Principal (Dark)

- **Background (#0c0c0c)**: Casi negro absoluto. Es el fondo principal que absorbe la luz y hace que los números de puntaje resalten como en un radar.
- **Surface (#1a1a1a)**: Un tono más claro para separadores, barras de sets, y superficies elevadas.
- **Text Primary (#e2e8f0)**: Gris muy claro para nombres de jugadores y números principales.
- **Text Secondary (#94a3b8)**: Gris medio para labels y metadatos.
- **Text Muted (#475569)**: Gris oscuro para elementos inactivos y bordes.

### Paleta Principal (Light)

- **Background (#ffffff)**: Blanco puro. No es un simple inverso del dark — es un tema real con sus propios matices.
- **Surface (#f3f4f6)**: Gris muy claro para barras de sets y superficies.
- **Text Primary (#1f2937)**: Gris muy oscuro, casi negro, para textos principales.
- **Text Secondary (#6b7280)**: Gris medio para labels.
- **Text Muted (#9ca3af)**: Gris claro para elementos inactivos.

### Colores de Acento

- **Verde (#10b981 / #059669)**: Jugador/Equipo 1. En dark brilla con glow. En light es más oscuro para contraste.
- **Ámbar (#f59e0b / #d97706)**: Jugador/Equipo 2. Color cálido que contrasta bien con el verde.
- **Rojo (#ef4444 / #dc2626)**: Peligro, undo, server dot. Usado con moderación.

### Uso de Glow (Dark Only)

El glow solo existe en el tema dark y solo en los marcadores principales:
- P1: `text-shadow: 0 0 40px rgba(16,185,129,0.25)`
- P2: `text-shadow: 0 0 40px rgba(245,158,11,0.25)`

En TV el glow es más intenso (0.4 en lugar de 0.25) para visibilidad a distancia.

## Typography

### Fuente Base

**JetBrains Mono** — monospace con características técnicas, legible en tamaños pequeños, ideal para dashboards y sistemas de control. Cargada vía Google Fonts con pesos 300, 400, 600, 700, 800.

### Escala Tipográfica

| Token | Tamaño | Peso | Uso |
|---|---|---|---|
| Display TV | 7rem | 800 | Marcador TV (visibilidad a distancia) |
| Display Portrait | 5.5rem | 800 | Marcador portrait (móvil vertical) |
| Display Landscape | 4rem | 800 | Marcador landscape (móvil horizontal) |
| H1 | 1rem | 400 | Título de página, tracking 3px |
| H2 TV Names | 0.8rem | 700 | Nombres de equipo en TV |
| Body | 0.65rem | 700 | Nombres de jugadores, tracking 0.5px |
| Label | 0.55rem | 700 | Labels, indicadores, tracking 1px |
| Caption | 0.5rem | 600 | PTS, meta info, tracking 2px |

### Principios Tipográficos

- Todo el sistema usa JetBrains Mono. Sin excepciones.
- Los números de marcador usan letter-spacing negativo (-3px a -4px) para que los dígitos dobles (21, 18) se sientan compactos y potentes.
- Labels y captions usan tracking positivo (1px-2px) para legibilidad en tamaños pequeños.
- Los textos uppercase solo se usan en labels, never en nombres de jugadores.

## Layout

### Dimensiones de Vista

| Vista | Ancho | Alto | Padding Frame | Radio Pantalla |
|---|---|---|---|---|
| Portrait | 375px | 680px | 8px | 24px |
| Landscape | 480px | 260px | 8px | 24px |
| TV | 640px | 340px | 12px | 4px |

### Estructura de Layout (Todas las Vistas)

```
┌─────────────────────────┐
│ Header (EN VIVO | Mode) │
├─────────────────────────┤
│ Sets Bar                │
├─────────────────────────┤
│ Players Row             │
├─────────────────────────┤
│ Scoreboard Area         │
│   [Cancha BG]           │
│   [Score P1] [Score P2] │
├─────────────────────────┤
│ Controls (solo control)  │
├─────────────────────────┤
│ Footer / Names Row      │
└─────────────────────────┘
```

### Portrait Específico

- Marcadores lado a lado horizontalmente (flex row, gap 32px)
- Números: 5.5rem con glow
- Controles abajo divididos por mitad (izquierda P1, derecha P2)
- Footer con tabs: MARCADOR | DETALLES | 🌙

### Landscape Específico

- Marcadores lado a lado horizontalmente (flex row, gap 40px)
- Números: 4rem con glow
- Players row arriba (nombres a los lados, VS centro)
- Sets bar entre header y players (igual que portrait)
- Controles abajo divididos por mitad

### TV Específico (Solo Dark)

- Marcadores lado a lado horizontalmente (flex row, gap 60px)
- Números: 7rem con glow intenso (visibilidad a distancia)
- Set destacado: badge verde con fondo semi-transparente
- Indicador LIVE: punto verde parpadeante + texto
- Sin controles (solo visualización)
- Footer con info: SET X DE Y | MARCADOR: XX-XX | PUNTOS AL 21

### Responsive

- **< 375px**: Reducir números 10%, padding mínimo
- **375-479px**: Portrait layout
- **480-639px**: Landscape layout
- **640px+**: TV layout para espectadores
- **> 1024px**: TV layout centrado, max-width 960px

## Elevation & Depth

### Cancha Background

La cancha de bádminton como background sutil:
- **Dark**: opacity 12%, color `#6b7280`
- **Light**: opacity 8%, color `#9ca3af`
- Posición: absolute, centrada, pointer-events: none
- SVG inline con viewBox proporcional

### Glow de Marcadores

Los números principales tienen text-shadow con blur 40px (portrait) o 50px (TV). Este glow es el único efecto de "profundidad" en la interfaz. No hay sombras de caja (box-shadow) en cards ni botones.

### Superficies

- **Dark**: bg `#0c0c0c` → surface `#1a1a1a` → elevated `#1e293b`
- **Light**: bg `#ffffff` → surface `#f3f4f6` → elevated `#e5e7eb`

## Shapes

### Bordes Redondeados

| Token | Valor | Uso |
|---|---|---|
| sm | 4px | Badges, inputs, small elements |
| md | 6px | Botones, cards |
| lg | 24px | Phone screen inner |
| xl | 32px | Phone frame outer |
| tv | 8px | TV frame |

### Formas de Componentes

- **Botones**: rectángulos con bordes redondeados (6px), sin sombras
- **Badges**: rectángulos con bordes ligeramente redondeados (4px)
- **Inputs**: rectángulos con bordes redondeados (6px), borde sólido 1px
- **Phone frames**: bordes muy redondeados (32px) para simular dispositivo
- **TV frames**: bordes casi cuadrados (8px) para pantalla profesional

## Components

### Header

Barra superior con indicador EN VIVO y modo de juego.
- Dot verde 5px parpadeante (animation: blink 2s)
- Texto "EN VIVO" en verde, 0.55rem, weight 700
- Modo alineado a la derecha: "DOBLES" o "INDIVIDUAL"
- Border-bottom sutil separa del contenido

### Sets Bar

Barra centrada mostrando sets ganados y set actual.
- "SETS" label uppercase, muted
- Score: números separados por "—" (em-dash)
- Set ganado resaltado en verde
- Badge "SET X/Y" con fondo sutil

**TV Variant**: El badge del set actual tiene fondo verde semi-transparente (`rgba(16,185,129,0.15)`) con borde verde para destacar a distancia.

### Players Row

Nombres de jugadores/equipos con indicador de servicio.
- Nombres: 0.65rem, weight 700, text-primary
- Status: 0.5rem, muted — "SERVICIO → DERECHO" o "RECIBE"
- "VS" centrado: 0.45rem, tracking 2px, muted
- Server indicado por dot rojo (5px) en la columna de score

### Scoreboard Area

Área principal con cancha de fondo y marcadores.
- Cancha SVG: opacity 12% (dark) / 8% (light), centrada
- Marcadores: flex row, centrados, z-index sobre cancha
- Cada marcador: número grande + label "PTS"

### Control Buttons

Botones de +/- para cada jugador.
- **+ P1**: fondo verde `#059669`, texto blanco
- **+ P2**: fondo ámbar `#d97706`, texto blanco
- **−**: fondo `#1e293b` (dark) / `#f3f4f6` (light), texto muted
- Tamaño: 44px × 36px, radio 6px
- Gap entre botones: 6px, gap entre mitades: 8px

**End Match Button**: botón pequeño, transparente, borde sutil, texto muted. En hover: rojo.

### Create Match Form

Formulario simple para iniciar partido.
- Inputs: fondo surface, borde 1px default, radio 6px
- Select/dropdown: mismo estilo que inputs
- Focus: borde verde + glow sutil
- Botón submit: full-width, verde, blanco, 12px padding

### Celebration Screen

Pantalla de fin de partido con animación de fireworks.
- Fondo: siempre `#0c0c0c` (independiente del tema)
- Fireworks: canvas 2D con partículas verdes y ámbar
- Trophy icon (2rem) + nombres ganadores (1.2rem, color ganador)
- Resultado final: 3rem, weight 800
- Sets detallados: 0.7rem, secondary
- Botones: "NUEVO PARTIDO" (primary) + "COMPARTIR" (secondary)
- **Reduced motion**: resultado estático sin animación

### App Footer

Barra inferior con navegación y toggle de tema.
- Tabs: MARCADOR | DETALLES | icono tema
- Tab activo: color verde
- Tab inactivo: color muted
- Icono sol/luna: 14px, toggles dark/light

## Do's and Don'ts

### Do

- ✅ Usar JetBrains Mono para todo el sistema
- ✅ Mantener números de marcador grandes y con glow (dark)
- ✅ Usar verde vs ámbar para diferenciar jugadores
- ✅ Mantener cancha como background sutil, nunca dominante
- ✅ Mostrar información de sets consistente en todas las vistas
- ✅ Hacer TV solo dark con números más grandes
- ✅ Respetar `prefers-reduced-motion` para fireworks
- ✅ Usar gris oscuro (`#475569`) para texto secundario en dark

### Don't

- ❌ Usar fuentes que no sean JetBrains Mono
- ❌ Añadir sombras de caja (box-shadow) a cards o botones
- ❌ Cambiar colores de acento (verde/ámbar) por otros
- ❌ Hacer glow en tema light (no existe glow en light)
- ❌ Poner controles en la vista TV (es solo lectura)
- ❌ Usar texto uppercase en nombres de jugadores
- ❌ Hacer la cancha más opaca de 12% (dark) / 8% (light)
- ❌ Usar bordes redondeados mayores a 6px en botones
