<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Story;
use Exception;

class StoryController extends Controller
{
    /**
     * It creates a story that the current user is submitting
     * @return JsonResponse
     */
    public function store()
    {
        try {
            return response()
                ->json(
                    [
                        'msg' => 'success',
                        'data' => []
                    ],
                    201
                );
        } catch (Exception $e) {
            return response()
                ->json(
                    [
                        'msg' => 'Unable to create story',
                        'error' => $e->getMessage()
                    ],
                    400
                );
        }
    }
}
