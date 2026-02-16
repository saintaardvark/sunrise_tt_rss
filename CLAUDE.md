# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Tiny Tiny RSS plugin (`vancouver_sunrise/`) that displays Vancouver daylight information (civil twilight, sunrise, sunset, total daylight, daylight change) in the tt-rss header toolbar. Data comes from a CSV file of 2026 astronomical data from NRC Canada.

## Commands

```bash
mise run test      # Run PHP unit tests (parser + DST logic)
mise run format    # Format JS/CSS with Prettier
mise run publish   # Deploy plugin via rsync to tt-rss server
```

## Architecture

- **Backend** (`vancouver_sunrise/init.php`): PHP plugin class extending tt-rss `Plugin`. Hooks into `HOOK_MAIN_TOOLBAR_BUTTON`. Reads `sunrise.csv`, parses dates, applies DST offset, calculates daylight duration and daily change, renders toolbar HTML.
- **Frontend** (`vancouver_sunrise/init.js`): Vanilla JS IIFE. Polls backend via `xhrPost()` every 5 minutes. Only updates DOM when date changes. Exposes `window.VancouverSunrise` with `.update()` and `.forceUpdate()`.
- **Styling** (`vancouver_sunrise/init.css`): Glassmorphic toolbar component using Inter font, flexbox layout.
- **Data** (`vancouver_sunrise/sunrise.csv`): Full-year 2026 CSV from NRC Canada Sun Calculator. Key columns: Date, Civil Twilight Start, Sun Rise, Sun Set, Civil Twilight End.
- **Tests** (`vancouver_sunrise/test_parser.php`): Standalone PHP tests with mock Plugin class. Covers DST detection, time parsing, daylight calculation, and change direction.

## Code Conventions

- Prettier formatting: double quotes, trailing commas (es5), 4-space tabs, 100 char line width
- DST is hardcoded for 2026 Vancouver (March 8 – November 1)
- Plugin API version: 2
- Uses tt-rss `xhrPost()` (not `xhr.post()`) for backend calls
