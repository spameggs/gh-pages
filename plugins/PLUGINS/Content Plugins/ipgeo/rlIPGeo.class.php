<?php
class rlIPGeo extends reefless
{
    function import($dump = false)
    {
        $errors_count = 0;
        $ips_dump     = fopen($dump, "r");
        if ($ips_dump) {
            while ($query = fgets($ips_dump, 10240)) {
                $query = trim($query);
                if ($query[0] == '#')
                    continue;
                if ($query[0] == '-')
                    continue;
                if ($query[strlen($query) - 1] == ';') {
                    $query_sql .= $query;
                } else {
                    $query_sql .= $query;
                    continue;
                }
                if (!empty($query_sql)) {
                    $query_sql = str_replace('{db_prefix}', RL_DBPREFIX, $query_sql);
                }
                $res = $this->query($query_sql);
                if (!$res) {
                    $errors_count++;
                }
                unset($query_sql);
            }
            fclose($ips_dump);
        }
        if ($errors_count > 0) {
            $GLOBALS['rlDb']->logger("[IPGeo]: Can't run sql query; {$errors_count} times.");
        }
    }
    function importIPs()
    {
        set_time_limit(0);
        $m_path     = RL_PLUGINS . 'ipgeo' . RL_DS . 'mysql' . RL_DS;
        $dump_parts = $this->scanDir($m_path);
        foreach ($dump_parts as $key => $part) {
            $this->import($m_path . $part);
        }
    }
    function isBot()
    {
        if (empty($_SERVER['HTTP_USER_AGENT']))
            return true;
        $bots = array(
            "google",
            "bot",
            "radian",
            "yahoo",
            "spider",
            "crawl",
            "archiver",
            "curl",
            "yandex",
            "python",
            "nambu",
            "eventbox",
            "twitt",
            "perl",
            "monitor",
            "sphere",
            "PEAR",
            "mechanize",
            "java",
            "wordpress",
            "facebookexternal",
            "^PHP"
        );
        foreach ($bots as $bot) {
            if ((bool) preg_match("/{$bot}/i", $_SERVER['HTTP_USER_AGENT'])) {
                return true;
            }
        }
        return false;
    }
    function getIP()
    {
        $vars = array(
            'REMOTE_ADDR',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED'
        );
        foreach ($vars as $key) {
            if (array_key_exists($key, $_SERVER) === true && !empty($_SERVER[$key])) {
                foreach (explode(',', $_SERVER[$key]) as $IP) {
                    if ((bool) preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $IP)) {
                        return $IP;
                    }
                }
            }
        }
        return false;
    }
    function getDefaultLocation()
    {
        $countries = $this->getCountriesList();
        foreach ($countries as $key => $country) {
            if ($country->Country_code == $GLOBALS['config']['ipgeo_default_country']) {
                return $country;
            }
        }
    }
    function sortByAccuracy($a, $b)
    {
        $a_min = intval($a['To']) - intval($a['From']);
        $b_min = intval($b['To']) - intval($b['From']);
        if ($a_min == $b_min)
            return 0;
        return ($a_min < $b_min) ? -1 : 1;
    }
    function getLocationInfo()
    {
        if (isset($_SESSION['GEOLocationData']) && !empty($_SESSION['GEOLocationData']->Country_code)) {
            return $_SESSION['GEOLocationData'];
        }
        $location = false;
        if (false === $this->isBot() && false !== $IP = $this->getIP()) {
            $long_IP = sprintf("%u", ip2long($IP));
            $sql     = "SELECT `From`, `To`, `Country_code`, `Country_name` FROM `" . RL_DBPREFIX . "ipgeo` ";
            $sql .= "WHERE '{$long_IP}' BETWEEN `From` AND `To`";
            $info = $this->getAll($sql);
            if (!empty($info)) {
                if (count($info) > 1) {
                    uasort($info, array(
                        'rlIPGeo',
                        'sortByAccuracy'
                    ));
                }
                $info                   = current($info);
                $location               = new stdClass;
                $location->Country_code = $info['Country_code'];
                $location->Country_name = $info['Country_name'];
                unset($info);
            } else {
                $location = $this->getDefaultLocation();
            }
        } else {
            $location = $this->getDefaultLocation();
        }
        $_SESSION['GEOLocationData'] = $location;
    }
    function getCountriesList()
    {
        $countries = '[
			{"Country_code":"AF","Country_name":"Afghanistan"},{"Country_code":"AX","Country_name":"Aland Islands"},{"Country_code":"AL","Country_name":"Albania"},
			{"Country_code":"DZ","Country_name":"Algeria"},{"Country_code":"AS","Country_name":"American Samoa"},{"Country_code":"AD","Country_name":"Andorra"},
			{"Country_code":"AO","Country_name":"Angola"},{"Country_code":"AI","Country_name":"Anguilla"},{"Country_code":"AQ","Country_name":"Antarctica"},
			{"Country_code":"AG","Country_name":"Antigua and Barbuda"},{"Country_code":"AR","Country_name":"Argentina"},{"Country_code":"AM","Country_name":"Armenia"},
			{"Country_code":"AW","Country_name":"Aruba"},{"Country_code":"AU","Country_name":"Australia"},{"Country_code":"AT","Country_name":"Austria"},
			{"Country_code":"AZ","Country_name":"Azerbaijan"},{"Country_code":"BS","Country_name":"Bahamas"},{"Country_code":"BH","Country_name":"Bahrain"},
			{"Country_code":"BD","Country_name":"Bangladesh"},{"Country_code":"BB","Country_name":"Barbados"},{"Country_code":"BY","Country_name":"Belarus"},
			{"Country_code":"BE","Country_name":"Belgium"},{"Country_code":"BZ","Country_name":"Belize"},{"Country_code":"BJ","Country_name":"Benin"},
			{"Country_code":"BM","Country_name":"Bermuda"},{"Country_code":"BT","Country_name":"Bhutan"},{"Country_code":"BO","Country_name":"Bolivia"},
			{"Country_code":"BA","Country_name":"Bosnia and Herzegovina"},{"Country_code":"BW","Country_name":"Botswana"},
			{"Country_code":"BV","Country_name":"Bouvet Island"},{"Country_code":"BR","Country_name":"Brazil"},{"Country_code":"IO","Country_name":"British Indian Ocean Territory"},
			{"Country_code":"BN","Country_name":"Brunei Darussalam"},{"Country_code":"BG","Country_name":"Bulgaria"},{"Country_code":"BF","Country_name":"Burkina Faso"},
			{"Country_code":"BI","Country_name":"Burundi"},{"Country_code":"KH","Country_name":"Cambodia"},{"Country_code":"CM","Country_name":"Cameroon"},
			{"Country_code":"CA","Country_name":"Canada"},{"Country_code":"CV","Country_name":"Cape Verde"},{"Country_code":"KY","Country_name":"Cayman Islands"},
			{"Country_code":"CF","Country_name":"Central African Republic"},{"Country_code":"TD","Country_name":"Chad"},{"Country_code":"CL","Country_name":"Chile"},
			{"Country_code":"CN","Country_name":"China"},{"Country_code":"CX","Country_name":"Christmas Island"},{"Country_code":"CC","Country_name":"Cocos (Keeling) Islands"},
			{"Country_code":"CO","Country_name":"Colombia"},{"Country_code":"KM","Country_name":"Comoros"},{"Country_code":"CG","Country_name":"Congo"},
			{"Country_code":"CD","Country_name":"Congo, The Democratic Republic of the"},{"Country_code":"CK","Country_name":"Cook Islands"},
			{"Country_code":"CR","Country_name":"Costa Rica"},{"Country_code":"CI","Country_name":"Cote D\'Ivoire"},{"Country_code":"HR","Country_name":"Croatia"},
			{"Country_code":"CU","Country_name":"Cuba"},{"Country_code":"CY","Country_name":"Cyprus"},{"Country_code":"CZ","Country_name":"Czech Republic"},
			{"Country_code":"DK","Country_name":"Denmark"},{"Country_code":"DJ","Country_name":"Djibouti"},{"Country_code":"DM","Country_name":"Dominica"},
			{"Country_code":"DO","Country_name":"Dominican Republic"},{"Country_code":"TL","Country_name":"East Timor"},{"Country_code":"EC","Country_name":"Ecuador"},
			{"Country_code":"EG","Country_name":"Egypt"},{"Country_code":"SV","Country_name":"El Salvador"},{"Country_code":"GQ","Country_name":"Equatorial Guinea"},
			{"Country_code":"ER","Country_name":"Eritrea"},{"Country_code":"EE","Country_name":"Estonia"},{"Country_code":"ET","Country_name":"Ethiopia"},
			{"Country_code":"FK","Country_name":"Falkland Islands (Malvinas)"},{"Country_code":"FO","Country_name":"Faroe Islands"},{"Country_code":"FJ","Country_name":"Fiji"},
			{"Country_code":"FI","Country_name":"Finland"},{"Country_code":"FR","Country_name":"France"},{"Country_code":"GF","Country_name":"French Guiana"},
			{"Country_code":"PF","Country_name":"French Polynesia"},{"Country_code":"TF","Country_name":"French Southern Territories"},{"Country_code":"GA","Country_name":"Gabon"},
			{"Country_code":"GM","Country_name":"Gambia"},{"Country_code":"GE","Country_name":"Georgia"},{"Country_code":"DE","Country_name":"Germany"},
			{"Country_code":"GH","Country_name":"Ghana"},{"Country_code":"GI","Country_name":"Gibraltar"},{"Country_code":"GR","Country_name":"Greece"},
			{"Country_code":"GL","Country_name":"Greenland"},{"Country_code":"GD","Country_name":"Grenada"},{"Country_code":"GP","Country_name":"Guadeloupe"},
			{"Country_code":"GU","Country_name":"Guam"},{"Country_code":"GT","Country_name":"Guatemala"},{"Country_code":"GG","Country_name":"Guernsey"},
			{"Country_code":"GN","Country_name":"Guinea"},{"Country_code":"GW","Country_name":"Guinea-Bissau"},{"Country_code":"GY","Country_name":"Guyana"},
			{"Country_code":"HT","Country_name":"Haiti"},{"Country_code":"HM","Country_name":"Heard Island and McDonald Islands"},
			{"Country_code":"VA","Country_name":"Holy See (Vatican City State)"},{"Country_code":"HN","Country_name":"Honduras"},{"Country_code":"HK","Country_name":"Hong Kong"},
			{"Country_code":"HU","Country_name":"Hungary"},{"Country_code":"IS","Country_name":"Iceland"},{"Country_code":"IN","Country_name":"India"},
			{"Country_code":"ID","Country_name":"Indonesia"},{"Country_code":"IR","Country_name":"Iran, Islamic Republic of"},{"Country_code":"IQ","Country_name":"Iraq"},
			{"Country_code":"IE","Country_name":"Ireland"},{"Country_code":"IM","Country_name":"Isle of Man"},{"Country_code":"IL","Country_name":"Israel"},
			{"Country_code":"IT","Country_name":"Italy"},{"Country_code":"JM","Country_name":"Jamaica"},{"Country_code":"JP","Country_name":"Japan"},
			{"Country_code":"JE","Country_name":"Jersey"},{"Country_code":"JO","Country_name":"Jordan"},{"Country_code":"KZ","Country_name":"Kazakhstan"},
			{"Country_code":"KE","Country_name":"Kenya"},{"Country_code":"KI","Country_name":"Kiribati"},{"Country_code":"KP","Country_name":"Korea, Democratic People\'s Republic of"},
			{"Country_code":"KR","Country_name":"Korea, Republic of"},{"Country_code":"KW","Country_name":"Kuwait"},{"Country_code":"KG","Country_name":"Kyrgyzstan"},
			{"Country_code":"LA","Country_name":"Lao People\'s Democratic Republic"},{"Country_code":"LV","Country_name":"Latvia"},{"Country_code":"LB","Country_name":"Lebanon"},
			{"Country_code":"LS","Country_name":"Lesotho"},{"Country_code":"LR","Country_name":"Liberia"},{"Country_code":"LY","Country_name":"Libyan Arab Jamahiriya"},
			{"Country_code":"LI","Country_name":"Liechtenstein"},{"Country_code":"LT","Country_name":"Lithuania"},{"Country_code":"LU","Country_name":"Luxembourg"},
			{"Country_code":"MO","Country_name":"Macau"},{"Country_code":"MK","Country_name":"Macedonia"},{"Country_code":"MG","Country_name":"Madagascar"},
			{"Country_code":"MW","Country_name":"Malawi"},{"Country_code":"MY","Country_name":"Malaysia"},{"Country_code":"MV","Country_name":"Maldives"},
			{"Country_code":"ML","Country_name":"Mali"},{"Country_code":"MT","Country_name":"Malta"},{"Country_code":"MH","Country_name":"Marshall Islands"},
			{"Country_code":"MQ","Country_name":"Martinique"},{"Country_code":"MR","Country_name":"Mauritania"},{"Country_code":"MU","Country_name":"Mauritius"},
			{"Country_code":"YT","Country_name":"Mayotte"},{"Country_code":"MX","Country_name":"Mexico"},{"Country_code":"FM","Country_name":"Micronesia, Federated States of"},
			{"Country_code":"MD","Country_name":"Moldova, Republic of"},{"Country_code":"MC","Country_name":"Monaco"},{"Country_code":"MN","Country_name":"Mongolia"},
			{"Country_code":"ME","Country_name":"Montenegro"},{"Country_code":"MS","Country_name":"Montserrat"},{"Country_code":"MA","Country_name":"Morocco"},
			{"Country_code":"MZ","Country_name":"Mozambique"},{"Country_code":"MM","Country_name":"Myanmar"},{"Country_code":"NA","Country_name":"Namibia"},
			{"Country_code":"NR","Country_name":"Nauru"},{"Country_code":"NP","Country_name":"Nepal"},{"Country_code":"NL","Country_name":"Netherlands"},
			{"Country_code":"AN","Country_name":"Netherlands Antilles"},{"Country_code":"NC","Country_name":"New Caledonia"},{"Country_code":"NZ","Country_name":"New Zealand"},
			{"Country_code":"NI","Country_name":"Nicaragua"},{"Country_code":"NE","Country_name":"Niger"},{"Country_code":"NG","Country_name":"Nigeria"},
			{"Country_code":"NU","Country_name":"Niue"},{"Country_code":"NF","Country_name":"Norfolk Island"},{"Country_code":"MP","Country_name":"Northern Mariana Islands"},
			{"Country_code":"NO","Country_name":"Norway"},{"Country_code":"OM","Country_name":"Oman"},{"Country_code":"PK","Country_name":"Pakistan"},
			{"Country_code":"PW","Country_name":"Palau"},{"Country_code":"PS","Country_name":"Palestinian Territory"},{"Country_code":"PA","Country_name":"Panama"},
			{"Country_code":"PG","Country_name":"Papua New Guinea"},{"Country_code":"PY","Country_name":"Paraguay"},{"Country_code":"PE","Country_name":"Peru"},
			{"Country_code":"PH","Country_name":"Philippines"},{"Country_code":"PN","Country_name":"Pitcairn"},{"Country_code":"PL","Country_name":"Poland"},
			{"Country_code":"PT","Country_name":"Portugal"},{"Country_code":"PR","Country_name":"Puerto Rico"},{"Country_code":"QA","Country_name":"Qatar"},
			{"Country_code":"RE","Country_name":"Reunion"},{"Country_code":"RO","Country_name":"Romania"},{"Country_code":"RU","Country_name":"Russian Federation"},
			{"Country_code":"RW","Country_name":"Rwanda"},{"Country_code":"SH","Country_name":"Saint Helena"},{"Country_code":"KN","Country_name":"Saint Kitts and Nevis"},
			{"Country_code":"LC","Country_name":"Saint Lucia"},{"Country_code":"PM","Country_name":"Saint Pierre and Miquelon"},
			{"Country_code":"VC","Country_name":"Saint Vincent and the Grenadines"},{"Country_code":"WS","Country_name":"Samoa"},{"Country_code":"SM","Country_name":"San Marino"},
			{"Country_code":"ST","Country_name":"Sao Tome and Principe"},{"Country_code":"SA","Country_name":"Saudi Arabia"},{"Country_code":"SN","Country_name":"Senegal"},
			{"Country_code":"RS","Country_name":"Serbia"},{"Country_code":"SC","Country_name":"Seychelles"},{"Country_code":"SL","Country_name":"Sierra Leone"},
			{"Country_code":"SG","Country_name":"Singapore"},{"Country_code":"SK","Country_name":"Slovakia"},{"Country_code":"SI","Country_name":"Slovenia"},
			{"Country_code":"SB","Country_name":"Solomon Islands"},{"Country_code":"SO","Country_name":"Somalia"},{"Country_code":"ZA","Country_name":"South Africa"},
			{"Country_code":"GS","Country_name":"South Georgia and the South Sandwich Islands"},{"Country_code":"ES","Country_name":"Spain"},
			{"Country_code":"LK","Country_name":"Sri Lanka"},{"Country_code":"SD","Country_name":"Sudan"},{"Country_code":"SR","Country_name":"Suriname"},
			{"Country_code":"SJ","Country_name":"Svalbard and Jan Mayen"},{"Country_code":"SZ","Country_name":"Swaziland"},{"Country_code":"SE","Country_name":"Sweden"},
			{"Country_code":"CH","Country_name":"Switzerland"},{"Country_code":"SY","Country_name":"Syrian Arab Republic"},
			{"Country_code":"TW","Country_name":"Taiwan (Province of China)"},{"Country_code":"TJ","Country_name":"Tajikistan"},{"Country_code":"TZ","Country_name":"Tanzania, United Republic of"},
			{"Country_code":"TH","Country_name":"Thailand"},{"Country_code":"TG","Country_name":"Togo"},{"Country_code":"TK","Country_name":"Tokelau"},{"Country_code":"TO","Country_name":"Tonga"},
			{"Country_code":"TT","Country_name":"Trinidad and Tobago"},{"Country_code":"TN","Country_name":"Tunisia"},{"Country_code":"TR","Country_name":"Turkey"},
			{"Country_code":"TM","Country_name":"Turkmenistan"},{"Country_code":"TC","Country_name":"Turks and Caicos Islands"},{"Country_code":"TV","Country_name":"Tuvalu"},
			{"Country_code":"UG","Country_name":"Uganda"},{"Country_code":"UA","Country_name":"Ukraine"},{"Country_code":"AE","Country_name":"United Arab Emirates"},
			{"Country_code":"GB","Country_name":"United Kingdom"},{"Country_code":"US","Country_name":"United States"},{"Country_code":"UM","Country_name":"United States Minor Outlying Islands"},
			{"Country_code":"UY","Country_name":"Uruguay"},{"Country_code":"UZ","Country_name":"Uzbekistan"},{"Country_code":"VU","Country_name":"Vanuatu"},
			{"Country_code":"VE","Country_name":"Venezuela"},{"Country_code":"VN","Country_name":"Vietnam"},{"Country_code":"VG","Country_name":"Virgin Islands, British"},
			{"Country_code":"VI","Country_name":"Virgin Islands, U.S."},{"Country_code":"WF","Country_name":"Wallis and Futuna"},{"Country_code":"EH","Country_name":"Western Sahara"},
			{"Country_code":"YE","Country_name":"Yemen"},{"Country_code":"ZM","Country_name":"Zambia"},{"Country_code":"ZW","Country_name":"Zimbabwe"}
		]';
        $countries = preg_replace('/(\n|\t|\r)?/', '', $countries);
        $this->loadClass('Json');
        return $GLOBALS['rlJson']->decode($countries);
    }
}