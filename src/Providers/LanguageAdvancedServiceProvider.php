<?php

namespace BlackCMS\LanguageAdvanced\Providers;

use BlackCMS\Base\Models\BaseModel;
use BlackCMS\Base\Traits\LoadAndPublishDataTrait;
use BlackCMS\LanguageAdvanced\Supports\LanguageAdvancedManager;
use BlackCMS\Page\Models\Page;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Language;
use MacroableModels;

class LanguageAdvancedServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot()
    {
        if (is_addon_active("language")) {
            $this->setNamespace("addons/language-advanced")
                ->loadHelpers()
                ->loadMigrations()
                ->loadAndPublishConfigurations(["general"])
                ->loadAndPublishViews()
                ->loadRoutes(["web"]);

            $this->app->register(HookServiceProvider::class);
            $this->app->register(EventServiceProvider::class);

            $this->app->booted(function () {
                foreach (LanguageAdvancedManager::getSupported() as $item => $columns) {
                    if (!class_exists($item)) {
                        continue;
                    }

                    /**
                     * @var BaseModel $item
                     */
                    $item::resolveRelationUsing("translations", function (
                        $model
                    ) {
                        return $model->hasMany(
                            LanguageAdvancedManager::getTranslationModel(
                                $model
                            ),
                            $model->getTable() . "_id"
                        );
                    });

                    foreach ($columns as $column) {
                        MacroableModels::addMacro(
                            $item,
                            "get" . Str::title($column) . "Attribute",
                            function () use ($column) {
                                /**
                                 * @var BaseModel $this
                                 */

                                $locale = is_in_admin()
                                    ? Language::getCurrentAdminLocaleCode()
                                    : Language::getCurrentLocaleCode();
                                if (!$this->lang_code &&
                                    $locale != Language::getDefaultLocaleCode()
                                ) {
                                    $translation = $this->translations
                                        ->where("lang_code", $locale)
                                        ->first();

                                    if ($translation) {
                                        return $translation->{$column};
                                    }
                                }

                                return $this->getAttribute($column);
                            }
                        );
                    }
                }
            });

            $config = $this->app["config"];

            if ($config->get(
                "addons.language-advanced.general.page_use_language_v2",
                false
            )
            ) {
                LanguageAdvancedManager::registerModule(Page::class, [
                    "name",
                    "description",
                    "content",
                ]);

                $supportedModels = Language::supportedModels();

                if (($key = array_search(Page::class, $supportedModels)) !==
                    false
                ) {
                    unset($supportedModels[$key]);
                }

                $config->set([
                    "addons.language.general.supported" => $supportedModels,
                ]);
            }
        }
    }
}
