<?php

namespace Lemon\Http\Client\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use RuntimeException;

class NetworkException extends RuntimeException implements NetworkExceptionInterface
{
}
