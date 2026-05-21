<?php

namespace Modules\KanbanAutoRefresh\Providers;

use Illuminate\Support\ServiceProvider;

define('KANBAN_AUTO_REFRESH_MODULE', 'kanbanautorefresh');

class KanbanAutoRefreshServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'kanbanautorefresh');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'kanbanautorefresh');
        $this->registerJavascript();
    }

    public function register(): void {}

    protected function registerJavascript(): void
    {
        if (!config('kanbanautorefresh.enabled', true)) {
            return;
        }

        $refreshMinutes = max(1, (int) config('kanbanautorefresh.refresh_minutes', 5));
        $pauseWhenHidden = (bool) config('kanbanautorefresh.pause_when_hidden', true);

        \Eventy::addAction('javascript', function () use ($refreshMinutes, $pauseWhenHidden) {
            $intervalMs = $refreshMinutes * 60 * 1000;
            $pauseWhenHiddenJs = $pauseWhenHidden ? 'true' : 'false';

            echo <<<JS
(function ($) {
    'use strict';

    if (window.location.pathname.indexOf('/kanban') === -1) {
        return;
    }

    var intervalMs = {$intervalMs};
    var pauseWhenHidden = {$pauseWhenHiddenJs};
    var refreshTimer = null;

    function triggerKanbanRefresh() {
        var \$refreshControl = $(
            '.kanban-refresh, .kanban-refresh-btn, [data-kanban-action="refresh"], ' +
            'a.kanban-refresh, button.kanban-refresh'
        ).first();

        if (!\$refreshControl.length) {
            \$refreshControl = $('a, button').filter(function () {
                var text = $(this).text().replace(/\\s+/g, ' ').trim().toLowerCase();
                return text === 'refresh' || text.indexOf('refresh') === 0;
            }).first();
        }

        if (!\$refreshControl.length) {
            \$refreshControl = $('[title="Refresh"], [aria-label="Refresh"]').first();
        }

        if (\$refreshControl.length) {
            \$refreshControl.trigger('click');
            return;
        }

        if (typeof window.kanbanReload === 'function') {
            window.kanbanReload();
            return;
        }

        if (typeof window.kanbanRefresh === 'function') {
            window.kanbanRefresh();
            return;
        }

        window.location.reload();
    }

    function scheduleRefresh() {
        if (refreshTimer) {
            clearInterval(refreshTimer);
        }

        refreshTimer = setInterval(function () {
            if (pauseWhenHidden && document.hidden) {
                return;
            }

            triggerKanbanRefresh();
        }, intervalMs);
    }

    scheduleRefresh();

    if (pauseWhenHidden) {
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                triggerKanbanRefresh();
            }
        });
    }
})(jQuery);
JS;
        });
    }
}
