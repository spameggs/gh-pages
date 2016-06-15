<?php
class rlCurrencyConverter extends reefless
{
    var $cc = array();
    var $systemCurrency = array('USD' => 'dollar', 'GBP' => 'ps', 'EUR' => 'euro');
    var $specialBlock = '
		global $block_keys, $rlXajax, $rlSmarty;

		$GLOBALS["reefless"] -> loadClass( "CurrencyConverter", null, "currencyConverter" );
		
		$rates = array({rates_items});
    	$rlSmarty -> assign_by_ref("curConv_rates", $rates);
    	$GLOBALS["rlCurrencyConverter"] -> rates = $rates;
		
		$GLOBALS["rlCurrencyConverter"] -> detectCurrency();
		
		if ( array_key_exists( "currencyConvertor_block", $block_keys) )
		{
			$rlXajax -> registerFunction( array( "setCurrency", $GLOBALS["rlCurrencyConverter"], "ajaxSetCurrency" ) );
		}
	';
    var $rates = false;
    function rlCurrencyConverter()
    {
        global $rlSmarty;
        $this->setCC();
        if (is_object('rlSmarty')) {
            $rlSmarty->assign_by_ref('curConv_mapping', $this->systemCurrency);
            $rlSmarty->assign_by_ref('curConv_abbr', $this->cc);
            $rlSmarty->register_modifier('flHtmlEntriesDecode', 'flHtmlEntriesDecode');
        }
    }
    function ajaxUpdateRate()
    {
        global $_response, $lang, $rlActions, $rlNotice, $config;
        $content = $this->getPageContent($config['currencyConverter_rss']);
        $this->loadClass('Rss');
        $GLOBALS['rlRss']->items_number = 300;
        $GLOBALS['rlRss']->createParser($content);
        $rates = $GLOBALS['rlRss']->getRssContent();
        if (empty($rates)) {
            $_response->script("printMessage('error', '{$lang['currencyConverter_update_rss_fail']}');");
        } else {
            foreach ($rates as $rate) {
                preg_match('/(.*)\/.*/', $rate['title'], $code_matches);
                $code = $code_matches[1];
                preg_match('/.*\=\s([0-9\.\,]*)\s(.*)/', $rate['description'], $matches);
                $rate    = str_replace(',', '', $matches[1]);
                $country = $matches[2];
                if ($this->getOne('ID', "`Code` = '{$code}'", 'currency_rate')) {
                    $this->query("UPDATE `" . RL_DBPREFIX . "currency_rate` SET `Rate` = '{$rate}', `Date` = NOW() WHERE `Code` = '{$code}' LIMIT 1");
                } else {
                    $this->query("INSERT INTO `" . RL_DBPREFIX . "currency_rate` (`Rate`, `Key`, `Country`, `Date`, `Code`) VALUES ('{$rate}', '{$code}', '{$country}', NOW(), '{$code}') ");
                }
            }
            $_response->script("printMessage('notice', '{$lang['currencyConverter_rates_updated']}');");
        }
        $this->updateHook();
        $_response->script("currencyGrid.reload();");
        return $_response;
    }
    function ajaxAddCurrency($code = false, $rate = false, $name = false, $status = 'active')
    {
        global $_response, $lang, $rlActions, $rlNotice, $config;
        if ($exist = $this->getOne('ID', "`Code` = '{$code}'", 'currency_rate')) {
            $errors[] = str_replace('{code}', $code, $lang['currencyConverter_code_exists']);
        }
        preg_match('/([A-Z]{3})/', $code, $matches);
        if (!$matches[1]) {
            $errors[] = $lang['currencyConverter_code_wrong'];
        }
        preg_match('/^([0-9\.]+)$/', $rate, $matches_rate);
        if (!$matches_rate[1]) {
            $errors[] = $lang['currencyConverter_rate_wrong'];
        }
        if (!empty($errors)) {
            $out = '<ul>';
            foreach ($errors as $error) {
                $out .= '<li>' . $error . '</li>';
            }
            $out .= '</ul>';
            $_response->script("printMessage('error', '{$out}');");
        } else {
            $insert = array(
                'Code' => $code,
                'Rate' => $rate,
                'Key' => $code,
                'Country' => $name,
                'Date' => 'NOW()',
                'Status' => $status
            );
            $rlActions->insertOne($insert, 'currency_rate');
            $this->updateHook();
            $_response->script("
				currencyGrid.reload();
				printNotice('notice', '{$lang['currencyConverter_added_notice']}');
				$('#new_item').slideUp('normal');
			");
        }
        $_response->script("$('input[name=add_new_currency_submit]').val('{$lang['add']}');");
        return $_response;
    }
    function insertRate($url = false)
    {
        $content = $this->getPageContent($url ? $url : $config['currencyConverter_rss']);
        $this->loadClass('Rss');
        $GLOBALS['rlRss']->items_number = 300;
        $GLOBALS['rlRss']->createParser($content);
        $rates = $GLOBALS['rlRss']->getRssContent();
        if (!empty($rates)) {
            $this->query("INSERT INTO `" . RL_DBPREFIX . "currency_rate` (`Rate`, `Key`, `Country`, `Date`, `Code`, `Symbol`) VALUES ('1', 'dollar', 'United States', NOW(), 'USD', '$') ");
            foreach ($rates as $rate) {
                preg_match('/(.*)\/.*/', $rate['title'], $code_matches);
                $code = $code_matches[1];
                preg_match('/.*\=\s([0-9\.]*)\s(.*)/', $rate['description'], $matches);
                $rate    = $matches[1];
                $country = $matches[2];
                switch ($code) {
                    case 'EUR':
                        $symbol = '&euro;';
                        $key    = 'euro';
                        break;
                    case 'GBP':
                        $symbol = '&pound;';
                        $key    = 'ps';
                        break;
                    default:
                        $symbol = '';
                        $key    = $code;
                        break;
                }
                $this->query("INSERT INTO `" . RL_DBPREFIX . "currency_rate` (`Rate`, `Key`, `Country`, `Date`, `Code`, `Symbol`) VALUES ('{$rate}', '{$key}', '{$country}', NOW(), '{$code}', '{$symbol}') ");
            }
        }
        $this->updateHook();
    }
    function updateRate($url = false)
    {
        global $config;
        $content = $this->getPageContent($config['currencyConverter_rss']);
        $this->loadClass('Rss');
        $GLOBALS['rlRss']->items_number = 300;
        $GLOBALS['rlRss']->createParser($content);
        $rates = $GLOBALS['rlRss']->getRssContent();
        if (!empty($rates)) {
            foreach ($rates as $rate) {
                preg_match('/(.*)\/.*/', $rate['title'], $code_matches);
                $code = $code_matches[1];
                preg_match('/.*\=\s([0-9\.]*)\s(.*)/', $rate['description'], $matches);
                $rate    = $matches[1];
                $country = $matches[2];
                if ($this->getOne('ID', "`Code` = '{$code}'", 'currency_rate')) {
                    $this->query("UPDATE `" . RL_DBPREFIX . "currency_rate` SET `Rate` = '{$rate}', `Date` = NOW() WHERE `Code` = '{$code}' LIMIT 1");
                } else {
                    $this->query("INSERT INTO `" . RL_DBPREFIX . "currency_rate` (`Rate`, `Key`, `Country`, `Date`, `Code`) VALUES ('{$rate}', '{$code}', '{$country}', NOW(), '{$code}') ");
                }
            }
        }
        $this->updateHook();
    }
    function updateHook()
    {
        global $rlActions;
        $this->setTable('currency_rate');
        $rates = $this->fetch(array(
            'Code',
            'Key',
            'Rate',
            'Country',
            'Symbol'
        ), array(
            'Status' => 'active'
        ));
        if (!$rates)
            return false;
        foreach ($rates as $rate) {
            $items .= "'{$rate['Key']}' => array(
						'Rate' => '{$rate['Rate']}',
						'Code' => '{$rate['Code']}',
						'Symbol' => '{$rate['Symbol']}',
						'Country' => '{$rate['Country']}'
					),";
        }
        $update['fields']['Code'] = str_replace('{rates_items}', rtrim($items, ','), $this->specialBlock);
        $update['where']          = array(
            'Plugin' => 'currencyConverter',
            'Name' => 'specialBlock'
        );
        $rlActions->rlAllowHTML   = true;
        $rlActions->updateOne($update, 'hooks');
        $rlActions->rlAllowHTML = false;
    }
    function ajaxSetCurrency($key = false)
    {
        global $_response, $lang, $config, $_POST;
        if ($this->rates[$key]) {
            $_SESSION['curConv_code'] = $key;
            setcookie('curConv_code', $key, time() + 2678400, '/');
            $_response->script("$('#curConv_1 b').html('" . strtoupper($this->rates[$key]['Code']) . "');");
            $_response->script("$('#curConv_2').hide();$('#curConv_1').show();");
        }
        $_response->script("$('#curConv_loading').hide();");
        $_response->script("currencyConverter.config['currency'] = '" . $key . "'; currencyConverter.convert();");
        return $_response;
    }
    function detectCurrency()
    {
        global $rlSmarty;
        $curConvCountry = array(
            'Code' => $_SESSION['GEOLocationData']->Country_code,
            'Name' => $_SESSION['GEOLocationData']->Country_name
        );
        if ($_SESSION['curConv_code'] || $_COOKIE['curConv_code']) {
            $curConvCountry['Currency'] = $_SESSION['curConv_code'] ? $_SESSION['curConv_code'] : $_COOKIE['curConv_code'];
        } else {
            if ($this->rates[$this->cc[$curConvCountry['Code']]]) {
                $curConvCountry['Currency'] = $this->cc[$curConvCountry['Code']];
            } elseif ($this->cc[$curConvCountry['Code']] == 'USD') {
                $curConvCountry['Currency'] = 'dollar';
            } elseif ($this->cc[$curConvCountry['Code']] == 'EUR') {
                $curConvCountry['Currency'] = 'euro';
            } elseif ($this->cc[$curConvCountry['Code']] == 'GBP' && $this->rates['ps']) {
                $curConvCountry['Currency'] = 'ps';
            }
        }
        $rlSmarty->assign_by_ref('curConv_country', $curConvCountry);
    }
    function setCC()
    {
        $this->cc = array(
            'NZ' => 'NZD',
            'CK' => 'NZD',
            'NU' => 'NZD',
            'PN' => 'NZD',
            'TK' => 'NZD',
            'AU' => 'AUD',
            'CX' => 'AUD',
            'CC' => 'AUD',
            'HM' => 'AUD',
            'KI' => 'AUD',
            'NR' => 'AUD',
            'NF' => 'AUD',
            'TV' => 'AUD',
            'AS' => 'EUR',
            'AD' => 'EUR',
            'AT' => 'EUR',
            'BE' => 'EUR',
            'FI' => 'EUR',
            'FR' => 'EUR',
            'GF' => 'EUR',
            'TF' => 'EUR',
            'DE' => 'EUR',
            'GR' => 'EUR',
            'GP' => 'EUR',
            'IE' => 'EUR',
            'IT' => 'EUR',
            'LU' => 'EUR',
            'MQ' => 'EUR',
            'YT' => 'EUR',
            'MC' => 'EUR',
            'NL' => 'EUR',
            'PT' => 'EUR',
            'RE' => 'EUR',
            'WS' => 'EUR',
            'SM' => 'EUR',
            'SI' => 'EUR',
            'ES' => 'EUR',
            'VA' => 'EUR',
            'GS' => 'GBP',
            'GB' => 'GBP',
            'JE' => 'GBP',
            'IO' => 'USD',
            'GU' => 'USD',
            'MH' => 'USD',
            'FM' => 'USD',
            'MP' => 'USD',
            'PW' => 'USD',
            'PR' => 'USD',
            'TC' => 'USD',
            'US' => 'USD',
            'UM' => 'USD',
            'VG' => 'USD',
            'VI' => 'USD',
            'HK' => 'HKD',
            'CA' => 'CAD',
            'JP' => 'JPY',
            'AF' => 'AFN',
            'AL' => 'ALL',
            'DZ' => 'DZD',
            'AI' => 'XCD',
            'AG' => 'XCD',
            'DM' => 'XCD',
            'GD' => 'XCD',
            'MS' => 'XCD',
            'KN' => 'XCD',
            'LC' => 'XCD',
            'VC' => 'XCD',
            'AR' => 'ARS',
            'AM' => 'AMD',
            'AW' => 'ANG',
            'AN' => 'ANG',
            'AZ' => 'AZN',
            'BS' => 'BSD',
            'BH' => 'BHD',
            'BD' => 'BDT',
            'BB' => 'BBD',
            'BY' => 'BYR',
            'BZ' => 'BZD',
            'BJ' => 'XOF',
            'BF' => 'XOF',
            'GW' => 'XOF',
            'CI' => 'XOF',
            'ML' => 'XOF',
            'NE' => 'XOF',
            'SN' => 'XOF',
            'TG' => 'XOF',
            'BM' => 'BMD',
            'BT' => 'INR',
            'IN' => 'INR',
            'BO' => 'BOB',
            'BW' => 'BWP',
            'BV' => 'NOK',
            'NO' => 'NOK',
            'SJ' => 'NOK',
            'BR' => 'BRL',
            'BN' => 'BND',
            'BG' => 'BGN',
            'BI' => 'BIF',
            'KH' => 'KHR',
            'CM' => 'XAF',
            'CF' => 'XAF',
            'TD' => 'XAF',
            'CG' => 'XAF',
            'GQ' => 'XAF',
            'GA' => 'XAF',
            'CV' => 'CVE',
            'KY' => 'KYD',
            'CL' => 'CLP',
            'CN' => 'CNY',
            'CO' => 'COP',
            'KM' => 'KMF',
            'CD' => 'CDF',
            'CR' => 'CRC',
            'HR' => 'HRK',
            'CU' => 'CUP',
            'CY' => 'CYP',
            'CZ' => 'CZK',
            'DK' => 'DKK',
            'FO' => 'DKK',
            'GL' => 'DKK',
            'DJ' => 'DJF',
            'DO' => 'DOP',
            'TP' => 'IDR',
            'ID' => 'IDR',
            'EC' => 'ECS',
            'EG' => 'EGP',
            'SV' => 'SVC',
            'ER' => 'ETB',
            'ET' => 'ETB',
            'EE' => 'EEK',
            'FK' => 'FKP',
            'FJ' => 'FJD',
            'PF' => 'XPF',
            'NC' => 'XPF',
            'WF' => 'XPF',
            'GM' => 'GMD',
            'GE' => 'GEL',
            'GI' => 'GIP',
            'GT' => 'GTQ',
            'GN' => 'GNF',
            'GY' => 'GYD',
            'HT' => 'HTG',
            'HN' => 'HNL',
            'HU' => 'HUF',
            'IS' => 'ISK',
            'IR' => 'IRR',
            'IQ' => 'IQD',
            'IL' => 'ILS',
            'JM' => 'JMD',
            'JO' => 'JOD',
            'KZ' => 'KZT',
            'KE' => 'KES',
            'KP' => 'KPW',
            'KR' => 'KRW',
            'KW' => 'KWD',
            'KG' => 'KGS',
            'LA' => 'LAK',
            'LV' => 'LVL',
            'LB' => 'LBP',
            'LS' => 'LSL',
            'LR' => 'LRD',
            'LY' => 'LYD',
            'LI' => 'CHF',
            'CH' => 'CHF',
            'LT' => 'LTL',
            'MO' => 'MOP',
            'MK' => 'MKD',
            'MG' => 'MGA',
            'MW' => 'MWK',
            'MY' => 'MYR',
            'MV' => 'MVR',
            'MT' => 'MTL',
            'MR' => 'MRO',
            'MU' => 'MUR',
            'MX' => 'MXN',
            'MD' => 'MDL',
            'MN' => 'MNT',
            'MA' => 'MAD',
            'EH' => 'MAD',
            'MZ' => 'MZN',
            'MM' => 'MMK',
            'NA' => 'NAD',
            'NP' => 'NPR',
            'NI' => 'NIO',
            'NG' => 'NGN',
            'OM' => 'OMR',
            'PK' => 'PKR',
            'PA' => 'PAB',
            'PG' => 'PGK',
            'PY' => 'PYG',
            'PE' => 'PEN',
            'PH' => 'PHP',
            'PL' => 'PLN',
            'QA' => 'QAR',
            'RO' => 'RON',
            'RU' => 'RUB',
            'RW' => 'RWF',
            'ST' => 'STD',
            'SA' => 'SAR',
            'SC' => 'SCR',
            'SL' => 'SLL',
            'SG' => 'SGD',
            'SK' => 'SKK',
            'SB' => 'SBD',
            'SO' => 'SOS',
            'ZA' => 'ZAR',
            'LK' => 'LKR',
            'SD' => 'SDG',
            'SR' => 'SRD',
            'SZ' => 'SZL',
            'SE' => 'SEK',
            'SY' => 'SYP',
            'TW' => 'TWD',
            'TJ' => 'TJS',
            'TZ' => 'TZS',
            'TH' => 'THB',
            'TO' => 'TOP',
            'TT' => 'TTD',
            'TN' => 'TND',
            'TR' => 'TRY',
            'TM' => 'TMT',
            'UG' => 'UGX',
            'UA' => 'UAH',
            'AE' => 'AED',
            'UY' => 'UYU',
            'UZ' => 'UZS',
            'VU' => 'VUV',
            'VE' => 'VEF',
            'VN' => 'VND',
            'YE' => 'YER',
            'ZM' => 'ZMK',
            'ZW' => 'ZWD'
        );
    }
}
function flHtmlEntriesDecode($string = false)
{
    return html_entity_decode($string, null, 'utf-8');
}