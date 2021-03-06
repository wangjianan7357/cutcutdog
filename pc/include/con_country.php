<?php
/**
 * 定义国家名称及其缩写
 */

/* 定义国家英文名 */
$con_country_en = array(
	'af' => 'Afghanistan',
	'al' => 'Albania',
	'dz' => 'Algeria',
	'as' => 'American Samoa',
	'ad' => 'Andorra',
	'ao' => 'Angola',
	'ai' => 'Anguilla',
	'aq' => 'Antarctica',
	'ag' => 'Antigua and Barbuda',
	'ar' => 'Argentina',
	'am' => 'Armenia',
	'aw' => 'Aruba',
	'au' => 'Australia',
	'at' => 'Austria',
	'az' => 'Azerbaijan',
	'bs' => 'Bahamas',
	'bh' => 'Bahrain',
	'bd' => 'Bangladesh',
	'bb' => 'Barbados',
	'by' => 'Belarus',
	'be' => 'Belgium',
	'bz' => 'Belize',
	'bj' => 'Benin',
	'bm' => 'Bermuda',
	'bt' => 'Bhutan',
	'bo' => 'Bolivia',
	'ba' => 'Bosnia & Herzegovina',
	'bw' => 'Botswana',
	'bv' => 'Bouvet Island',
	'br' => 'Brazil',
	'io' => 'British Indian Ocean Territory',
	'bn' => 'Brunei Darussalam',
	'bg' => 'Bulgaria',
	'bf' => 'Burkina Faso',
	'bi' => 'Burundi',
	'kh' => 'Cambodia',
	'cm' => 'Cameroon',
	'ca' => 'Canada',
	'cv' => 'Cape Verde',
	'ky' => 'Cayman Islands',
	'cf' => 'Central African Republic',
	'td' => 'Chad',
	'cl' => 'Chile',
	'cn' => 'China',
	'cx' => 'Christmas Island',
	'cc' => 'Cocos (Keeling) Islands',
	'co' => 'Colombia',
	'km' => 'Comoros',
	'cg' => 'Congo (Brazzaville)',
	'cd' => 'Congo (Kinshasa)',
	'ck' => 'Cook Islands',
	'cr' => 'Costa Rica',
	'ci' => 'Cote d\'Ivoire',
	'hr' => 'Croatia',
	'cu' => 'Cuba',
	'cy' => 'Cyprus',
	'cz' => 'Czech Republic',
	'dk' => 'Denmark',
	'dj' => 'Djibouti',
	'dm' => 'Dominica',
	'do' => 'Dominican Republic',
	'tl' => 'East Timor',
	'ec' => 'Ecuador',
	'eg' => 'Egypt',
	'sv' => 'El Salvador',
	'gq' => 'Equatorial Guinea',
	'er' => 'Eritrea',
	'ee' => 'Estonia',
	'et' => 'Ethiopia',
	'fk' => 'Falkland Islands',
	'fo' => 'Faroe Islands',
	'fj' => 'Fiji',
	'fi' => 'Finland',
	'fr' => 'France',
	'gf' => 'French Guiana',
	'pf' => 'French Polynesia',
	'tf' => 'French Southern Territories',
	'ga' => 'Gabon',
	'gm' => 'Gambia',
	'ge' => 'Georgia',
	'de' => 'Germany',
	'gh' => 'Ghana',
	'gi' => 'Gibraltar',
	'gr' => 'Greece',
	'gl' => 'Greenland',
	'gd' => 'Grenada',
	'gp' => 'Guadeloupe',
	'gu' => 'Guam',
	'gt' => 'Guatemala',
	'gn' => 'Guinea',
	'gw' => 'Guinea-Bissau',
	'gy' => 'Guyana',
	'ht' => 'Haiti',
	'hm' => 'Heard & McDonald Islands',
	'hn' => 'Honduras',
	'hk' => 'Hong Kong',
	'hu' => 'Hungary',
	'is' => 'Iceland',
	'in' => 'India',
	'id' => 'Indonesia',
	'ir' => 'Iran',
	'iq' => 'Iraq',
	'ie' => 'Ireland',
	'il' => 'Israel',
	'it' => 'Italy',
	'jm' => 'Jamaica',
	'jp' => 'Japan',
	'jo' => 'Jordan',
	'kz' => 'Kazakhstan',
	'ke' => 'Kenya',
	'ki' => 'Kiribati',
	'kw' => 'Kuwait',
	'kg' => 'Kyrgyzstan',
	'la' => 'Laos',
	'lv' => 'Latvia',
	'lb' => 'Lebanon',
	'ls' => 'Lesotho',
	'lr' => 'Liberia',
	'ly' => 'Libya',
	'li' => 'Liechtenstein',
	'lt' => 'Lithuania',
	'lu' => 'Luxembourg',
	'mo' => 'Macau',
	'mk' => 'Macedonia',
	'mg' => 'Madagascar',
	'mw' => 'Malawi',
	'my' => 'Malaysia',
	'mv' => 'Maldives',
	'ml' => 'Mali',
	'mt' => 'Malta',
	'mh' => 'Marshall Islands',
	'mq' => 'Martinique',
	'mr' => 'Mauritania',
	'mu' => 'Mauritius',
	'yt' => 'Mayotte',
	'mx' => 'Mexico',
	'fm' => 'Micronesia',
	'md' => 'Moldova',
	'mc' => 'Monaco',
	'mn' => 'Mongolia',
	'ms' => 'Montserrat',
	'ma' => 'Morocco',
	'mz' => 'Mozambique',
	'mm' => 'Myanmar',
	'na' => 'Namibia',
	'nr' => 'Nauru',
	'np' => 'Nepal',
	'nl' => 'Netherlands',
	'an' => 'Netherlands Antilles',
	'nc' => 'New Caledonia',
	'nz' => 'New Zealand',
	'ni' => 'Nicaragua',
	'ne' => 'Niger',
	'ng' => 'Nigeria',
	'nu' => 'Niue',
	'nf' => 'Norfolk Island',
	'kp' => 'North Korea',
	'mp' => 'Northern Mariana Islands',
	'no' => 'Norway',
	'om' => 'Oman',
	'pk' => 'Pakistan',
	'pw' => 'Palau',
	'ps' => 'Palestine',
	'pa' => 'Panama',
	'pg' => 'Papua New Guinea',
	'py' => 'Paraguay',
	'pe' => 'Peru',
	'ph' => 'Philippines',
	'pn' => 'Pitcairn',
	'pl' => 'Poland',
	'pt' => 'Portugal',
	'pr' => 'Puerto Rico',
	'qa' => 'Qatar',
	're' => 'Reunion',
	'ro' => 'Romania',
	'ru' => 'Russian Federation',
	'rw' => 'Rwanda',
	'kn' => 'Saint Kitts and Nevis',
	'ws' => 'Samoa',
	'sm' => 'San Marino',
	'st' => 'Sao Tome and Principe',
	'sa' => 'Saudi Arabia',
	'sn' => 'Senegal',
	'yu' => 'Serbia and Montenegro',
	'sc' => 'Seychelles',
	'sl' => 'Sierra Leone',
	'sg' => 'Singapore',
	'sk' => 'Slovakia',
	'si' => 'Slovenia',
	'sb' => 'Solomon Islands',
	'so' => 'Somalia',
	'za' => 'South Africa',
	'kr' => 'South Korea',
	'es' => 'Spain',
	'lk' => 'Sri Lanka',
	'lc' => 'St Lucia',
	'sh' => 'St. Helena',
	'pm' => 'St. Pierre and Miquelon',
	'vc' => 'St. Vincent and The Grenadines',
	'sd' => 'Sudan',
	'sr' => 'Suriname',
	'sj' => 'Svalbard and Jan Mayen',
	'sz' => 'Swaziland',
	'se' => 'Sweden',
	'ch' => 'Switzerland',
	'sy' => 'Syria',
	'tw' => 'Taiwan',
	'tj' => 'Tajikistan',
	'tz' => 'Tanzania',
	'th' => 'Thailand',
	'tg' => 'Togo',
	'tk' => 'Tokelau',
	'to' => 'Tonga',
	'tt' => 'Trinidad & Tobago',
	'tn' => 'Tunisia',
	'tr' => 'Turkey',
	'tm' => 'Turkmenistan',
	'tc' => 'Turks and Caicos Islands',
	'tv' => 'Tuvalu',
	'ug' => 'Uganda',
	'ua' => 'Ukraine',
	'ue' => 'United Arab Emirates',
	'gb' => 'United Kingdom',
	'us' => 'United States',
	'um' => 'United States Oceania',
	'uy' => 'Uruguay',
	'uz' => 'Uzbekistan',
	'vu' => 'Vanuatu',
	'va' => 'Vatican',
	've' => 'Venezuela',
	'vn' => 'Vietnam',
	'vg' => 'Virgin Islands (British)',
	'wf' => 'Wallis and Futuna',
	'eh' => 'Western Sahara',
	'ye' => 'Yemen',
	'zm' => 'Zambia',
	'zw' => 'Zimbabwe'
);

