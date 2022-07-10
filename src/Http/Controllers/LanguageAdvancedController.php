<?php

namespace BlackCMS\LanguageAdvanced\Http\Controllers;

use BlackCMS\Base\Events\UpdatedContentEvent;
use BlackCMS\Base\Http\Controllers\BaseController;
use BlackCMS\Base\Http\Responses\BaseHttpResponse;
use BlackCMS\LanguageAdvanced\Http\Requests\LanguageAdvancedRequest;
use BlackCMS\LanguageAdvanced\Supports\LanguageAdvancedManager;

class LanguageAdvancedController extends BaseController
{
    /**
     * @param int $id
     * @param LanguageAdvancedRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function save(
        $id,
        LanguageAdvancedRequest $request,
        BaseHttpResponse $response
    ) {
        $model = $request->input("model");

        if (!class_exists($model)) {
            abort(404);
        }

        $data = (new $model())->findOrFail($id);

        LanguageAdvancedManager::save($data, $request);

        do_action(LANGUAGE_ADVANCED_ACTION_SAVED, $data, $request);

        event(new UpdatedContentEvent("", $request, $data));

        return $response->setMessage(
            trans("core/base::notices.update_success_message")
        );
    }
}
