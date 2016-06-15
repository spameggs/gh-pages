CREATE TABLE IF NOT EXISTS {db_prefix}iso_countries (
  iso CHAR(2) NOT NULL PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  printable_name VARCHAR(80) NOT NULL,
  iso3 CHAR(3),
  numcode SMALLINT
);

INSERT INTO {db_prefix}iso_countries VALUES ('AF','AFGHANISTAN','Afghanistan','AFG','004');
INSERT INTO {db_prefix}iso_countries VALUES ('AL','ALBANIA','Albania','ALB','008');
INSERT INTO {db_prefix}iso_countries VALUES ('DZ','ALGERIA','Algeria','DZA','012');
INSERT INTO {db_prefix}iso_countries VALUES ('AS','AMERICAN SAMOA','American Samoa','ASM','016');
INSERT INTO {db_prefix}iso_countries VALUES ('AD','ANDORRA','Andorra','AND','020');
INSERT INTO {db_prefix}iso_countries VALUES ('AO','ANGOLA','Angola','AGO','024');
INSERT INTO {db_prefix}iso_countries VALUES ('AI','ANGUILLA','Anguilla','AIA','660');
INSERT INTO {db_prefix}iso_countries VALUES ('AQ','ANTARCTICA','Antarctica',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('AG','ANTIGUA AND BARBUDA','Antigua and Barbuda','ATG','028');
INSERT INTO {db_prefix}iso_countries VALUES ('AR','ARGENTINA','Argentina','ARG','032');
INSERT INTO {db_prefix}iso_countries VALUES ('AM','ARMENIA','Armenia','ARM','051');
INSERT INTO {db_prefix}iso_countries VALUES ('AW','ARUBA','Aruba','ABW','533');
INSERT INTO {db_prefix}iso_countries VALUES ('AU','AUSTRALIA','Australia','AUS','036');
INSERT INTO {db_prefix}iso_countries VALUES ('AT','AUSTRIA','Austria','AUT','040');
INSERT INTO {db_prefix}iso_countries VALUES ('AZ','AZERBAIJAN','Azerbaijan','AZE','031');
INSERT INTO {db_prefix}iso_countries VALUES ('BS','BAHAMAS','Bahamas','BHS','044');
INSERT INTO {db_prefix}iso_countries VALUES ('BH','BAHRAIN','Bahrain','BHR','048');
INSERT INTO {db_prefix}iso_countries VALUES ('BD','BANGLADESH','Bangladesh','BGD','050');
INSERT INTO {db_prefix}iso_countries VALUES ('BB','BARBADOS','Barbados','BRB','052');
INSERT INTO {db_prefix}iso_countries VALUES ('BY','BELARUS','Belarus','BLR','112');
INSERT INTO {db_prefix}iso_countries VALUES ('BE','BELGIUM','Belgium','BEL','056');
INSERT INTO {db_prefix}iso_countries VALUES ('BZ','BELIZE','Belize','BLZ','084');
INSERT INTO {db_prefix}iso_countries VALUES ('BJ','BENIN','Benin','BEN','204');
INSERT INTO {db_prefix}iso_countries VALUES ('BM','BERMUDA','Bermuda','BMU','060');
INSERT INTO {db_prefix}iso_countries VALUES ('BT','BHUTAN','Bhutan','BTN','064');
INSERT INTO {db_prefix}iso_countries VALUES ('BO','BOLIVIA','Bolivia','BOL','068');
INSERT INTO {db_prefix}iso_countries VALUES ('BA','BOSNIA AND HERZEGOVINA','Bosnia and Herzegovina','BIH','070');
INSERT INTO {db_prefix}iso_countries VALUES ('BW','BOTSWANA','Botswana','BWA','072');
INSERT INTO {db_prefix}iso_countries VALUES ('BV','BOUVET ISLAND','Bouvet Island',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('BR','BRAZIL','Brazil','BRA','076');
INSERT INTO {db_prefix}iso_countries VALUES ('IO','BRITISH INDIAN OCEAN TERRITORY','British Indian Ocean Territory',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('BN','BRUNEI DARUSSALAM','Brunei Darussalam','BRN','096');
INSERT INTO {db_prefix}iso_countries VALUES ('BG','BULGARIA','Bulgaria','BGR','100');
INSERT INTO {db_prefix}iso_countries VALUES ('BF','BURKINA FASO','Burkina Faso','BFA','854');
INSERT INTO {db_prefix}iso_countries VALUES ('BI','BURUNDI','Burundi','BDI','108');
INSERT INTO {db_prefix}iso_countries VALUES ('KH','CAMBODIA','Cambodia','KHM','116');
INSERT INTO {db_prefix}iso_countries VALUES ('CM','CAMEROON','Cameroon','CMR','120');
INSERT INTO {db_prefix}iso_countries VALUES ('CA','CANADA','Canada','CAN','124');
INSERT INTO {db_prefix}iso_countries VALUES ('CV','CAPE VERDE','Cape Verde','CPV','132');
INSERT INTO {db_prefix}iso_countries VALUES ('KY','CAYMAN ISLANDS','Cayman Islands','CYM','136');
INSERT INTO {db_prefix}iso_countries VALUES ('CF','CENTRAL AFRICAN REPUBLIC','Central African Republic','CAF','140');
INSERT INTO {db_prefix}iso_countries VALUES ('TD','CHAD','Chad','TCD','148');
INSERT INTO {db_prefix}iso_countries VALUES ('CL','CHILE','Chile','CHL','152');
INSERT INTO {db_prefix}iso_countries VALUES ('CN','CHINA','China','CHN','156');
INSERT INTO {db_prefix}iso_countries VALUES ('CX','CHRISTMAS ISLAND','Christmas Island',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('CC','COCOS (KEELING) ISLANDS','Cocos (Keeling) Islands',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('CO','COLOMBIA','Colombia','COL','170');
INSERT INTO {db_prefix}iso_countries VALUES ('KM','COMOROS','Comoros','COM','174');
INSERT INTO {db_prefix}iso_countries VALUES ('CG','CONGO','Congo','COG','178');
INSERT INTO {db_prefix}iso_countries VALUES ('CD','CONGO, THE DEMOCRATIC REPUBLIC OF THE','Congo, the Democratic Republic of the','COD','180');
INSERT INTO {db_prefix}iso_countries VALUES ('CK','COOK ISLANDS','Cook Islands','COK','184');
INSERT INTO {db_prefix}iso_countries VALUES ('CR','COSTA RICA','Costa Rica','CRI','188');
INSERT INTO {db_prefix}iso_countries VALUES ('CI','COTE D\'IVOIRE','Cote D\'Ivoire','CIV','384');
INSERT INTO {db_prefix}iso_countries VALUES ('HR','CROATIA','Croatia','HRV','191');
INSERT INTO {db_prefix}iso_countries VALUES ('CU','CUBA','Cuba','CUB','192');
INSERT INTO {db_prefix}iso_countries VALUES ('CY','CYPRUS','Cyprus','CYP','196');
INSERT INTO {db_prefix}iso_countries VALUES ('CZ','CZECH REPUBLIC','Czech Republic','CZE','203');
INSERT INTO {db_prefix}iso_countries VALUES ('DK','DENMARK','Denmark','DNK','208');
INSERT INTO {db_prefix}iso_countries VALUES ('DJ','DJIBOUTI','Djibouti','DJI','262');
INSERT INTO {db_prefix}iso_countries VALUES ('DM','DOMINICA','Dominica','DMA','212');
INSERT INTO {db_prefix}iso_countries VALUES ('DO','DOMINICAN REPUBLIC','Dominican Republic','DOM','214');
INSERT INTO {db_prefix}iso_countries VALUES ('EC','ECUADOR','Ecuador','ECU','218');
INSERT INTO {db_prefix}iso_countries VALUES ('EG','EGYPT','Egypt','EGY','818');
INSERT INTO {db_prefix}iso_countries VALUES ('SV','EL SALVADOR','El Salvador','SLV','222');
INSERT INTO {db_prefix}iso_countries VALUES ('GQ','EQUATORIAL GUINEA','Equatorial Guinea','GNQ','226');
INSERT INTO {db_prefix}iso_countries VALUES ('ER','ERITREA','Eritrea','ERI','232');
INSERT INTO {db_prefix}iso_countries VALUES ('EE','ESTONIA','Estonia','EST','233');
INSERT INTO {db_prefix}iso_countries VALUES ('ET','ETHIOPIA','Ethiopia','ETH','231');
INSERT INTO {db_prefix}iso_countries VALUES ('FK','FALKLAND ISLANDS (MALVINAS)','Falkland Islands (Malvinas)','FLK','238');
INSERT INTO {db_prefix}iso_countries VALUES ('FO','FAROE ISLANDS','Faroe Islands','FRO','234');
INSERT INTO {db_prefix}iso_countries VALUES ('FJ','FIJI','Fiji','FJI','242');
INSERT INTO {db_prefix}iso_countries VALUES ('FI','FINLAND','Finland','FIN','246');
INSERT INTO {db_prefix}iso_countries VALUES ('FR','FRANCE','France','FRA','250');
INSERT INTO {db_prefix}iso_countries VALUES ('GF','FRENCH GUIANA','French Guiana','GUF','254');
INSERT INTO {db_prefix}iso_countries VALUES ('PF','FRENCH POLYNESIA','French Polynesia','PYF','258');
INSERT INTO {db_prefix}iso_countries VALUES ('TF','FRENCH SOUTHERN TERRITORIES','French Southern Territories',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('GA','GABON','Gabon','GAB','266');
INSERT INTO {db_prefix}iso_countries VALUES ('GM','GAMBIA','Gambia','GMB','270');
INSERT INTO {db_prefix}iso_countries VALUES ('GE','GEORGIA','Georgia','GEO','268');
INSERT INTO {db_prefix}iso_countries VALUES ('DE','GERMANY','Germany','DEU','276');
INSERT INTO {db_prefix}iso_countries VALUES ('GH','GHANA','Ghana','GHA','288');
INSERT INTO {db_prefix}iso_countries VALUES ('GI','GIBRALTAR','Gibraltar','GIB','292');
INSERT INTO {db_prefix}iso_countries VALUES ('GR','GREECE','Greece','GRC','300');
INSERT INTO {db_prefix}iso_countries VALUES ('GL','GREENLAND','Greenland','GRL','304');
INSERT INTO {db_prefix}iso_countries VALUES ('GD','GRENADA','Grenada','GRD','308');
INSERT INTO {db_prefix}iso_countries VALUES ('GP','GUADELOUPE','Guadeloupe','GLP','312');
INSERT INTO {db_prefix}iso_countries VALUES ('GU','GUAM','Guam','GUM','316');
INSERT INTO {db_prefix}iso_countries VALUES ('GT','GUATEMALA','Guatemala','GTM','320');
INSERT INTO {db_prefix}iso_countries VALUES ('GN','GUINEA','Guinea','GIN','324');
INSERT INTO {db_prefix}iso_countries VALUES ('GW','GUINEA-BISSAU','Guinea-Bissau','GNB','624');
INSERT INTO {db_prefix}iso_countries VALUES ('GY','GUYANA','Guyana','GUY','328');
INSERT INTO {db_prefix}iso_countries VALUES ('HT','HAITI','Haiti','HTI','332');
INSERT INTO {db_prefix}iso_countries VALUES ('HM','HEARD ISLAND AND MCDONALD ISLANDS','Heard Island and Mcdonald Islands',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('VA','HOLY SEE (VATICAN CITY STATE)','Holy See (Vatican City State)','VAT','336');
INSERT INTO {db_prefix}iso_countries VALUES ('HN','HONDURAS','Honduras','HND','340');
INSERT INTO {db_prefix}iso_countries VALUES ('HK','HONG KONG','Hong Kong','HKG','344');
INSERT INTO {db_prefix}iso_countries VALUES ('HU','HUNGARY','Hungary','HUN','348');
INSERT INTO {db_prefix}iso_countries VALUES ('IS','ICELAND','Iceland','ISL','352');
INSERT INTO {db_prefix}iso_countries VALUES ('IN','INDIA','India','IND','356');
INSERT INTO {db_prefix}iso_countries VALUES ('ID','INDONESIA','Indonesia','IDN','360');
INSERT INTO {db_prefix}iso_countries VALUES ('IR','IRAN, ISLAMIC REPUBLIC OF','Iran, Islamic Republic of','IRN','364');
INSERT INTO {db_prefix}iso_countries VALUES ('IQ','IRAQ','Iraq','IRQ','368');
INSERT INTO {db_prefix}iso_countries VALUES ('IE','IRELAND','Ireland','IRL','372');
INSERT INTO {db_prefix}iso_countries VALUES ('IL','ISRAEL','Israel','ISR','376');
INSERT INTO {db_prefix}iso_countries VALUES ('IT','ITALY','Italy','ITA','380');
INSERT INTO {db_prefix}iso_countries VALUES ('JM','JAMAICA','Jamaica','JAM','388');
INSERT INTO {db_prefix}iso_countries VALUES ('JP','JAPAN','Japan','JPN','392');
INSERT INTO {db_prefix}iso_countries VALUES ('JO','JORDAN','Jordan','JOR','400');
INSERT INTO {db_prefix}iso_countries VALUES ('KZ','KAZAKHSTAN','Kazakhstan','KAZ','398');
INSERT INTO {db_prefix}iso_countries VALUES ('KE','KENYA','Kenya','KEN','404');
INSERT INTO {db_prefix}iso_countries VALUES ('KI','KIRIBATI','Kiribati','KIR','296');
INSERT INTO {db_prefix}iso_countries VALUES ('KP','KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF','Korea, Democratic People\'s Republic of','PRK','408');
INSERT INTO {db_prefix}iso_countries VALUES ('KR','KOREA, REPUBLIC OF','Korea, Republic of','KOR','410');
INSERT INTO {db_prefix}iso_countries VALUES ('KW','KUWAIT','Kuwait','KWT','414');
INSERT INTO {db_prefix}iso_countries VALUES ('KG','KYRGYZSTAN','Kyrgyzstan','KGZ','417');
INSERT INTO {db_prefix}iso_countries VALUES ('LA','LAO PEOPLE\'S DEMOCRATIC REPUBLIC','Lao People\'s Democratic Republic','LAO','418');
INSERT INTO {db_prefix}iso_countries VALUES ('LV','LATVIA','Latvia','LVA','428');
INSERT INTO {db_prefix}iso_countries VALUES ('LB','LEBANON','Lebanon','LBN','422');
INSERT INTO {db_prefix}iso_countries VALUES ('LS','LESOTHO','Lesotho','LSO','426');
INSERT INTO {db_prefix}iso_countries VALUES ('LR','LIBERIA','Liberia','LBR','430');
INSERT INTO {db_prefix}iso_countries VALUES ('LY','LIBYAN ARAB JAMAHIRIYA','Libyan Arab Jamahiriya','LBY','434');
INSERT INTO {db_prefix}iso_countries VALUES ('LI','LIECHTENSTEIN','Liechtenstein','LIE','438');
INSERT INTO {db_prefix}iso_countries VALUES ('LT','LITHUANIA','Lithuania','LTU','440');
INSERT INTO {db_prefix}iso_countries VALUES ('LU','LUXEMBOURG','Luxembourg','LUX','442');
INSERT INTO {db_prefix}iso_countries VALUES ('MO','MACAO','Macao','MAC','446');
INSERT INTO {db_prefix}iso_countries VALUES ('MK','MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','Macedonia, the Former Yugoslav Republic of','MKD','807');
INSERT INTO {db_prefix}iso_countries VALUES ('MG','MADAGASCAR','Madagascar','MDG','450');
INSERT INTO {db_prefix}iso_countries VALUES ('MW','MALAWI','Malawi','MWI','454');
INSERT INTO {db_prefix}iso_countries VALUES ('MY','MALAYSIA','Malaysia','MYS','458');
INSERT INTO {db_prefix}iso_countries VALUES ('MV','MALDIVES','Maldives','MDV','462');
INSERT INTO {db_prefix}iso_countries VALUES ('ML','MALI','Mali','MLI','466');
INSERT INTO {db_prefix}iso_countries VALUES ('MT','MALTA','Malta','MLT','470');
INSERT INTO {db_prefix}iso_countries VALUES ('MH','MARSHALL ISLANDS','Marshall Islands','MHL','584');
INSERT INTO {db_prefix}iso_countries VALUES ('MQ','MARTINIQUE','Martinique','MTQ','474');
INSERT INTO {db_prefix}iso_countries VALUES ('MR','MAURITANIA','Mauritania','MRT','478');
INSERT INTO {db_prefix}iso_countries VALUES ('MU','MAURITIUS','Mauritius','MUS','480');
INSERT INTO {db_prefix}iso_countries VALUES ('YT','MAYOTTE','Mayotte',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('MX','MEXICO','Mexico','MEX','484');
INSERT INTO {db_prefix}iso_countries VALUES ('FM','MICRONESIA, FEDERATED STATES OF','Micronesia, Federated States of','FSM','583');
INSERT INTO {db_prefix}iso_countries VALUES ('MD','MOLDOVA, REPUBLIC OF','Moldova, Republic of','MDA','498');
INSERT INTO {db_prefix}iso_countries VALUES ('MC','MONACO','Monaco','MCO','492');
INSERT INTO {db_prefix}iso_countries VALUES ('MN','MONGOLIA','Mongolia','MNG','496');
INSERT INTO {db_prefix}iso_countries VALUES ('MS','MONTSERRAT','Montserrat','MSR','500');
INSERT INTO {db_prefix}iso_countries VALUES ('MA','MOROCCO','Morocco','MAR','504');
INSERT INTO {db_prefix}iso_countries VALUES ('MZ','MOZAMBIQUE','Mozambique','MOZ','508');
INSERT INTO {db_prefix}iso_countries VALUES ('MM','MYANMAR','Myanmar','MMR','104');
INSERT INTO {db_prefix}iso_countries VALUES ('NA','NAMIBIA','Namibia','NAM','516');
INSERT INTO {db_prefix}iso_countries VALUES ('NR','NAURU','Nauru','NRU','520');
INSERT INTO {db_prefix}iso_countries VALUES ('NP','NEPAL','Nepal','NPL','524');
INSERT INTO {db_prefix}iso_countries VALUES ('NL','NETHERLANDS','Netherlands','NLD','528');
INSERT INTO {db_prefix}iso_countries VALUES ('AN','NETHERLANDS ANTILLES','Netherlands Antilles','ANT','530');
INSERT INTO {db_prefix}iso_countries VALUES ('NC','NEW CALEDONIA','New Caledonia','NCL','540');
INSERT INTO {db_prefix}iso_countries VALUES ('NZ','NEW ZEALAND','New Zealand','NZL','554');
INSERT INTO {db_prefix}iso_countries VALUES ('NI','NICARAGUA','Nicaragua','NIC','558');
INSERT INTO {db_prefix}iso_countries VALUES ('NE','NIGER','Niger','NER','562');
INSERT INTO {db_prefix}iso_countries VALUES ('NG','NIGERIA','Nigeria','NGA','566');
INSERT INTO {db_prefix}iso_countries VALUES ('NU','NIUE','Niue','NIU','570');
INSERT INTO {db_prefix}iso_countries VALUES ('NF','NORFOLK ISLAND','Norfolk Island','NFK','574');
INSERT INTO {db_prefix}iso_countries VALUES ('MP','NORTHERN MARIANA ISLANDS','Northern Mariana Islands','MNP','580');
INSERT INTO {db_prefix}iso_countries VALUES ('NO','NORWAY','Norway','NOR','578');
INSERT INTO {db_prefix}iso_countries VALUES ('OM','OMAN','Oman','OMN','512');
INSERT INTO {db_prefix}iso_countries VALUES ('PK','PAKISTAN','Pakistan','PAK','586');
INSERT INTO {db_prefix}iso_countries VALUES ('PW','PALAU','Palau','PLW','585');
INSERT INTO {db_prefix}iso_countries VALUES ('PS','PALESTINIAN TERRITORY, OCCUPIED','Palestinian Territory, Occupied',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('PA','PANAMA','Panama','PAN','591');
INSERT INTO {db_prefix}iso_countries VALUES ('PG','PAPUA NEW GUINEA','Papua New Guinea','PNG','598');
INSERT INTO {db_prefix}iso_countries VALUES ('PY','PARAGUAY','Paraguay','PRY','600');
INSERT INTO {db_prefix}iso_countries VALUES ('PE','PERU','Peru','PER','604');
INSERT INTO {db_prefix}iso_countries VALUES ('PH','PHILIPPINES','Philippines','PHL','608');
INSERT INTO {db_prefix}iso_countries VALUES ('PN','PITCAIRN','Pitcairn','PCN','612');
INSERT INTO {db_prefix}iso_countries VALUES ('PL','POLAND','Poland','POL','616');
INSERT INTO {db_prefix}iso_countries VALUES ('PT','PORTUGAL','Portugal','PRT','620');
INSERT INTO {db_prefix}iso_countries VALUES ('PR','PUERTO RICO','Puerto Rico','PRI','630');
INSERT INTO {db_prefix}iso_countries VALUES ('QA','QATAR','Qatar','QAT','634');
INSERT INTO {db_prefix}iso_countries VALUES ('RE','REUNION','Reunion','REU','638');
INSERT INTO {db_prefix}iso_countries VALUES ('RO','ROMANIA','Romania','ROM','642');
INSERT INTO {db_prefix}iso_countries VALUES ('RU','RUSSIAN FEDERATION','Russian Federation','RUS','643');
INSERT INTO {db_prefix}iso_countries VALUES ('RW','RWANDA','Rwanda','RWA','646');
INSERT INTO {db_prefix}iso_countries VALUES ('SH','SAINT HELENA','Saint Helena','SHN','654');
INSERT INTO {db_prefix}iso_countries VALUES ('KN','SAINT KITTS AND NEVIS','Saint Kitts and Nevis','KNA','659');
INSERT INTO {db_prefix}iso_countries VALUES ('LC','SAINT LUCIA','Saint Lucia','LCA','662');
INSERT INTO {db_prefix}iso_countries VALUES ('PM','SAINT PIERRE AND MIQUELON','Saint Pierre and Miquelon','SPM','666');
INSERT INTO {db_prefix}iso_countries VALUES ('VC','SAINT VINCENT AND THE GRENADINES','Saint Vincent and the Grenadines','VCT','670');
INSERT INTO {db_prefix}iso_countries VALUES ('WS','SAMOA','Samoa','WSM','882');
INSERT INTO {db_prefix}iso_countries VALUES ('SM','SAN MARINO','San Marino','SMR','674');
INSERT INTO {db_prefix}iso_countries VALUES ('ST','SAO TOME AND PRINCIPE','Sao Tome and Principe','STP','678');
INSERT INTO {db_prefix}iso_countries VALUES ('SA','SAUDI ARABIA','Saudi Arabia','SAU','682');
INSERT INTO {db_prefix}iso_countries VALUES ('SN','SENEGAL','Senegal','SEN','686');
INSERT INTO {db_prefix}iso_countries VALUES ('CS','SERBIA AND MONTENEGRO','Serbia and Montenegro',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('SC','SEYCHELLES','Seychelles','SYC','690');
INSERT INTO {db_prefix}iso_countries VALUES ('SL','SIERRA LEONE','Sierra Leone','SLE','694');
INSERT INTO {db_prefix}iso_countries VALUES ('SG','SINGAPORE','Singapore','SGP','702');
INSERT INTO {db_prefix}iso_countries VALUES ('SK','SLOVAKIA','Slovakia','SVK','703');
INSERT INTO {db_prefix}iso_countries VALUES ('SI','SLOVENIA','Slovenia','SVN','705');
INSERT INTO {db_prefix}iso_countries VALUES ('SB','SOLOMON ISLANDS','Solomon Islands','SLB','090');
INSERT INTO {db_prefix}iso_countries VALUES ('SO','SOMALIA','Somalia','SOM','706');
INSERT INTO {db_prefix}iso_countries VALUES ('ZA','SOUTH AFRICA','South Africa','ZAF','710');
INSERT INTO {db_prefix}iso_countries VALUES ('GS','SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS','South Georgia and the South Sandwich Islands',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('ES','SPAIN','Spain','ESP','724');
INSERT INTO {db_prefix}iso_countries VALUES ('LK','SRI LANKA','Sri Lanka','LKA','144');
INSERT INTO {db_prefix}iso_countries VALUES ('SD','SUDAN','Sudan','SDN','736');
INSERT INTO {db_prefix}iso_countries VALUES ('SR','SURINAME','Suriname','SUR','740');
INSERT INTO {db_prefix}iso_countries VALUES ('SJ','SVALBARD AND JAN MAYEN','Svalbard and Jan Mayen','SJM','744');
INSERT INTO {db_prefix}iso_countries VALUES ('SZ','SWAZILAND','Swaziland','SWZ','748');
INSERT INTO {db_prefix}iso_countries VALUES ('SE','SWEDEN','Sweden','SWE','752');
INSERT INTO {db_prefix}iso_countries VALUES ('CH','SWITZERLAND','Switzerland','CHE','756');
INSERT INTO {db_prefix}iso_countries VALUES ('SY','SYRIAN ARAB REPUBLIC','Syrian Arab Republic','SYR','760');
INSERT INTO {db_prefix}iso_countries VALUES ('TW','TAIWAN, PROVINCE OF CHINA','Taiwan, Province of China','TWN','158');
INSERT INTO {db_prefix}iso_countries VALUES ('TJ','TAJIKISTAN','Tajikistan','TJK','762');
INSERT INTO {db_prefix}iso_countries VALUES ('TZ','TANZANIA, UNITED REPUBLIC OF','Tanzania, United Republic of','TZA','834');
INSERT INTO {db_prefix}iso_countries VALUES ('TH','THAILAND','Thailand','THA','764');
INSERT INTO {db_prefix}iso_countries VALUES ('TL','TIMOR-LESTE','Timor-Leste',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('TG','TOGO','Togo','TGO','768');
INSERT INTO {db_prefix}iso_countries VALUES ('TK','TOKELAU','Tokelau','TKL','772');
INSERT INTO {db_prefix}iso_countries VALUES ('TO','TONGA','Tonga','TON','776');
INSERT INTO {db_prefix}iso_countries VALUES ('TT','TRINIDAD AND TOBAGO','Trinidad and Tobago','TTO','780');
INSERT INTO {db_prefix}iso_countries VALUES ('TN','TUNISIA','Tunisia','TUN','788');
INSERT INTO {db_prefix}iso_countries VALUES ('TR','TURKEY','Turkey','TUR','792');
INSERT INTO {db_prefix}iso_countries VALUES ('TM','TURKMENISTAN','Turkmenistan','TKM','795');
INSERT INTO {db_prefix}iso_countries VALUES ('TC','TURKS AND CAICOS ISLANDS','Turks and Caicos Islands','TCA','796');
INSERT INTO {db_prefix}iso_countries VALUES ('TV','TUVALU','Tuvalu','TUV','798');
INSERT INTO {db_prefix}iso_countries VALUES ('UG','UGANDA','Uganda','UGA','800');
INSERT INTO {db_prefix}iso_countries VALUES ('UA','UKRAINE','Ukraine','UKR','804');
INSERT INTO {db_prefix}iso_countries VALUES ('AE','UNITED ARAB EMIRATES','United Arab Emirates','ARE','784');
INSERT INTO {db_prefix}iso_countries VALUES ('GB','UNITED KINGDOM','United Kingdom','GBR','826');
INSERT INTO {db_prefix}iso_countries VALUES ('US','UNITED STATES','United States','USA','840');
INSERT INTO {db_prefix}iso_countries VALUES ('UM','UNITED STATES MINOR OUTLYING ISLANDS','United States Minor Outlying Islands',NULL,NULL);
INSERT INTO {db_prefix}iso_countries VALUES ('UY','URUGUAY','Uruguay','URY','858');
INSERT INTO {db_prefix}iso_countries VALUES ('UZ','UZBEKISTAN','Uzbekistan','UZB','860');
INSERT INTO {db_prefix}iso_countries VALUES ('VU','VANUATU','Vanuatu','VUT','548');
INSERT INTO {db_prefix}iso_countries VALUES ('VE','VENEZUELA','Venezuela','VEN','862');
INSERT INTO {db_prefix}iso_countries VALUES ('VN','VIET NAM','Viet Nam','VNM','704');
INSERT INTO {db_prefix}iso_countries VALUES ('VG','VIRGIN ISLANDS, BRITISH','Virgin Islands, British','VGB','092');
INSERT INTO {db_prefix}iso_countries VALUES ('VI','VIRGIN ISLANDS, U.S.','Virgin Islands, U.s.','VIR','850');
INSERT INTO {db_prefix}iso_countries VALUES ('WF','WALLIS AND FUTUNA','Wallis and Futuna','WLF','876');
INSERT INTO {db_prefix}iso_countries VALUES ('EH','WESTERN SAHARA','Western Sahara','ESH','732');
INSERT INTO {db_prefix}iso_countries VALUES ('YE','YEMEN','Yemen','YEM','887');
INSERT INTO {db_prefix}iso_countries VALUES ('ZM','ZAMBIA','Zambia','ZMB','894');
INSERT INTO {db_prefix}iso_countries VALUES ('ZW','ZIMBABWE','Zimbabwe','ZWE','716');