<?php
namespace PhpMvcTest\Controllers;

use \PhpMvc\OutputCache;
use \PhpMvc\OutputCacheLocation;
use \PhpMvc\Controller;

class OutputCacheController extends Controller {

    public function __construct() {
        OutputCache::setLocation('nocache', OutputCacheLocation::NONE);

        OutputCache::setDuration('duration10', 10);

        OutputCache::set('locationServer', 10, OutputCacheLocation::SERVER);
        OutputCache::set('locationClient', 10, OutputCacheLocation::CLIENT);
        OutputCache::set('locationServerAndClient', 10, OutputCacheLocation::SERVER_AND_CLIENT);
        OutputCache::set('locationDownstream', 10, OutputCacheLocation::DOWNSTREAM);

        OutputCache::setVaryByParam('varyByParam', 'id');
        OutputCache::setDuration('varyByParam', 30);

        OutputCache::set('varyByParam2', 30, OutputCacheLocation::ANY, 'id;abc');

        OutputCache::setVaryByHeader('VaryByHeader', 'User-Agent');
        OutputCache::setDuration('varybyheader', 30);

        OutputCache::setVaryByCustom('VARYBYCUSTOM', function ($actionContext) {
            $userLanguages = $actionContext->getHttpContext()->getRequest()->userLanguages();

            if (isset($userLanguages['en'])) {
                return true;
            }
            else {
                return false;
            }
        });
        OutputCache::setDuration('varybyCustom', 30);
    }

    public function index($time) {
        return $this->content('time => ' . $time);
    }

    public function nocache($time) {
        return $this->content('time => ' . $time);
    }

    public function duration10($time) {
        return $this->content('time => ' . $time);
    }

    public function locationServer($time) {
        return $this->content('time => ' . $time);
    }

    public function locationClient($time) {
        return $this->content('time => ' . $time);
    }

    public function locationServerAndClient($time) {
        return $this->content('time => ' . $time);
    }

    public function locationDownstream($time) {
        return $this->content('time => ' . $time);
    }

    public function varyByParam($id, $time) {
        return $this->content($id . ' => ' . $time);
    }

    public function varyByParam2($id, $abc, $time) {
        return $this->content($id . ' => ' . $abc . ' => ' . $time);
    }

    public function varyByHeader($id, $time) {
        return $this->content($this->getRequest()->userAgent() . ' => ' . $time);
    }

    public function varyByCustom() {
        $languages = $this->getHttpContext()->getRequest()->userLanguages();
        return $this->content($this->getRequest()->get('time'));
    }

}