# Vancouver Sunrise tt-rss Plugin

A standalone plugin for [Tiny Tiny RSS](https://tt-rss.org/) that displays daily Vancouver daylight information (twilight, sunrise, sunset) in the header toolbar.

## Features
- Displays Civil Twilight Start | Sunrise | Sunset | Civil Twilight End | Total Daylight | Daylight Change.
- Automatically handles Vancouver Daylight Savings Time (+1h offset).
- Sleek CSS styling for a modern look.

## Project Structure
- `vancouver_sunrise/`: The plugin source code.
- `data/`: Source daylight data.
- `mise.toml`: Task configuration for testing and publishing.

## Data Credit
The astronomical data for Vancouver is sourced from the [National Research Council Canada - Sun Calculator](https://nrc.canada.ca/en/research-development/products-services/software-applications/sun-calculator/).

## Development
This project uses [mise](https://mise.jdx.dev/) for task automation.

```bash
# Run tests
mise run test

# Package the plugin for publishing
mise run publish
```

## Installation
Copy the `vancouver_sunrise` directory to your tt-rss `plugins.local` folder and enable it in the preferences.
