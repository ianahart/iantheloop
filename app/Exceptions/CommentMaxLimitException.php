<?php

namespace App\Exceptions;

use Exception;

class CommentMaxLimitException extends Exception
{

    public function __construct($message, $code)
    {

        parent::__construct($message, $code);
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'error' => 'Bad Request',
            'message' => $this->message,
        ], $this->code);
    }
}
