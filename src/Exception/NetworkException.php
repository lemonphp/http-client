<?php

namespace Lemon\Http\Client\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use RuntimeException;

/**
 * The network exception
 *
 * @package     Lemon\Http\Client\Exception
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class NetworkException extends RuntimeException implements NetworkExceptionInterface
{
}
