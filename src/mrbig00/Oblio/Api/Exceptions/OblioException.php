<?php
/**
 * @copyright   Copyright (c) 2019 Zoltan Szanto
 * @author      Zoltan Szanto
 * @license     MIT
 * @since       2019-09-14
 */

namespace mrbig00\Oblio\Api\Exceptions;


use Throwable;

class OblioException extends \Exception
{
    public function __construct($message = "Unknown error", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}