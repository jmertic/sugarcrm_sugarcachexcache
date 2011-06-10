<?php
/*
Copyright 2011 John Mertic. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, this list of
      conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice, this list
      of conditions and the following disclaimer in the documentation and/or other materials
      provided with the distribution.

THIS SOFTWARE IS PROVIDED BY JOHN MERTIC ``AS IS'' AND ANY EXPRESS OR IMPLIED
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those of the
authors and should not be interpreted as representing official policies, either expressed
or implied, of John Mertic.
*/

require_once('include/SugarCache/SugarCacheAbstract.php');

class SugarCacheXCache extends SugarCacheAbstract
{
    /**
     * @see SugarCacheAbstract::$_priority
     */
    protected $_priority = 980;
    
    /**
     * @see SugarCacheAbstract::useBackend()
     */
    public function useBackend()
    {
        if ( !parent::useBackend() )
            return false;
        
        if ( function_exists("xcache_get")
                && empty($GLOBALS['sugar_config']['external_cache_disabled_xcache']))
            return true;
            
        return false;
    }
    
    /**
     * @see SugarCacheAbstract::_setExternal()
     */
    protected function _setExternal(
        $key,
        $value
        )
    {
        xcache_set($key,serialize($value),$this->expireTimeout);
    }
    
    /**
     * @see SugarCacheAbstract::_getExternal()
     */
    protected function _getExternal(
        $key
        )
    {
        if ( !xcache_isset($key) ) {
            return null;
        }
        
        $raw_cache_value = xcache_get($key);
        return is_string($raw_cache_value) ?
            unserialize($raw_cache_value) :
            $raw_cache_value;
    }
    
    /**
     * @see SugarCacheAbstract::_clearExternal()
     */
    protected function _clearExternal(
        $key
        )
    {
        xcache_unset($key);
    }
    
    /**
     * @see SugarCacheAbstract::_resetExternal()
     */
    protected function _resetExternal()
    {
        $max = xcache_count(XC_TYPE_VAR);
        for ($i = 0; $i < $max; $i++) {
            if (!xcache_clear_cache(XC_TYPE_VAR, $i)) {
                break;
            }
        }
    }
}
