<?php
class PHPExcel_DocumentSecurity
{
    private $_lockRevision;
    private $_lockStructure;
    private $_lockWindows;
    private $_revisionsPassword;
    private $_workbookPassword;
    public function __construct()
    {
        $this->_lockRevision      = false;
        $this->_lockStructure     = false;
        $this->_lockWindows       = false;
        $this->_revisionsPassword = '';
        $this->_workbookPassword  = '';
    }
    function isSecurityEnabled()
    {
        return $this->_lockRevision || $this->_lockStructure || $this->_lockWindows;
    }
    function getLockRevision()
    {
        return $this->_lockRevision;
    }
    function setLockRevision($pValue = false)
    {
        $this->_lockRevision = $pValue;
        return $this;
    }
    function getLockStructure()
    {
        return $this->_lockStructure;
    }
    function setLockStructure($pValue = false)
    {
        $this->_lockStructure = $pValue;
        return $this;
    }
    function getLockWindows()
    {
        return $this->_lockWindows;
    }
    function setLockWindows($pValue = false)
    {
        $this->_lockWindows = $pValue;
        return $this;
    }
    function getRevisionsPassword()
    {
        return $this->_revisionsPassword;
    }
    function setRevisionsPassword($pValue = '', $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PHPExcel_Shared_PasswordHasher::hashPassword($pValue);
        }
        $this->_revisionsPassword = $pValue;
        return $this;
    }
    function getWorkbookPassword()
    {
        return $this->_workbookPassword;
    }
    function setWorkbookPassword($pValue = '', $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PHPExcel_Shared_PasswordHasher::hashPassword($pValue);
        }
        $this->_workbookPassword = $pValue;
        return $this;
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}