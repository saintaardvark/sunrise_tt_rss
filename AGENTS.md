# AGENTS.md

Guidance for AI coding agents working in this repository.

## Project Overview

A Tiny Tiny RSS plugin (`vancouver_sunrise/`) that shows Vancouver daylight information in the tt-rss header toolbar.

Displayed values:

- Civil twilight start and end
- Sunrise and sunset
- Total daylight
- Daylight change since yesterday

Data comes from a CSV file of 2026 astronomical data from NRC Canada.

## Commands

Tasks are defined in `mise.toml`:

```bash
mise run test        # Run PHP tests (CSV parser, time handling, daylight calculation)
mise run format      # Format JS/CSS with Prettier
mise run publish     # Deploy the plugin with rsync to the tt-rss server
mise run fetch-data  # Fetch NRC Canada data and update sunrise.csv (util/fetch-sunrise-data.sh)
```

Notes:

- `publish` uses `rsync --delete`, so the remote plugin directory mirrors `vancouver_sunrise/` exactly.
- Node 20 is pinned in `mise.toml`; Prettier is run through `mise x npm:prettier`.

## Architecture

- Backend, `vancouver_sunrise/init.php`: a PHP class extending the tt-rss `Plugin`. It hooks `HOOK_MAIN_TOOLBAR_BUTTON` and registers an `update` handler. It reads `sunrise.csv`, finds today's and yesterday's rows, applies the time offset, computes daylight duration and daily change, and renders the toolbar HTML.

- Frontend, `vancouver_sunrise/init.js`: a vanilla JS IIFE. It checks every 5 minutes and calls the backend with `xhrPost()` only when the local date has changed. It then swaps the `.vancouver-sunrise-container` element. It exposes `window.VancouverSunrise` with `.update()` and `.forceUpdate()`.

- Styling, `vancouver_sunrise/init.css`: a glassmorphic toolbar component using the Inter font and flexbox.

- Data, `vancouver_sunrise/sunrise.csv`: full-year 2026 data from the NRC Canada Sun Calculator. The first line is a title and the second is the header row. Key columns: Date, Civil Twilight Start, Sun rise, Sun set, Civil Twilight End. Dates look like `Jan  1 2026` (padded single-digit days) and are whitespace-normalized before matching.

- Tests, `vancouver_sunrise/test_parser.php`: standalone PHP tests using a mock `Plugin` class. They cover time parsing, daylight calculation, and change direction.

- Utilities: `util/fetch-sunrise-data.sh` refreshes the CSV; `data/` holds related data files.

## Time Handling

- Vancouver (British Columbia) uses permanent summer time: a fixed UTC-7 offset all year, with no seasonal switch.

- The plugin applies this fixed offset to the CSV times. There is no DST logic and no date-range check.

- Do not add DST detection or date windows. If the offset ever changes, change the single fixed value.

## Code Conventions

- Plugin API version: 2.
- Use tt-rss `xhrPost()` for backend calls, not `xhr.post()`.
- Prettier formatting for JS and CSS: double quotes, trailing commas (es5), 4-space indentation, 100 character line width.
- No smart quotes in code or Markdown files.
- Markdown in this repo: no emojis, no bold or emphasis, short paragraphs.
- Tests that need a domain name must use `.test` as the top-level domain.
