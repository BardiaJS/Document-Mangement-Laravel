<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class UnauthorizedException extends Exception
{
    protected $statusCode = 401;
    protected $customData;

    public function __construct($message = "Unauthoriezed Error", $code = 401, $previous = null)
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
        Log::error('Unauthoriezed Exception: ' . $this->getMessage(), [
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString()
        ]);
    }
}
