<?php

namespace Lemon\Http\Client\Exception;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * The client exception
 *
 * @package     Lemon\Http\Client\Exception
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class ClientException extends Exception implements ClientExceptionInterface
{
}
