<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpMvc;

/**
 * Specifies the valid values for controlling the location of the output-cached HTTP response for a resource.
 */
final class OutputCacheLocation {

    /**
     * The output cache can be located on the browser client (where the request originated), on a proxy server (or any other server) participating in the request, or on the server where the request was processed.
     * 
    * @var int
     */
    const ANY = 0;

    /**
     * The output cache is located on the browser client where the request originated.
     * 
    * @var int
     */
    const CLIENT = 1;

    /**
     * The output cache can be stored in any HTTP 1.1 cache-capable devices other than the origin server.
     * 
    * @var int
     */
    const DOWNSTREAM = 2;

    /**
     * The output cache is disabled for the requested page.
     * 
     * @var int
     */
    const NONE = 3;

    /**
     * The output cache is located on the Web server where the request was processed.
     * 
     * @var int
     */
    const SERVER = 4;

    /**
     * The output cache can be stored only at the origin server or at the requesting client.
     * 
     * @var int
     */
    const SERVER_AND_CLIENT = 5;

}