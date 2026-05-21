# FreeScout — Kanban Auto Refresh

A custom module for [FreeScout](https://freescout.net) that automatically refreshes Kanban boards while the page is open.

The official [Kanban module](https://freescout.net/module/kanban/) requires users to click **Refresh** manually. This module triggers that refresh on a configurable timer (default: every 5 minutes).

---

## Requirements

- FreeScout (App version ≥ 1.8.117)
- [Kanban module](https://freescout.net/module/kanban/) ≥ 1.0.0

---

## Installation

### Via Docker

```bash
docker cp ./KanbanAutoRefresh freescout-app:/www/html/Modules/KanbanAutoRefresh

docker exec freescout-app php /www/html/artisan cache:clear
docker exec freescout-app php /www/html/artisan config:clear
```

### Manual

Copy this folder into your FreeScout `/Modules/` directory as `KanbanAutoRefresh`, then clear the application cache.

After installation, go to **Manage → Modules** and activate **Kanban Auto Refresh**.

---

## Configuration

Edit `Config/config.php` or set environment variables in `.env`:

| Setting | Env variable | Default | Description |
|---|---|---|---|
| `enabled` | `KANBAN_AUTO_REFRESH_ENABLED` | `true` | Master on/off switch |
| `refresh_minutes` | `KANBAN_AUTO_REFRESH_MINUTES` | `5` | Refresh interval (minimum 1) |
| `pause_when_hidden` | `KANBAN_AUTO_REFRESH_PAUSE_WHEN_HIDDEN` | `true` | Skip refreshes while the browser tab is hidden |

Example `.env` entries:

```env
KANBAN_AUTO_REFRESH_ENABLED=true
KANBAN_AUTO_REFRESH_MINUTES=3
KANBAN_AUTO_REFRESH_PAUSE_WHEN_HIDDEN=true
```

---

## How It Works

The module injects a small JavaScript snippet on every page via FreeScout's `javascript` Eventy hook. On Kanban pages (URL contains `/kanban`), it:

1. Starts a timer based on `refresh_minutes`
2. Clicks the Kanban **Refresh** button when the timer fires (or calls a known refresh handler if present)
3. Pauses while the tab is in the background (when enabled)
4. Refreshes once when you return to a hidden tab

If no refresh control is found, it falls back to a full page reload.

---

## Troubleshooting

**Refresh does not happen**

Open the Kanban page, use browser DevTools to inspect the Refresh button, and check whether its class or label differs from what the module expects. Update the selectors in `Providers/KanbanAutoRefreshServiceProvider.php` if needed.

**Board feels slow**

Increase `refresh_minutes`. Large Kanban boards can be expensive to reload frequently.

---

## License

MIT
