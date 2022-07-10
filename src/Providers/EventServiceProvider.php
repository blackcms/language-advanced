<?php

namespace BlackCMS\LanguageAdvanced\Providers;

use BlackCMS\Base\Events\CreatedContentEvent;
use BlackCMS\Base\Events\DeletedContentEvent;
use BlackCMS\LanguageAdvanced\Listeners\AddDefaultTranslations;
use BlackCMS\LanguageAdvanced\Listeners\DeletedContentListener;
use BlackCMS\LanguageAdvanced\Listeners\PriorityLanguageAdvancedAddonListener;
use BlackCMS\Addon\Events\ActivatedAddonEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        DeletedContentEvent::class => [DeletedContentListener::class],
        CreatedContentEvent::class => [AddDefaultTranslations::class],
        ActivatedAddonEvent::class => [
            PriorityLanguageAdvancedAddonListener::class,
        ],
    ];
}
