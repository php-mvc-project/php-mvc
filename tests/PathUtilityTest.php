<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use PhpMvc\PathUtility;

final class PathUtilityTest extends TestCase
{

    protected $preserveGlobalState = FALSE;

    protected $runTestInSeparateProcess = TRUE;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (!defined('PHPMVC_ROOT_PATH')) {
            $basePath = getcwd() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'mvc';

            define('PHPMVC_ROOT_PATH', $basePath . DIRECTORY_SEPARATOR);
            define('PHPMVC_DS', DIRECTORY_SEPARATOR);
        }
        
    }

    public function testMapPath(): void
    {
        $this->assertEquals(PHPMVC_ROOT_PATH, PathUtility::mapPath());
        $this->assertEquals(PHPMVC_ROOT_PATH, PathUtility::mapPath('~'));
        $this->assertEquals(PHPMVC_ROOT_PATH, PathUtility::mapPath('~/'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home', PathUtility::mapPath('~/views/home'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php', PathUtility::mapPath('~/views/home/index.php'));
        $this->assertEquals(PHPMVC_ROOT_PATH, PathUtility::mapPath('/'));
        $this->assertEquals(dirname(PHPMVC_ROOT_PATH), PathUtility::mapPath('../'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::mapPath('/index.php'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::mapPath('~/index.php'));
    }

    public function testGetFilePath(): void
    {
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::getFilePath('index.php'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::getFilePath('~index.php'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::getFilePath('~/index.php'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::getFilePath('/index.php'));

        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::getFilePath('index'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::getFilePath('~index'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::getFilePath('~/index'));
        $this->assertEquals(PHPMVC_ROOT_PATH . 'index.php', PathUtility::getFilePath('/index'));

        $this->assertEquals(
            PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php', 
            PathUtility::getFilePath('views/home/index.php')
        );
        $this->assertEquals(
            PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php',
            PathUtility::getFilePath('~views/home/index.php')
        );
        $this->assertEquals(
            PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php',
            PathUtility::getFilePath('~/views/home/index.php')
        );
        $this->assertEquals(
            PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php',
            PathUtility::getFilePath('/views/home/index.php')
        );

        $this->assertEquals(
            PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php', 
            PathUtility::getFilePath('views/home/index')
        );
        $this->assertEquals(
            PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php',
            PathUtility::getFilePath('~views/home/index')
        );
        $this->assertEquals(
            PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php',
            PathUtility::getFilePath('~/views/home/index')
        );
        $this->assertEquals(
            PHPMVC_ROOT_PATH . 'views' . DIRECTORY_SEPARATOR . 'home' . DIRECTORY_SEPARATOR . 'index.php',
            PathUtility::getFilePath('/views/home/index')
        );
    }

}