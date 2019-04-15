<?php if (!isset($_GET['xml'])){die;}
	set_time_limit(0);
	require('simple_html_dom.php');
	
	$url = "http://".$_SERVER['SERVER_NAME'];
	$alias = [
'Bank Transfer USD'=>'WIREUSD',
'WebMoney USD'=>'WMZ',
'WebMoney RUB'=>'WMR',
'WebMoney EUR'=>'WME',
'Paymer USD'=>'PMRUSD',
'Paymer RUB'=>'PMRRUB',
'Paymer UAH'=>'PMRUAH',
'Yandex Money RUB'=>'YAMRUB',
'QIWI USD'=>'QWUSD',
'QIWI RUB'=>'QWRUB',
'QIWI EUR'=>'QWEUR',
'Perfect Money USD'=>'PMUSD',
'Perfect Money EUR'=>'PMEUR',
'Perfect Money BTC'=>'PMBTC',
'PayPal USD'=>'PPUSD',
'PayPal RUB'=>'PPRUB',
'PayPal EUR'=>'PPEUR',
'PayPal GBP'=>'PPGBP',
'BTC-e USD'=>'BTCEUSD',
'BTC-e RUB'=>'BTCERUB',
'BTC-e EUR'=>'BTCEEUR',
'Exmo USD'=>'EXMUSD',
'Exmo RUB'=>'EXMRUB',
'Exmo EUR'=>'EXMEUR',
'AdvCash USD'=>'ADVCUSD',
'AdvCash RUB'=>'ADVCRUB',
'AdvCash EUR'=>'ADVCEUR',
'AdvCash UAH'=>'ADVCUAH',
'Payeer USD'=>'PRUSD',
'Payeer RUB'=>'PRRUB',
'Payeer EUR'=>'PREUR',
'Skrill USD'=>'SKLUSD',
'Skrill EUR'=>'SKLEUR',
'Payza USD'=>'PAUSD',
'Payza EUR'=>'PAEUR',
'OKPay USD'=>'OKUSD',
'OKPay RUB'=>'OKRUB',
'OKPay EUR'=>'OKEUR',
'W1 USD'=>'WOUSD',
'W1 UAH'=>'WOUAH',
'Paxum USD'=>'PAXUMUSD',
'Paxum EUR'=>'PAXUMEUR',
'MoneyPolo USD'=>'MPLUSD',
'MoneyPolo EUR'=>'MPLEUR',
'Neteller USD'=>'NTLRUSD',
'Neteller EUR'=>'NTLREUR',
'Приват 24 USD'=>'P24USD',
'Приват 24 UAH'=>'P24UAH',
'Western Union USD'=>'WUUSD',
'Western Union EUR'=>'WUEUR',
'MoneyGram USD'=>'MGUSD',
'MoneyGram EUR'=>'MGEUR',
'Contact USD'=>'CNTUSD',
'Contact RUB'=>'CNTRUB',
'Золотая Корона USD'=>'GCMTUSD',
'Сбербанк RUB'=>'SBERRUB',
'ВТБ24 RUB'=>'TBRUB',
'Золотая Корона RUB'=>'GCMTRUB'


	];
	
	
	
	function get_page($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);      
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
		$data=curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	function generate($info) {
		$xml = '<rates>'.PHP_EOL;
		foreach ($info as $item) {
			$xml .= '<item>'.PHP_EOL;
			if(isset($item['from']))
			$xml .= '<from>' . $item['from'] . '</from>'.PHP_EOL;
			if(isset($item['to']))
			$xml .= '<to>' . $item['to'] . '</to>'.PHP_EOL;
			if(isset($item['in']))
			$xml .= '<in>' . $item['in'] . '</in>'.PHP_EOL;
			if(isset($item['out']))
			$xml .= '<out>' . $item['out'] . '</out>'.PHP_EOL;
			if(isset($item['amount']))
			$xml .= '<amount>' . $item['amount'] . '</amount>'.PHP_EOL;
			if(isset($item['minfee']))
			$xml .= '<minfee>' . $item['minfee'] . '</minfee>'.PHP_EOL;
			if(isset($item['fromfee']))
			$xml .= '<fromfee>' . $item['fromfee'] . '</fromfee>'.PHP_EOL;
			if(isset($item['tofee']))
			$xml .= '<tofee>' . $item['tofee'] . '</tofee>'.PHP_EOL;
			if(isset($item['minamount']))
			$xml .= '<minamount>' . $item['minamount'] . '</minamount>'.PHP_EOL;
			if(isset($item['maxamount']))
			$xml .= '<maxamount>' . $item['maxamount'] . '</maxamount>'.PHP_EOL;
			if(isset($item['param']))
			$xml .= '<param>' . $item['param'] . '</param>'.PHP_EOL;
			if(isset($item['city']))
			$xml .= '<city>' . $item['city'] . '</city>'.PHP_EOL;
			$xml .= '</item>'.PHP_EOL;
		} 
		$xml .= '</rates>'.PHP_EOL;
		return $xml;
	}
	$test[]=['from'=>'WNZ','to'=>'WNR','in'=>1,'out'=>'0.9767'];
	$test[]=['from'=>'WNR','to'=>'WNZ','in'=>1,'out'=>'1.2','city'=>'Dmitrov'];
	//file_put_contents('file.xml',generate($test));
	

	//die(get_page($url));
	$html = str_get_html(get_page($url));
$html = array_unique(array_merge($html->find('#bit_gateway_send option'),$html->find('#bit_gateway_receive option')));
	foreach ($html as $f) {
		$tmp['num'] = $f->value;
		$tmp['name'] = $f->plaintext;
		$tmp['name'] = isset($alias[$tmp['name']]) ? $alias[$tmp['name']] : $tmp['name'];
		$num[] = $tmp;
	}
	foreach ($num as $im) {
		$i = $im['num'];
		foreach($num as $jm) {
			$j = $jm['num'];
			$t = json_decode(get_page($url."/requests/bit_rates.php?gateway_send=$i&gateway_receive=$j"));
			$amount = get_page($url."/requests/bit_reserve.php?gateway_send=$i&gateway_receive=$j");
			$word = strpos($amount,' ');
			$amount = substr($amount,0,$word);
			$xml[] = [
				'from' => $im['name'],
				'to' => $jm['name'],
				'in' => $t->rate_from,
				'out' => $t->rate_to,
				'amount' => $amount
			];
		}
	}
	header('Content-type: text/xml');
if ($_GET['xml']=='file'){file_put_contents('rates.xml',generate($xml));}
	else {echo generate($xml);}
	