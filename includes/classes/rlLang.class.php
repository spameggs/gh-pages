<?php
class rlLang extends reefless
{
    var $rlConfig;
    var $rlValid;
    function rlLang()
    {
        global $rlConfig, $rlValid;
        $this->rlConfig =& $rlConfig;
        $this->rlValid =& $rlValid;
    }
    function replaceLangKeys($data = null, $module = '', $fields = null, $langCode = RL_LANG_CODE, $side = 'frontEnd', $status = 'active')
    {
        $this->setTable('lang_keys');
        if (is_array($data)) {
            if (defined('REALM') && REALM == 'admin') {
                if ($side == 'frontEnd' || $side == 'admin') {
                    if (empty($GLOBALS['lang'])) {
                        $lang_values = $this->getLangBySide($side, $langCode, $status);
                        $GLOBALS['lang'] =& $lang_values;
                    } else {
                        $lang_values =& $GLOBALS['lang'];
                    }
                } else {
                    $lang_values = $this->getLangBySide($side, $langCode, $status);
                }
            } else {
                if ($side == 'frontEnd') {
                    if (empty($GLOBALS['lang'])) {
                        $lang_values = $this->getLangBySide($side, $langCode, $status);
                        $GLOBALS['lang'] =& $lang_values;
                    } else {
                        $lang_values =& $GLOBALS['lang'];
                    }
                } else {
                    $lang_values = $this->getLangBySide($side, $langCode, $status);
                }
            }
            foreach ($data as $key => $value) {
                if (!empty($fields)) {
                    if (is_array($fields)) {
                        if (is_array($data[$key])) {
                            foreach ($fields as $field) {
                                if (!empty($lang_values[$module . '+' . $field . '+' . $data[$key]['Key']])) {
                                    $data[$key][$field] = $lang_values[$module . '+' . $field . '+' . $data[$key]['Key']];
                                }
                            }
                        } else {
                            foreach ($fields as $field) {
                                if (!empty($lang_values[$module . '+' . $field . '+' . $data['Key']])) {
                                    $data[$field] = $lang_values[$module . '+' . $field . '+' . $data['Key']];
                                }
                            }
                        }
                    } else {
                        if (is_array($data[$key])) {
                            if (!empty($lang_values[$module . '+' . $fields . '+' . $data[$key]['Key']])) {
                                $data[$key][$fields] = $lang_values[$module . '+' . $fields . '+' . $data[$key]['Key']];
                            }
                        } else {
                            if (!empty($lang_values[$module . '+' . $fields . '+' . $data['Key']])) {
                                $data[$fields] = $lang_values[$module . '+' . $fields . '+' . $data['Key']];
                            }
                        }
                    }
                } else {
                    if (!empty($lang_values[$module . '+' . $data[$key]['Key']])) {
                        $data[$key] = $lang_values[$module . '+' . $data[$key]['Key']];
                    }
                }
            }
        } elseif ($data) {
            $data = $this->fetch(array(
                'Value'
            ), array(
                'Code' => $langCode,
                'Key' => $data
            ), null, 1, 'lang_keys', 'row');
            return $data['Value'];
        }
        return $data;
    }
    function getLangBySide($module = 'frontEnd', $langCode = RL_LANG_CODE, $status = 'active')
    {
        global $config;
        $this->setTable('lang_keys');
        if ($module == 'admin' || $module == 'frontEnd') {
            $options = "WHERE (`Module` = '{$module}' OR `Module` = 'common') ";
        } else {
            $options = "WHERE (`Module` = '{$module}')";
        }
        $options .= $langCode == '*' ? " AND `Status` = '{$status}'" : " AND `Code` = '{$langCode}'";
        $options .= $status == 'all' ? '' : " AND `Status` = '{$status}' ";
        $phrases_tmp = $this->fetch(array(
            'Key',
            'Value'
        ), null, $options);
        foreach ($phrases_tmp as $phrase) {
            if ($phrase['Value'] == strip_tags($phrase['Value'])) {
                $phrase['Value'] = str_replace(array(
                    '"',
                    "'"
                ), array(
                    '&quot;',
                    '&rsquo;'
                ), $phrase['Value']);
            }
            $phrases[$phrase['Key']] = !(bool) preg_match('/^blocks\+name\+/', $phrase['Key']) ? $phrase['Value'] : str_replace(array(
                PHP_EOL
            ), array(
                '<br />'
            ), $phrase['Value']);
        }
        unset($phrases_tmp);
        $config['site_name'] = $phrases['pages+title+home'];
        return $phrases;
    }
    function defineLanguage($language = false)
    {
        global $config, $languages;
        $count       = count($languages);
        $cookie_lang = defined('REALM') ? "rl_lang_" . REALM : "rl_lang_front";
        if ($count > 1) {
            if (isset($language)) {
                $this->rlValid->sql($language);
                setcookie($cookie_lang, $language, time() + ($config['expire_languages'] * 86400), '/');
                if ($languages[$language]) {
                    define('RL_LANG_CODE', $language);
                } else {
                    define('RL_LANG_CODE', $config['lang']);
                }
            } elseif (isset($_COOKIE[$cookie_lang])) {
                $this->rlValid->sql($_COOKIE[$cookie_lang]);
                if ($languages[$_COOKIE[$cookie_lang]]) {
                    define('RL_LANG_CODE', $_COOKIE[$cookie_lang]);
                } else {
                    define('RL_LANG_CODE', $config['lang']);
                }
            } else {
                define('RL_LANG_CODE', $config['lang']);
            }
        } else {
            define('RL_LANG_CODE', $config['lang']);
        }
        define('RL_LANG_DIR', $languages[RL_LANG_CODE]['Direction']);
    }
    function extDefineLanguage()
    {
        global $config;
        $cookie_lang = defined('REALM') ? "rl_lang_" . REALM : "rl_lang_front";
        if (isset($_COOKIE[$cookie_lang])) {
            $this->rlValid->sql($_COOKIE[$cookie_lang]);
            $user_lang = $this->fetch(array(
                'ID',
                'Date_format'
            ), array(
                'Status' => 'active',
                'Code' => $_COOKIE[$cookie_lang]
            ), null, null, 'languages', 'row');
            define('RL_DATE_FORMAT', $user_lang['Date_format']);
            if (!empty($user_lang)) {
                define('RL_LANG_CODE', $_COOKIE[$cookie_lang]);
            } else {
                define('RL_LANG_CODE', $config['lang']);
            }
        } else {
            $user_lang = $this->fetch(array(
                'Date_format'
            ), array(
                'Status' => 'active',
                'Code' => $config['lang']
            ), null, null, 'languages', 'row');
            define('RL_DATE_FORMAT', $user_lang['Date_format']);
            define('RL_LANG_CODE', $config['lang']);
        }
    }
    function getLanguagesList($status = 'active')
    {
        global $config;
        if (empty($GLOBALS['languages']) || $status == 'all') {
            $where   = array(
                'Status' => $status
            );
            $options = null;
            if ($status == 'all') {
                $where   = null;
                $options = "WHERE `Status` <> 'trash'";
            }
            $tmp_languages = $this->fetch(array(
                'Code`, IF(`Code` = "' . $config['lang'] . '", 1, 0) AS `Order',
                'Key',
                'Direction',
                'Date_format',
                'Status'
            ), $where, $options . " ORDER BY `Order` DESC", null, 'languages');
            foreach ($tmp_languages as $key => $value) {
                $languages[$value['Code']]         = $value;
                $languages[$value['Code']]['name'] = $this->replaceLangKeys('languages+name+' . $value['Key'], null, null, $value['Code']);
            }
            unset($tmp_languages);
            $GLOBALS['languages'] = $languages;
        }
        $languages =& $GLOBALS['languages'];
        return $languages;
    }
    function modifyLanguagesList(&$langList)
    {
        global $page_info;
        foreach ($langList as $key => $value) {
            if ($langList[$key]['Code'] == $GLOBALS['config']['lang'] && $page_info['Controller'] != 'home') {
                $langList[$key]['dCode'] = "";
            } else {
                $langList[$key]['dCode'] = $langList[$key]['Code'] . "/";
            }
            if ($langList[$key]['Code'] == RL_LANG_CODE) {
                define('RL_DATE_FORMAT', $langList[$key]['Date_format']);
            }
        }
    }
}