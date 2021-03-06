<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart <info@slimframework.com>
 * @copyright   2011 Josh Lockhart
 * @link        http://www.slimframework.com
 * @license     http://www.slimframework.com/license
 * @version     1.6.4
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once 'Slim/Slim.php';

class PrettyExceptionsTest extends PHPUnit_Framework_TestCase {
    /**
     * Test middleware returns successful response unchanged
     */
    public function testReturnsUnchangedSuccessResponse() {
        Slim_Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/foo'
        ));
        $app = new Slim();
        $app->get('/foo', function () {
            echo "Success";
        });
        $mw = new Slim_Middleware_PrettyExceptions();
        $mw->setApplication($app);
        $mw->setNextMiddleware($app);
        $mw->call();
        $this->assertEquals(200, $app->response()->status());
        $this->assertEquals('Success', $app->response()->body());
    }

    /**
     * Test middleware returns diagnostic screen for error response
     */
    public function testReturnsDiagnosticsForErrorResponse() {
        Slim_Environment::mock(array(
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '/foo'
        ));
        $app = new Slim(array(
            'log.enabled' => false
        ));
        $app->get('/foo', function () {
            throw new Exception('Test Message', 100);
        });
        $mw = new Slim_Middleware_PrettyExceptions();
        $mw->setApplication($app);
        $mw->setNextMiddleware($app);
        $mw->call();
        $this->assertEquals(1, preg_match('@Slim Application Error@', $app->response()->body()));
        $this->assertEquals(500, $app->response()->status());
    }
}