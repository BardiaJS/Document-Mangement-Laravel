<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class MethodNotAllowed extends Exception
{

    protected $statusCode = 405;
    protected $customData;

    public function __construct($message = "Method Not Allowed Error", $code = 405, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setCustomData($data)
    {
        $this->customData = $data;
        return $this;
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'code' => $this->getCode()
        ], $this->statusCode);
    }

    public function report()
    {
        // Log exception if needed
        Log::error('Method Not Allowed Exception: ' . $this->getMessage(), [
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString()
        ]);
    }
}
