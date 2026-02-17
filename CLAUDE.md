# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WordPress plugin "Draad Adreszoeker" for denhaag.nl. Citizens of The Hague can look up their address and receive personalized energy-saving advice based on neighbourhood, building year, and heat solution type. Requires ACF Pro as a dependency.

## Build Commands

- `npm run build` — Production build (uses `wp-scripts build --webpack-copy-php`)
- `npm start` — Development mode with watch (uses `wp-scripts start --webpack-copy-php`)
- `npm run lint:js` — Lint JavaScript
- `npm run lint:css` — Lint CSS
- `npm run format` — Format code
- Node version: 22 (see `.nvmrc`)

## Architecture

### Gutenberg Blocks (src/)

Three blocks registered under namespace `draadnl/`:

- **draad-adreszoeker** — Legacy ACF-based block with server-side render (`render.php`). Uses jQuery.
- **draad-adreszoeker-formulier** — Native block: address search form. Redirects user to results page.
- **draad-adreszoeker-output** — Native block: displays advice results (tabs with energy topics).

Each block follows the standard `wp-scripts` structure: `index.js` (registration), `edit.js` (editor), `save.js` (save), `view.js` (frontend).

### PHP Classes (includes/)

- **Draad_Adreszoeker** (`class-draad-adreszoeker.php`) — Singleton main class. Registers blocks, enqueues assets, handles AJAX endpoints (`get_streets`, `get_advice`, `get_advice_react`).
- **Draad_Adreszoeker_Import** (`class-draad-adreszoeker-import.php`) — Imports address data from SQL files in `data/` into custom DB table `{prefix}_draad_az_addresses`.
- **Draad_Adreszoeker_Admin** (`class-draad-adreszoeker-admin.php`) — Admin import page under Tools menu.

### Data Model

Custom post types (registered in `post-types.php`):
- `draad_az_text` — Base advice texts (linked to neighbourhoods via `text_number` ACF field)
- `draad_az_text_2` — Detailed advice items per tab/topic
- `draad_az_area` — Neighbourhood codes (linked via `neigbourhood_code` meta)

Custom taxonomy: `draad_az_build_period` — Building period ranges (with ACF fields `start_year`/`end_year`)

Custom DB table: `{prefix}_draad_az_addresses` — Address lookup data (street, huisnummer, buurtcode, bouwjaar, energielabel)

ACF options page stored under post_id `draad_az` — Contains tab configurations (isolatie, ventilatie, opwekken, verwarmen, koken, subsidies) with repeaters keyed by heat solution and building period.

### AJAX Flow

1. `get_streets` — Autocomplete: searches streets by partial match in DB table
2. `get_advice` — Legacy: returns rendered HTML from `templates/grid-container.php`
3. `get_advice_react` — React blocks: returns JSON with neighbourhood data, tabs with advice items, tiles, and base content

### Frontend Design System

Uses Den Haag design system (`@gemeente-denhaag/*`) and Utrecht component libraries (`@utrecht/*`).

## Code Style

- WordPress coding standards (tabs for indentation)
- Text domain: `draad-adreszoeker`
- All user-facing strings are in Dutch, wrapped in `__()` / `esc_html__()`
