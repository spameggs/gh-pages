<?php
class parseCSV
{
    var $heading = true;
    var $fields = array();
    var $sort_by = null;
    var $sort_reverse = false;
    var $delimiter = ',';
    var $enclosure = '"';
    var $conditions = null;
    var $offset = null;
    var $limit = null;
    var $auto_depth = 15;
    var $auto_non_chars = "a-zA-Z0-9\n\r";
    var $auto_preferred = ",;\t.:|";
    var $convert_encoding = false;
    var $input_encoding = 'ISO-8859-1';
    var $output_encoding = 'ISO-8859-1';
    var $linefeed = "\r\n";
    var $output_delimiter = ',';
    var $output_filename = 'data.csv';
    var $file;
    var $file_data;
    var $titles = array();
    var $data = array();
    function parseCSV($input = null, $offset = null, $limit = null, $conditions = null)
    {
        if ($offset !== null)
            $this->offset = $offset;
        if ($limit !== null)
            $this->limit = $limit;
        if (count($conditions) > 0)
            $this->conditions = $conditions;
        if (!empty($input))
            $this->parse($input);
    }
    function parse($input = null, $offset = null, $limit = null, $conditions = null)
    {
        if (!empty($input)) {
            if ($offset !== null)
                $this->offset = $offset;
            if ($limit !== null)
                $this->limit = $limit;
            if (count($conditions) > 0)
                $this->conditions = $conditions;
            if (is_readable($input)) {
                $this->data = $this->parse_file($input);
            } else {
                $this->file_data =& $input;
                $this->data = $this->parse_string();
            }
            if ($this->data === false)
                return false;
        }
        return true;
    }
    function save($file = null, $data = array(), $append = false, $fields = array())
    {
        if (empty($file))
            $file =& $this->file;
        $mode   = ($append) ? 'at' : 'wt';
        $is_php = (preg_match('/\.php$/i', $file)) ? true : false;
        return $this->_wfile($file, $this->unparse($data, $fields, $append, $is_php), $mode);
    }
    function output($output = true, $filename = null, $data = array(), $fields = array(), $delimiter = null)
    {
        if (empty($filename))
            $filename = $this->output_filename;
        if ($delimiter === null)
            $delimiter = $this->output_delimiter;
        $data = $this->unparse($data, $fields, null, null, $delimiter);
        if ($output) {
            header('Content-type: application/csv');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            echo $data;
        }
        return $data;
    }
    function encoding($input = null, $output = null)
    {
        $this->convert_encoding = true;
        if ($input !== null)
            $this->input_encoding = $input;
        if ($output !== null)
            $this->output_encoding = $output;
    }
    function auto($file = null, $parse = true, $search_depth = null, $preferred = null, $enclosure = null)
    {
        if ($file === null)
            $file = $this->file;
        if (empty($search_depth))
            $search_depth = $this->auto_depth;
        if ($enclosure === null)
            $enclosure = $this->enclosure;
        if ($preferred === null)
            $preferred = $this->auto_preferred;
        if (empty($this->file_data)) {
            if ($this->_check_data($file)) {
                $data =& $this->file_data;
            } else
                return false;
        } else {
            $data =& $this->file_data;
        }
        $chars    = array();
        $strlen   = strlen($data);
        $enclosed = false;
        $n        = 1;
        $to_end   = true;
        for ($i = 0; $i < $strlen; $i++) {
            $ch  = $data{$i};
            $nch = (isset($data{$i + 1})) ? $data{$i + 1} : false;
            $pch = (isset($data{$i - 1})) ? $data{$i - 1} : false;
            if ($ch == $enclosure && (!$enclosed || $nch != $enclosure)) {
                $enclosed = ($enclosed) ? false : true;
            } elseif ($ch == $enclosure && $enclosed) {
                $i++;
            } elseif (($ch == "\n" && $pch != "\r" || $ch == "\r") && !$enclosed) {
                if ($n >= $search_depth) {
                    $strlen = 0;
                    $to_end = false;
                } else {
                    $n++;
                }
            } elseif (!$enclosed) {
                if (!preg_match('/[' . preg_quote($this->auto_non_chars, '/') . ']/i', $ch)) {
                    if (!isset($chars[$ch][$n])) {
                        $chars[$ch][$n] = 1;
                    } else {
                        $chars[$ch][$n]++;
                    }
                }
            }
        }
        $depth    = ($to_end) ? $n - 1 : $n;
        $filtered = array();
        foreach ($chars as $char => $value) {
            if ($match = $this->_check_count($char, $value, $depth, $preferred)) {
                $filtered[$match] = $char;
            }
        }
        ksort($filtered);
        $delimiter       = reset($filtered);
        $this->delimiter = $delimiter;
        if ($parse)
            $this->data = $this->parse_string();
        return $delimiter;
    }
    function parse_file($file = null)
    {
        if ($file === null)
            $file = $this->file;
        if (empty($this->file_data))
            $this->load_data($file);
        return (!empty($this->file_data)) ? $this->parse_string() : false;
    }
    function parse_string($data = null)
    {
        if (empty($data)) {
            if ($this->_check_data()) {
                $data =& $this->file_data;
            } else
                return false;
        }
        $rows         = array();
        $row          = array();
        $row_count    = 0;
        $current      = '';
        $head         = (!empty($this->fields)) ? $this->fields : array();
        $col          = 0;
        $enclosed     = false;
        $was_enclosed = false;
        $strlen       = strlen($data);
        for ($i = 0; $i < $strlen; $i++) {
            $ch  = $data{$i};
            $nch = (isset($data{$i + 1})) ? $data{$i + 1} : false;
            $pch = (isset($data{$i - 1})) ? $data{$i - 1} : false;
            if ($ch == $this->enclosure && (!$enclosed || $nch != $this->enclosure)) {
                $enclosed = ($enclosed) ? false : true;
                if ($enclosed)
                    $was_enclosed = true;
            } elseif ($ch == $this->enclosure && $enclosed) {
                $current .= $ch;
                $i++;
            } elseif (($ch == $this->delimiter || ($ch == "\n" && $pch != "\r") || $ch == "\r") && !$enclosed) {
                if (!$was_enclosed)
                    $current = trim($current);
                $key       = (!empty($head[$col])) ? $head[$col] : $col;
                $row[$key] = $current;
                $current   = '';
                $col++;
                if ($ch == "\n" || $ch == "\r") {
                    if ($this->_validate_offset($row_count) && $this->_validate_row_conditions($row, $this->conditions)) {
                        if ($this->heading && empty($head)) {
                            $head = $row;
                        } elseif (empty($this->fields) || (!empty($this->fields) && (($this->heading && $row_count > 0) || !$this->heading))) {
                            if (!empty($this->sort_by) && !empty($row[$this->sort_by])) {
                                if (isset($rows[$row[$this->sort_by]])) {
                                    $rows[$row[$this->sort_by] . '_0'] =& $rows[$row[$this->sort_by]];
                                    unset($rows[$row[$this->sort_by]]);
                                    for ($sn = 1; isset($rows[$row[$this->sort_by] . '_' . $sn]); $sn++) {
                                    }
                                    $rows[$row[$this->sort_by] . '_' . $sn] = $row;
                                } else
                                    $rows[$row[$this->sort_by]] = $row;
                            } else
                                $rows[] = $row;
                        }
                    }
                    $row = array();
                    $col = 0;
                    $row_count++;
                    if ($this->sort_by === null && $this->limit !== null && count($rows) == $this->limit) {
                        $i = $strlen;
                    }
                }
            } else {
                $current .= $ch;
            }
        }
        $this->titles = $head;
        if (!empty($this->sort_by)) {
            ($this->sort_reverse) ? krsort($rows) : ksort($rows);
            if ($this->offset !== null || $this->limit !== null) {
                $rows = array_slice($rows, ($this->offset === null ? 0 : $this->offset), $this->limit, true);
            }
        }
        return $rows;
    }
    function unparse($data = array(), $fields = array(), $append = false, $is_php = false, $delimiter = null)
    {
        if (!is_array($data) || empty($data))
            $data =& $this->data;
        if (!is_array($fields) || empty($fields))
            $fields =& $this->titles;
        if ($delimiter === null)
            $delimiter = $this->delimiter;
        $string = ($is_php) ? "<?php header('Status: 403'); die(' '); ?>" . $this->linefeed : '';
        $entry  = array();
        if ($this->heading && !$append) {
            foreach ($fields as $key => $value) {
                $entry[] = $this->_enclose_value($value);
            }
            $string .= implode($delimiter, $entry) . $this->linefeed;
            $entry = array();
        }
        foreach ($data as $key => $row) {
            foreach ($row as $field => $value) {
                $entry[] = $this->_enclose_value($value);
            }
            $string .= implode($delimiter, $entry) . $this->linefeed;
            $entry = array();
        }
        return $string;
    }
    function load_data($input = null)
    {
        $data = null;
        $file = null;
        if ($input === null) {
            $file = $this->file;
        } elseif (file_exists($input)) {
            $file = $input;
        } else {
            $data = $input;
        }
        if (!empty($data) || $data = $this->_rfile($file)) {
            if ($this->file != $file)
                $this->file = $file;
            if (preg_match('/\.php$/i', $file) && preg_match('/<\?.*?\?>(.*)/ims', $data, $strip)) {
                $data = ltrim($strip[1]);
            }
            if ($this->convert_encoding)
                $data = iconv($this->input_encoding, $this->output_encoding, $data);
            if (substr($data, -1) != "\n")
                $data .= "\n";
            $this->file_data =& $data;
            return true;
        }
        return false;
    }
    function _validate_row_conditions($row = array(), $conditions = null)
    {
        if (!empty($row)) {
            if (!empty($conditions)) {
                $conditions = (strpos($conditions, ' OR ') !== false) ? explode(' OR ', $conditions) : array(
                    $conditions
                );
                $or         = '';
                foreach ($conditions as $key => $value) {
                    if (strpos($value, ' AND ') !== false) {
                        $value = explode(' AND ', $value);
                        $and   = '';
                        foreach ($value as $k => $v) {
                            $and .= $this->_validate_row_condition($row, $v);
                        }
                        $or .= (strpos($and, '0') !== false) ? '0' : '1';
                    } else {
                        $or .= $this->_validate_row_condition($row, $value);
                    }
                }
                return (strpos($or, '1') !== false) ? true : false;
            }
            return true;
        }
        return false;
    }
    function _validate_row_condition($row, $condition)
    {
        $operators       = array(
            '=',
            'equals',
            'is',
            '!=',
            'is not',
            '<',
            'is less than',
            '>',
            'is greater than',
            '<=',
            'is less than or equals',
            '>=',
            'is greater than or equals',
            'contains',
            'does not contain'
        );
        $operators_regex = array();
        foreach ($operators as $value) {
            $operators_regex[] = preg_quote($value, '/');
        }
        $operators_regex = implode('|', $operators_regex);
        if (preg_match('/^(.+) (' . $operators_regex . ') (.+)$/i', trim($condition), $capture)) {
            $field = $capture[1];
            $op    = $capture[2];
            $value = $capture[3];
            if (preg_match('/^([\'\"]{1})(.*)([\'\"]{1})$/i', $value, $capture)) {
                if ($capture[1] == $capture[3]) {
                    $value = $capture[2];
                    $value = str_replace("\\n", "\n", $value);
                    $value = str_replace("\\r", "\r", $value);
                    $value = str_replace("\\t", "\t", $value);
                    $value = stripslashes($value);
                }
            }
            if (array_key_exists($field, $row)) {
                if (($op == '=' || $op == 'equals' || $op == 'is') && $row[$field] == $value) {
                    return '1';
                } elseif (($op == '!=' || $op == 'is not') && $row[$field] != $value) {
                    return '1';
                } elseif (($op == '<' || $op == 'is less than') && $row[$field] < $value) {
                    return '1';
                } elseif (($op == '>' || $op == 'is greater than') && $row[$field] > $value) {
                    return '1';
                } elseif (($op == '<=' || $op == 'is less than or equals') && $row[$field] <= $value) {
                    return '1';
                } elseif (($op == '>=' || $op == 'is greater than or equals') && $row[$field] >= $value) {
                    return '1';
                } elseif ($op == 'contains' && preg_match('/' . preg_quote($value, '/') . '/i', $row[$field])) {
                    return '1';
                } elseif ($op == 'does not contain' && !preg_match('/' . preg_quote($value, '/') . '/i', $row[$field])) {
                    return '1';
                } else {
                    return '0';
                }
            }
        }
        return '1';
    }
    function _validate_offset($current_row)
    {
        if ($this->sort_by === null && $this->offset !== null && $current_row < $this->offset)
            return false;
        return true;
    }
    function _enclose_value($value = null)
    {
        if ($value !== null && $value != '') {
            $delimiter = preg_quote($this->delimiter, '/');
            $enclosure = preg_quote($this->enclosure, '/');
            if (preg_match("/" . $delimiter . "|" . $enclosure . "|\n|\r/i", $value) || ($value{0} == ' ' || substr($value, -1) == ' ')) {
                $value = str_replace($this->enclosure, $this->enclosure . $this->enclosure, $value);
                $value = $this->enclosure . $value . $this->enclosure;
            }
        }
        return $value;
    }
    function _check_data($file = null)
    {
        if (empty($this->file_data)) {
            if ($file === null)
                $file = $this->file;
            return $this->load_data($file);
        }
        return true;
    }
    function _check_count($char, $array, $depth, $preferred)
    {
        if ($depth == count($array)) {
            $first  = null;
            $equal  = null;
            $almost = false;
            foreach ($array as $key => $value) {
                if ($first == null) {
                    $first = $value;
                } elseif ($value == $first && $equal !== false) {
                    $equal = true;
                } elseif ($value == $first + 1 && $equal !== false) {
                    $equal  = true;
                    $almost = true;
                } else {
                    $equal = false;
                }
            }
            if ($equal) {
                $match = ($almost) ? 2 : 1;
                $pref  = strpos($preferred, $char);
                $pref  = ($pref !== false) ? str_pad($pref, 3, '0', STR_PAD_LEFT) : '999';
                return $pref . $match . '.' . (99999 - str_pad($first, 5, '0', STR_PAD_LEFT));
            } else
                return false;
        }
    }
    function _rfile($file = null)
    {
        if (is_readable($file)) {
            if (!($fh = fopen($file, 'r')))
                return false;
            $data = fread($fh, filesize($file));
            fclose($fh);
            return $data;
        }
        return false;
    }
    function _wfile($file, $string = '', $mode = 'wb', $lock = 2)
    {
        if ($fp = fopen($file, $mode)) {
            flock($fp, $lock);
            $re  = fwrite($fp, $string);
            $re2 = fclose($fp);
            if ($re != false && $re2 != false)
                return true;
        }
        return false;
    }
}
?>