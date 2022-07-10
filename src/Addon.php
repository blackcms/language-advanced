<?php

namespace BlackCMS\LanguageAdvanced;

use BlackCMS\Addon\Abstracts\AddonOperationAbstract;
use Illuminate\Support\Facades\Schema;
use Setting;

class Addon extends AddonOperationAbstract
{
    public static function activated()
    {
        $addons = get_active_addons();

        $isAddonLanguageActivated = is_addon_active("language");

        if ($isAddonLanguageActivated &&
            ($key = array_search("language", $addons)) !== false
        ) {
            unset($addons[$key]);
        }

        if (($key = array_search("language-advanced", $addons)) !== false) {
            unset($addons[$key]);
        }

        array_unshift($addons, "language-advanced");

        if ($isAddonLanguageActivated) {
            array_unshift($addons, "language");
        }

        Setting::set("activated_addons", json_encode($addons))->save();
    }

    public static function remove()
    {
        Schema::dropIfExists("pages_translations");
    }
}