/* 定义国家中文名 */
$con_country_cn = array(
	'ad' => '安道尔',
	'ae' => '阿拉伯联合酋长国',
	'af' => '阿富汗',
	//'ag' => '安提瓜和巴尔布达(加勒比海)',
	'ai' => '安圭拉岛',
	'al' => '阿尔巴尼亚',
	'am' => '亚美尼亚(西南亚)',
	'an' => '荷属西印度群岛',
	'ao' => '安哥拉',
	'aq' => '南极洲',
	'ar' => '阿根廷',
	//'as' => '美属萨摩亚',
	'at' => '奥地利(中欧)',
	'au' => '澳大利亚',
	'aw' => '亚鲁伯',
	//'az' => '亚塞拜然共和国(西南亚)',
	'ba' => '波斯尼亚',
	'bb' => '巴贝多(加勒比海)',
	'bd' => '孟加拉',
	'be' => '比利时',
	'bf' => '布吉纳法索(南非)',
	'bg' => '保加利亚(东欧)',
	'bh' => '巴林(波斯湾)',
	'bi' => '蒲隆地(中非)',
	'bj' => '贝南(西非)',
	//'bm' => '百慕达群岛(大西洋西部)',
	'bn' => '文莱(东亚)',
	'bo' => '玻利维亚(南美洲)',
	'br' => '巴西(南美)',
	'bs' => '巴哈马群岛',
	'bt' => '不丹(印度北部)',
	'bv' => '布干维尔岛',
	'bw' => '波札那(南非)',
	'by' => '柏劳斯',
	'bz' => '贝里斯(加勒比海)',
	'ca' => '加拿大',
	//'cc' => '可可斯群岛(椰子岛)',
	'cf' => '中非共和国',
	'cg' => '刚果民主共和国',
	'ch' => '瑞士(中欧)',
	'ck' => '科克群岛',
	'cl' => '智利(南美洲西南部)',
	'cm' => '喀麦隆(西非)',
	'cn' => '中国',
	'co' => '哥伦比亚',
	'cr' => '哥斯达黎加(中美洲)',
	'cu' => '古巴(加勒比海)',
	'cv' => '维德角(大西洋东部)',
	'cx' => '圣诞岛屿',
	//'cy' => '赛普勒斯(土耳其西南方)',
	'cz' => '捷克(中欧)',
	'de' => '德国',
	'dj' => '吉布地(东非)',
	'dk' => '丹麦(西北欧)',
	'dm' => '多明尼克岛(加勒比海)',
	'do' => '多米尼加(加勒比海)',
	'dz' => '阿尔及利亚',
	//'ec' => '厄瓜多尔(南美洲西北部)',
	'ee' => '爱沙尼亚(波罗的海)',
	'eg' => '埃及',
	//'eh' => '西撒哈拉沙漠',
	'er' => '厄立特里亚(东北非)',
	'es' => '西班牙',
	'et' => '埃塞俄比亚',
	'fi' => '芬兰(东北欧)',
	'fj' => '裴济(西南太平洋)',
	'fk' => '福克兰群岛',
	//'fm' => '密克罗尼西亚(太平洋西部)',
	'fr' => '法国',
	'ga' => '加彭(中非西部)',
	'gb' => '英国',
	//'gd' => '格瑞那达(西印度群岛东南部)',
	'ge' => '乔治亚洲',
	//'gf' => '法属圭亚那(南美洲东北部)',
	'gh' => '迦纳(西非)',
	'gi' => '直布罗陀海峡',
	'gl' => '格陵兰(北大西洋)',
	'gm' => '甘比亚',
	'gn' => '几内亚(西非)',
	'gp' => '瓜达康纳尔岛',
	'gq' => '赤道几内亚(西非)',
	'gr' => '希腊',
	'gt' => '瓜地马拉',
	'gu' => '关岛',
	'gw' => '几内亚比索',
	'gy' => '盖亚那',
	'hk' => '香港特别行政区',
	'hn' => '洪都拉斯',
	'hr' => '克罗埃西亚',
	'ht' => '海地',
	'hu' => '匈牙利',
	'id' => '印尼',
	'ie' => '爱尔兰',
	'il' => '以色列',
	'in' => '印度',
	'io' => '英属印度洋领域',
	'iq' => '伊拉克',
	'ir' => '伊朗',
	'is' => '冰岛',
	'it' => '意大利',
	'jm' => '牙买加',
	'jo' => '约旦',
	'jp' => '日本',
	'ke' => '肯亚',
	'kh' => '高棉(柬埔寨)',
	'ki' => '吉里巴斯',
	//'km' => '葛摩伊斯兰联邦共和国(印度洋西部)',
	'kp' => '南韩',
	'kr' => '北韩',
	'kw' => '科威特',
	'ky' => '开曼群岛',
	'kz' => '哈萨克',
	'la' => '寮国',
	'lb' => '黎巴嫩',
	'lc' => '圣路其亚',
	'li' => '列支敦斯登',
	'lk' => '斯里兰卡',
	'lr' => '赖比瑞亚',
	'ls' => '赖索托',
	'lt' => '立陶宛',
	'lu' => '卢森堡',
	'lv' => '拉脱维亚',
	'ma' => '摩洛哥',
	'mc' => '摩纳哥',
	'mg' => '马达加斯加',
	'mh' => '马绍尔群岛',
	'mk' => '马其顿',
	'ml' => '马利',
	'mn' => '蒙古',
	'mo' => '澳门特别行政区',
	'mp' => '马里亚纳群岛',
	'mq' => '圣马丁节',
	'mr' => '茅利塔尼亚',
	'ms' => '蒙特色纳岛',
	'mt' => '马尔他',
	'mu' => '模里西斯',
	'mv' => '马尔代夫',
	'mw' => '马拉威',
	'mx' => '墨西哥',
	'my' => '马来群岛',
	'mz' => '莫三比克',
	'na' => '纳米比亚',
	'nc' => '新苏格兰',
	'ne' => '尼日',
	'nf' => '诺福克岛屿',
	'ng' => '奈及利亚',
	'ni' => '尼加拉瓜',
	'nl' => '荷兰',
	'no' => '挪威',
	'np' => '尼泊尔',
	'nr' => '诺鲁',
	'nu' => '尼乌亚岛',
	'nz' => '新西兰',
	'om' => '阿曼',
	'pa' => '巴拿马',
	'pe' => '秘鲁',
	'pf' => '法国的玻里尼西亚',
	'pg' => '巴布亚新几内亚',
	'ph' => '菲律宾群岛',
	'pk' => '巴基斯坦',
	'pl' => '波兰',
	'pr' => '波多黎各',
	'pt' => '葡萄牙',
	'pw' => '帛琉',
	'py' => '巴拉圭',
	'qa' => '卡达',
	're' => '留尼旺岛',
	'ro' => '罗马尼亚',
	'ru' => '俄罗斯联邦',
	'rw' => '卢旺达',
	'sa' => '沙特阿拉伯',
	'sb' => '所罗门群岛',
	'sc' => '赛席尔群岛',
	'sd' => '苏丹',
	'se' => '瑞典',
	'sg' => '新加坡',
	'sh' => '圣赫勒拿岛',
	'si' => '斯洛法尼亚',
	'sj' => '冷岸和央麦恩岛',
	'sk' => '斯洛法克人共和国',
	'sl' => '狮子山',
	'sm' => '圣马利诺',
	'sn' => '塞内加尔',
	'so' => '索马里',
	'sr' => '苏利南',
	'sv' => '萨尔瓦多(中南美洲)',
	'sy' => '叙利亚',
	'sz' => '史瓦济兰',
	//'tc' => '土克斯和开卡斯群岛',
	'td' => '查德(中北非)',
	'tf' => '法国的南方的领域',
	'tg' => '土哥(西非)',
	'th' => '泰国',
	'tk' => '托客劳群岛',
	'tm' => '土库曼(中亚)',
	'tn' => '突尼西亚(北非)',
	'to' => '东加王国(西南太平洋)',
	'tr' => '土耳其',
	//'tt' => '千理达和托贝哥共和国',
	'tv' => '吐瓦鲁(西南太平洋)',
	'tw' => '中国台湾省',
	'tz' => '坦尚尼亚',
	'ua' => '乌克兰',
	'ug' => '乌干达',
	'uk' => '英国',
	//'um' => '联合的状况微小的在外的岛屿',
	'us' => '美国',
	'uy' => '乌拉圭',
	'uz' => '乌兹别克斯坦',
	'va' => '梵蒂冈',
	've' => '委内瑞拉(南美洲北部)',
	'vg' => '英属维京群岛',
	//'vi' => '美英属维京群岛',
	'vn' => '越南',
	//'vu' => '梵尼瓦土;万那杜(南太平洋)',
	//'wf' => '沃利斯和富图纳群岛',
	'ws' => '萨摩亚群岛',
	'ye' => '叶门',
	'yt' => '梅约特',
	'yu' => '南斯拉夫',
	'za' => '南非',
	'zm' => '尚比亚',
	'zr' => '扎伊尔',
	'zw' => '辛巴威(南非)'
);
?>