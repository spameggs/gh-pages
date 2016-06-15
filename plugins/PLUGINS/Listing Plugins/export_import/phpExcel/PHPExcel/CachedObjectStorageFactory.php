<?php
class PHPExcel_CachedObjectStorageFactory
{
    const cache_in_memory = 'Memory';
    const cache_in_memory_gzip = 'MemoryGZip';
    const cache_in_memory_serialized = 'MemorySerialized';
    const cache_igbinary = 'Igbinary';
    const cache_to_discISAM = 'DiscISAM';
    const cache_to_apc = 'APC';
    const cache_to_memcache = 'Memcache';
    const cache_to_phpTemp = 'PHPTemp';
    const cache_to_wincache = 'Wincache';
    const cache_to_sqlite = 'SQLite';
    const cache_to_sqlite3 = 'SQLite3';
    private static $_cacheStorageMethod = NULL;
    private static $_cacheStorageClass = NULL;
    private static $_storageMethods = array(self::cache_in_memory, self::cache_in_memory_gzip, self::cache_in_memory_serialized, self::cache_igbinary, self::cache_to_phpTemp, self::cache_to_discISAM, self::cache_to_apc, self::cache_to_memcache, self::cache_to_wincache, self::cache_to_sqlite, self::cache_to_sqlite3);
    private static $_storageMethodDefaultParameters = array(self::cache_in_memory => array(), self::cache_in_memory_gzip => array(), self::cache_in_memory_serialized => array(), self::cache_igbinary => array(), self::cache_to_phpTemp => array('memoryCacheSize' => '1MB'), self::cache_to_discISAM => array('dir' => NULL), self::cache_to_apc => array('cacheTime' => 600), self::cache_to_memcache => array('memcacheServer' => 'localhost', 'memcachePort' => 11211, 'cacheTime' => 600), self::cache_to_wincache => array('cacheTime' => 600), self::cache_to_sqlite => array(), self::cache_to_sqlite3 => array());
    private static $_storageMethodParameters = array();
    public static function getCacheStorageMethod()
    {
        return self::$_cacheStorageMethod;
    }
    public static function getCacheStorageClass()
    {
        return self::$_cacheStorageClass;
    }
    public static function getAllCacheStorageMethods()
    {
        return self::$_storageMethods;
    }
    public static function getCacheStorageMethods()
    {
        $activeMethods = array();
        foreach (self::$_storageMethods as $storageMethod) {
            $cacheStorageClass = 'PHPExcel_CachedObjectStorage_' . $storageMethod;
            if (call_user_func(array(
                $cacheStorageClass,
                'cacheMethodIsAvailable'
            ))) {
                $activeMethods[] = $storageMethod;
            }
        }
        return $activeMethods;
    }
    public static function initialize($method = self::cache_in_memory, $arguments = array())
    {
        if (!in_array($method, self::$_storageMethods)) {
            return FALSE;
        }
        $cacheStorageClass = 'PHPExcel_CachedObjectStorage_' . $method;
        if (!call_user_func(array(
            $cacheStorageClass,
            'cacheMethodIsAvailable'
        ))) {
            return FALSE;
        }
        self::$_storageMethodParameters[$method] = self::$_storageMethodDefaultParameters[$method];
        foreach ($arguments as $k => $v) {
            if (array_key_exists($k, self::$_storageMethodParameters[$method])) {
                self::$_storageMethodParameters[$method][$k] = $v;
            }
        }
        if (self::$_cacheStorageMethod === NULL) {
            self::$_cacheStorageClass  = 'PHPExcel_CachedObjectStorage_' . $method;
            self::$_cacheStorageMethod = $method;
        }
        return TRUE;
    }
    public static function getInstance(PHPExcel_Worksheet $parent)
    {
        $cacheMethodIsAvailable = TRUE;
        if (self::$_cacheStorageMethod === NULL) {
            $cacheMethodIsAvailable = self::initialize();
        }
        if ($cacheMethodIsAvailable) {
            $instance = new self::$_cacheStorageClass($parent, self::$_storageMethodParameters[self::$_cacheStorageMethod]);
            if ($instance !== NULL) {
                return $instance;
            }
        }
        return FALSE;
    }
    public static function finalize()
    {
        self::$_cacheStorageMethod      = NULL;
        self::$_cacheStorageClass       = NULL;
        self::$_storageMethodParameters = array();
    }
}