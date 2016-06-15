<?php
class PHPExcel_CachedObjectStorage_Igbinary extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
{
    protected function _storeData()
    {
        if ($this->_currentCellIsDirty) {
            $this->_currentObject->detach();
            $this->_cellCache[$this->_currentObjectID] = igbinary_serialize($this->_currentObject);
            $this->_currentCellIsDirty                 = false;
        }
        $this->_currentObjectID = $this->_currentObject = null;
    }
    public function addCacheData($pCoord, PHPExcel_Cell $cell)
    {
        if (($pCoord !== $this->_currentObjectID) && ($this->_currentObjectID !== null)) {
            $this->_storeData();
        }
        $this->_currentObjectID    = $pCoord;
        $this->_currentObject      = $cell;
        $this->_currentCellIsDirty = true;
        return $cell;
    }
    public function getCacheData($pCoord)
    {
        if ($pCoord === $this->_currentObjectID) {
            return $this->_currentObject;
        }
        $this->_storeData();
        if (!isset($this->_cellCache[$pCoord])) {
            return null;
        }
        $this->_currentObjectID = $pCoord;
        $this->_currentObject   = igbinary_unserialize($this->_cellCache[$pCoord]);
        $this->_currentObject->attach($this->_parent);
        return $this->_currentObject;
    }
    public function getCellList()
    {
        if ($this->_currentObjectID !== null) {
            $this->_storeData();
        }
        return parent::getCellList();
    }
    public function unsetWorksheetCells()
    {
        if (!is_null($this->_currentObject)) {
            $this->_currentObject->detach();
            $this->_currentObject = $this->_currentObjectID = null;
        }
        $this->_cellCache = array();
        $this->_parent    = null;
    }
    public static function cacheMethodIsAvailable()
    {
        if (!function_exists('igbinary_serialize')) {
            return false;
        }
        return true;
    }
}