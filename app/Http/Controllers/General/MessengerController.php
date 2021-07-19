<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Messenger;
use Exception;


class MessengerController extends Controller
{
    /*It retrieves users that are following the current user and
    * those users are followed by the current user
    * @param Request $request
    * @param string $userId
    * @return JsonResponse
    */
    #Paginate later on if users grow
    #Switch to show only users that are online

    public function show(Request $request, string $userId)
    {
        try {
            $messenger = new Messenger($userId);

            $messenger->aggregateContacts();

            $exception = $messenger->getError();

            if (!is_null($exception)) {
                throw new Exception($exception);
            }

            return response()
                ->json(
                    [
                        'msg' => 'Contacts retrieved',
                        'contacts' => $messenger->getContacts(),
                        'contacts_count' => $messenger->getContacts()->count(),
                    ]
                );
        } catch (Exception $e) {

            return response()
                ->json(
                    [
                        'msg' => 'No contacts available',
                        'errors' => $e->getMessage(),
                    ],
                    404
                );
        }
    }
}
