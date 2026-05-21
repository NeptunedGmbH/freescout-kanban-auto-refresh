<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto Refresh
    |--------------------------------------------------------------------------
    |
    | When enabled, Kanban pages trigger a refresh at the interval below.
    | Set enabled to false to disable without deactivating the module.
    |
    */
    'enabled' => env('KANBAN_AUTO_REFRESH_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Refresh Interval (minutes)
    |--------------------------------------------------------------------------
    |
    | How often to refresh while a Kanban page is open. Minimum: 1 minute.
    |
    */
    'refresh_minutes' => (int) env('KANBAN_AUTO_REFRESH_MINUTES', 5),

    /*
    |--------------------------------------------------------------------------
    | Pause When Tab Hidden
    |--------------------------------------------------------------------------
    |
    | Skip scheduled refreshes while the browser tab is in the background.
    |
    */
    'pause_when_hidden' => env('KANBAN_AUTO_REFRESH_PAUSE_WHEN_HIDDEN', true),

];
