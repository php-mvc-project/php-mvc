<?php
namespace Test;

use PHPUnit\Framework\TestCase;

use PhpMvc\InternalHelper;

final class ArrayToObjectTest extends TestCase
{

    public function testStrict(): void
    {
        $array = array(
            'string' => 'hello',
            'number' => 123,
            'b_string' => 'world!',
            'b_number' => 321,
            'b_boolean' => true,
            'b_c_string' => 'I am C!',
            'b_c_number' => 111,
            'b_c_array' => array(1, 2, 3, 4, 5),
            'b_d[0]_string[0]' => 'first',
            'b_d[0]_string[1]' => 'second',
            'b_d[1]_string[0]' => 'second first',
            'b_d[1]_string[1]' => 'double second',
        );

        InternalHelper::arrayToObject($array, $result, '\Test\A');

        $this->assertEquals('hello', $result->string);
        $this->assertEquals(123, $result->number);
        $this->assertEquals('world!', $result->b->string);
        $this->assertEquals(321, $result->b->number);
        $this->assertEquals(true, $result->b->boolean);
        $this->assertEquals('I am C!', $result->b->c->string);
        $this->assertEquals(111, $result->b->c->number);
        $this->assertEquals(json_encode(array(1, 2, 3, 4, 5)), json_encode($result->b->c->array));
        $this->assertEquals('first', $result->b->d[0]->string[0]);
        $this->assertEquals('second', $result->b->d[0]->string[1]);
        $this->assertEquals('second first', $result->b->d[1]->string[0]);
        $this->assertEquals('double second', $result->b->d[1]->string[1]);

        $this->assertInstanceOf(A::class, $result);
        $this->assertInstanceOf(B::class, $result->b);
        $this->assertInstanceOf(C::class, $result->b->c);
        $this->assertInstanceOf(D::class, $result->b->d[0]);
        $this->assertInstanceOf(D::class, $result->b->d[1]);

        $this->assertInternalType('string', $result->string);
        $this->assertInternalType('integer', $result->number);
        $this->assertInternalType('string', $result->b->string);
        $this->assertInternalType('integer', $result->b->number);
        $this->assertInternalType('boolean', $result->b->boolean);
        $this->assertInternalType('string', $result->b->c->string);
        $this->assertInternalType('integer', $result->b->c->number);
        $this->assertInternalType('array', $result->b->c->array);
        $this->assertInternalType('string', $result->b->d[0]->string[0]);
        $this->assertInternalType('string', $result->b->d[0]->string[1]);
        $this->assertInternalType('string', $result->b->d[1]->string[0]);
        $this->assertInternalType('string', $result->b->d[1]->string[1]);
    }

    public function testNonStrict(): void
    {
        $array = array(
            'string' => 'hello',
            'number' => 123,
            'b_string' => 'world!',
            'b_number' => 321,
            'b_boolean' => true,
            'b_c_string' => 'I am C!',
            'b_c_number' => 111,
            'b_c_array' => array(1, 2, 3, 4, 5),
            'b_d[0]_string[0]' => 'first',
            'b_d[0]_string[1]' => 'second',
            'b_d[1]_string[0]' => 'second first',
            'b_d[1]_string[1]' => 'double second',
        );

        InternalHelper::arrayToObject($array, $result);

        $this->assertEquals('hello', $result->string);
        $this->assertEquals(123, $result->number);
        $this->assertEquals('world!', $result->b->string);
        $this->assertEquals(321, $result->b->number);
        $this->assertEquals(true, $result->b->boolean);
        $this->assertEquals('I am C!', $result->b->c->string);
        $this->assertEquals(111, $result->b->c->number);
        $this->assertEquals(json_encode(array(1, 2, 3, 4, 5)), json_encode($result->b->c->array));
        $this->assertEquals('first', $result->b->d[0]->string[0]);
        $this->assertEquals('second', $result->b->d[0]->string[1]);
        $this->assertEquals('second first', $result->b->d[1]->string[0]);
        $this->assertEquals('double second', $result->b->d[1]->string[1]);

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertInstanceOf(\stdClass::class, $result->b);
        $this->assertInstanceOf(\stdClass::class, $result->b->c);
        $this->assertInstanceOf(\stdClass::class, $result->b->d[0]);
        $this->assertInstanceOf(\stdClass::class, $result->b->d[1]);

        $this->assertInternalType('string', $result->string);
        $this->assertInternalType('integer', $result->number);
        $this->assertInternalType('string', $result->b->string);
        $this->assertInternalType('integer', $result->b->number);
        $this->assertInternalType('boolean', $result->b->boolean);
        $this->assertInternalType('string', $result->b->c->string);
        $this->assertInternalType('integer', $result->b->c->number);
        $this->assertInternalType('array', $result->b->c->array);
        $this->assertInternalType('string', $result->b->d[0]->string[0]);
        $this->assertInternalType('string', $result->b->d[0]->string[1]);
        $this->assertInternalType('string', $result->b->d[1]->string[0]);
        $this->assertInternalType('string', $result->b->d[1]->string[1]);
    }

}

class A {

    /**
     * @var string
     */
    public $string;

    /**
     * @var int
     */
    public $number;

    /**
     * @var B
     */
    public $b;

}

class B {

    /**
     * @var string
     */
    public $string;

    /**
     * @var int
     */
    public $number;

    /**
     * @var bool
     */
    public $boolean;

    /**
     * @var C
     */
    public $c;

    /**
     * @var D[]
     */
    public $d;

}

class C {

    /**
     * @var string
     */
    public $string;

    /**
     * @var int
     */
    public $number;

    /**
     * @var array
     */
    public $array;

}

class D {

    /**
     * @var string
     */
    public $string;

}