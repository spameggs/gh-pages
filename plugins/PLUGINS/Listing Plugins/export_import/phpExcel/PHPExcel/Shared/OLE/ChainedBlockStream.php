<?php
class PHPExcel_Shared_OLE_ChainedBlockStream
{
    public $ole;
    public $params;
    public $data;
    public $pos;
    public function stream_open($path, $mode, $options, &$openedPath)
    {
        if ($mode != 'r') {
            if ($options & STREAM_REPORT_ERRORS) {
                trigger_error('Only reading is supported', E_USER_WARNING);
            }
            return false;
        }
        parse_str(substr($path, 25), $this->params);
        if (!isset($this->params['oleInstanceId'], $this->params['blockId'], $GLOBALS['_OLE_INSTANCES'][$this->params['oleInstanceId']])) {
            if ($options & STREAM_REPORT_ERRORS) {
                trigger_error('OLE stream not found', E_USER_WARNING);
            }
            return false;
        }
        $this->ole  = $GLOBALS['_OLE_INSTANCES'][$this->params['oleInstanceId']];
        $blockId    = $this->params['blockId'];
        $this->data = '';
        if (isset($this->params['size']) && $this->params['size'] < $this->ole->bigBlockThreshold && $blockId != $this->ole->root->_StartBlock) {
            $rootPos = $this->ole->_getBlockOffset($this->ole->root->_StartBlock);
            while ($blockId != -2) {
                $pos     = $rootPos + $blockId * $this->ole->bigBlockSize;
                $blockId = $this->ole->sbat[$blockId];
                fseek($this->ole->_file_handle, $pos);
                $this->data .= fread($this->ole->_file_handle, $this->ole->bigBlockSize);
            }
        } else {
            while ($blockId != -2) {
                $pos = $this->ole->_getBlockOffset($blockId);
                fseek($this->ole->_file_handle, $pos);
                $this->data .= fread($this->ole->_file_handle, $this->ole->bigBlockSize);
                $blockId = $this->ole->bbat[$blockId];
            }
        }
        if (isset($this->params['size'])) {
            $this->data = substr($this->data, 0, $this->params['size']);
        }
        if ($options & STREAM_USE_PATH) {
            $openedPath = $path;
        }
        return true;
    }
    public function stream_close()
    {
        $this->ole = null;
        unset($GLOBALS['_OLE_INSTANCES']);
    }
    public function stream_read($count)
    {
        if ($this->stream_eof()) {
            return false;
        }
        $s = substr($this->data, $this->pos, $count);
        $this->pos += $count;
        return $s;
    }
    public function stream_eof()
    {
        $eof = $this->pos >= strlen($this->data);
        if (version_compare(PHP_VERSION, '5.0', '>=') && version_compare(PHP_VERSION, '5.1', '<')) {
            $eof = !$eof;
        }
        return $eof;
    }
    public function stream_tell()
    {
        return $this->pos;
    }
    public function stream_seek($offset, $whence)
    {
        if ($whence == SEEK_SET && $offset >= 0) {
            $this->pos = $offset;
        } elseif ($whence == SEEK_CUR && -$offset <= $this->pos) {
            $this->pos += $offset;
        } elseif ($whence == SEEK_END && -$offset <= sizeof($this->data)) {
            $this->pos = strlen($this->data) + $offset;
        } else {
            return false;
        }
        return true;
    }
    public function stream_stat()
    {
        return array(
            'size' => strlen($this->data)
        );
    }
}