<?php
declare(strict_types=1);

require_once 'models/ModelA.php';

use PHPUnit\Framework\TestCase;

use PhpMvc\FileCacheProviderConfig;
use PhpMvc\FileCacheProvider;

final class FileCacheProviderTest extends TestCase
{

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $basePath = getcwd();

        if (!defined('PHPMVC_DS')) {
            define('PHPMVC_DS', DIRECTORY_SEPARATOR);
        }

        if (!defined('PHPMVC_ROOT_PATH')) {
            define('PHPMVC_ROOT_PATH', $basePath . PHPMVC_DS);
        }
    }

    public function testDefault(): void {
        $cache = new FileCacheProvider();

        $cache->init();
        $cache->clear();

        $this->assertNull($cache->get('test'));
        $this->assertFalse($cache->contains('test'));
        $this->assertEquals(0, $cache->count());

        $this->assertEquals('Hello, World!', $cache->getOrAdd('test', 'Hello, World!', 2));
        $this->assertEquals('Hello, World!', $cache->getOrAdd('test', 'World, hello!', 2));
        $this->assertEquals('Hello, World!', $cache->get('test'));

        $this->assertTrue($cache->contains('test'));
        $this->assertEquals(1, $cache->count());

        sleep(3);

        $this->assertNull($cache->get('test'));

        $this->assertEquals('Hello, World!', $cache->getOrAdd('test', function() { return 'Hello, World!'; }, 2));
        $this->assertEquals('Hello, World!', $cache->get('test'));

        sleep(3);

        $this->assertNull($cache->get('test'));

        $cache->add('test', 'Hello, World!', 2);
        $this->assertEquals('Hello, World!', $cache->get('test'));

        sleep(3);

        $this->assertNull($cache->get('test'));

        $cache->add('xyz', 123, 30);

        $this->assertEquals(123, $cache->get('xyz'));

        $cache->add('xyz', 321, 30);

        $this->assertEquals(321, $cache->get('xyz'));

        $cache->remove('xyz');

        $this->assertNull($cache->get('xyz'));

        $array = array('message' => 'Hello, world!', 'number' => 123, 'bool' => true);

        $cache->add('array', $array, 30);

        $this->assertEquals(json_encode($array), json_encode($cache->get('array')));

        $cache->remove('array');

        $this->assertNull($cache->get('array'));

        $class = new ModelA();

        $class->text = 'Hello, world!';
        $class->number = 42;
        $class->boolean = true;
        $class->array = $array;

        $cache->add('class', $class, 30);

        $this->assertEquals(json_encode($class), json_encode($cache->get('class')));

        $cache->remove('class');

        $this->assertNull($cache->get('class'));

        $cache->add('null', null, 30);

        $this->assertNull($cache->get('null'));
        $this->assertTrue($cache->contains('null'));

        $cache->remove('null');

        $this->assertNull($cache->get('null'));
    }

    public function testRegion(): void {
        $cache = new FileCacheProvider();

        $cache->init();
        $cache->clear();

        $this->assertNull($cache->get('test'));
        $this->assertNull($cache->get('test', 'ru'));
        $this->assertNull($cache->get('test', 'fr'));

        $this->assertFalse($cache->contains('test'));
        $this->assertFalse($cache->contains('test', 'ru'));

        $this->assertEquals('Hello, World!', $cache->getOrAdd('test', 'Hello, World!', 2));
        $this->assertEquals('Привет, мир!', $cache->getOrAdd('test', 'Привет, мир!', 2, 'ru'));
        $this->assertEquals('Hello, World!', $cache->getOrAdd('test', 'Hello, World!', 2));

        $this->assertEquals('Hello, World!', $cache->get('test'));
        $this->assertEquals('Привет, мир!', $cache->get('test', 'ru'));
        $this->assertEquals('Hello, World!', $cache->get('test'));
        $this->assertEquals('Привет, мир!', $cache->get('test', 'ru'));

        $this->assertTrue($cache->contains('test'));
        $this->assertTrue($cache->contains('test', 'ru'));

        sleep(3);

        $this->assertNull($cache->get('test'));
        $this->assertNull($cache->get('test', 'ru'));

        $this->assertEquals('Привет, мир!', $cache->getOrAdd('test', function() { return 'Привет, мир!'; }, 2, 'ru'));
        $this->assertEquals('Привет, мир!', $cache->get('test', 'ru'));

        sleep(3);

        $this->assertNull($cache->get('test', 'ru'));

        $cache->add('test', 'Привет, мир!', 2, 'ru');
        $this->assertEquals('Привет, мир!', $cache->get('test', 'ru'));

        sleep(3);

        $this->assertNull($cache->get('test', 'ru'));

        $cache->add('xyz', 555, 30, 'ru');

        $this->assertEquals(555, $cache->get('xyz', 'ru'));

        $cache->remove('xyz', 'ru');

        $this->assertNull($cache->get('xyz', 'ru'));
    }

    public function testKeys(): void {
        $cache = new FileCacheProvider();

        $cache->init();
        $cache->clear();

        $this->assertNull($cache->get('!@#/#$%@&*(/\\'));

        $this->assertEquals('Hello, World!', $cache->getOrAdd('!@#/#$%@&*(/\\', function() { return 'Hello, World!'; }, 5));
        $this->assertEquals('Hello, World!', $cache->getOrAdd('!@####!/#$%@&*(/\\', function() { return 123; }, 5));

        $cache->remove('///');

        $this->assertNull($cache->get('!@#/#$%@&*(/\\'));

        $cache = new FileCacheProvider(array('hash' => 'sha1'));

        $cache->init();

        $this->assertEquals('Hello, World!', $cache->getOrAdd('!@#/#$%@&*(/\\', function() { return 'Hello, World!'; }, 5));
        $this->assertEquals(123, $cache->getOrAdd('!@####!/#$%@&*(/\\', function() { return 123; }, 5));

        $this->assertNull($cache->remove('///'));
        $this->assertNotNull($cache->remove('!@#/#$%@&*(/\\'));
        $this->assertNotNull($cache->remove('!@####!/#$%@&*(/\\'));

        $this->assertNull($cache->get('!@#/#$%@&*(/\\'));
        $this->assertNull($cache->get('!@####!/#$%@&*(/\\'));
    }

    public function testAccess(): void {
        $cache = new FileCacheProvider();

        $cache->init();
        $cache->clear();

        $cache->add('test', 'Hello, world!', 300);

        echo chr(10);

        // read
        echo 'Read: ' . chr(10);

        for ($i = 0; $i < 1000; ++$i) {
            echo $i;
            $this->assertEquals('Hello, world!', $cache->get('test'));
            echo ' - OK' . chr(10);
        }

        // read and write
        echo 'Write and read: ' . chr(10);

        for ($i = 0; $i < 1000; ++$i) {
            echo $i;
            $cache->add('test', $i, 30);
            $this->assertEquals($i, $cache->get('test'));
            echo ' - OK' . chr(10);
        }

        // TODO: threads

        $cache->remove('test');

        $this->assertNull($cache->get('test'));
    }

}