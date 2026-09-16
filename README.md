# CultureGraph

**Atlas personal de cultura disfrutada.**

Registrá películas, series, discos y videojuegos, y relacionálos por autores, géneros, épocas, temas y experiencias. Misma línea que BookNest / LifeQuest / RepoScope: PHP + MySQL, sin frameworks, desplegable copiando una carpeta.

```text
╔══════════════════════════════════════════╗
║  CULTUREGRAPH                  v0.1      ║
║  ATLAS DE SEÑALES                       ║
╚══════════════════════════════════════════╝
```

---

## Qué es

Cada registro es una **obra disfrutada** (o pendiente). El valor está en el **grafo**: las conexiones entre obras y ejes (autores, géneros, épocas, temas, experiencias).

### Incluye (v0.1)

- Panel con métricas, recientes y vista previa del grafo
- Catálogo de obras (película / serie / disco / juego) con filtros
- Autores / creadores con roles
- Géneros, épocas, temas y experiencias (CRUD + obras vinculadas)
- Grafo interactivo (canvas, force layout)
- Estadísticas por tipo, estado, década y tops
- Búsqueda global
- Configuración del archivo
- Instalador one-shot + taxonomías iniciales

---

## Stack

| Capa | Tecnología |
|------|------------|
| Backend | PHP 8.1+ |
| Base | MySQL 8 / MariaDB |
| Front | HTML, CSS, JavaScript (sin frameworks) |

Sin Composer. Sin Node para correr la app.

---

## Instalación

1. Copiá el proyecto a `W:\culturegraph` (o servilo desde el repo).
2. Copiá `.env.example` → `.env` y ajustá la base:

```env
APP_NAME=CultureGraph
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=culturegraph
DB_USER=root
DB_PASS=
```

3. Abrí `install.php` en el navegador (crea la base y aplica `sql/schema.sql`).
4. Entrá a `index.php`.

Rutas por query string: `index.php?r=/obras`, `index.php?r=/grafo`, etc.

---

## Navegación

```text
Panel · Obras · Autores · Géneros · Épocas
Temas · Experiencias · Grafo · Estadísticas · Configuración
```

---

## Design system — Atlas de Señales

| Token | Hex | Uso |
|------:|-----|-----|
| Papel | `#E6EDF2` | Fondo |
| Superficie | `#F5F8FA` | Paneles |
| Tinta | `#152533` | Texto / bordes |
| Teal | `#1F6F78` | Estructura / géneros |
| Ámbar | `#C9922A` | Señal / autores / CTA |
| Coral | `#B85A45` | Experiencias |
| Musgo | `#4F6F5C` | Épocas / disfrutado |

- Marca / títulos: **Syne**
- UI: **IBM Plex Mono**
- Sinopsis: **Source Serif 4**
- Bordes 2px, sombras offset (`5px 5px 0`), radio mínimo

---

## Modelo (resumen)

- `works` — obra (tipo, año, rating, estado, metadata por tipo)
- `creators` + `work_creators` — autores y roles
- `genres` / `eras` / `themes` / `experiences` + tablas puente
- Conexiones cercanas: overlap de géneros, temas, experiencias y autores

---

## Principios

1. Una ficha = una obra disfrutada (o en camino)
2. Las relaciones son el producto, no un extra
3. Sin login: uso personal / Home Lab
4. Taxonomías editables; el seed inicial es punto de partida
5. Los datos locales son la fuente de verdad

---

## Estructura

```text
CultureGraph/
├── app/            Controllers, Services, Views
├── assets/         CSS, JS, iconos, patrón
├── config/         .env loader, app, database
├── sql/schema.sql  Único esquema versionado
├── storage/covers/ Portadas subidas
├── index.php
├── install.php
└── README.md
```

---

## Licencia / uso

Proyecto personal. Hecho para un solo atlas en casa.
