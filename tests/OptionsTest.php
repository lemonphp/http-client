<?php

namespace Tests;

use Lemon\Http\Client\Options;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * The HTTP client test
 *
 * @package     Tests
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @copyright   LemonPHP Team
 * @license     The MIT License
 */
class OptionsTest extends TestCase
{
    /**
     * Test it should has default values
     *
     * @return void
     */
    public function testItShouldHasDefaultValues()
    {
        $options = new Options();

        $this->assertSame(false, $options->follow_location);
        $this->assertSame(10 * 1000, $options->timeout);
        $this->assertFalse(isset($options->undefined));
    }

    /**
     * Test it should be countable implement
     *
     * @return void
     */
    public function testItShouldBeCountable()
    {
        $options = new Options();

        $this->assertTrue(count($options) === $options->count());
    }

    /**
     * Test it should be ArrayAccess implement
     *
     * @return void
     */
    public function testItShouldBeArrayAccess()
    {
        $options = new Options();

        $this->assertIsInt($options['timeout']);
        $this->assertIsBool($options['follow_location']);
        $this->assertFalse(isset($options['undefined']));
    }

    /**
     * Test it should be readonly object
     *
     * @return void
     */
    public function testItShouldThrowExceptionWhenSet()
    {
        $this->expectExceptionObject(new LogicException('Client options is readonly object'));

        $options = new Options();
        $options->timeout = 50000;
    }

    /**
     * Test it should be readonly object
     *
     * @return void
     */
    public function testItShouldThrowExceptionWhenUnset()
    {
        $this->expectExceptionObject(new LogicException('Client options is readonly object'));

        $options = new Options();
        unset($options->timeout);
    }
}
