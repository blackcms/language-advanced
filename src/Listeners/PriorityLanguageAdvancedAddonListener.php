<?php

namespace BlackCMS\LanguageAdvanced\Listeners;

use BlackCMS\LanguageAdvanced\Addon;
use Exception;

class PriorityLanguageAdvancedAddonListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Addon::activated();
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
