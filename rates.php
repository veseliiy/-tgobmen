<?php header('Content-Type: text/xml'); ?>
<rates>
<?php
ob_start();
session_start();
error_reporting(0);
if(file_exists("./install.php")) {
	header("Location: ./install.php");
} 
include("includes/config.php");
$db = new mysqli($CONF['host'], $CONF['user'], $CONF['pass'], $CONF['name']);
if ($db->connect_errno) {
    echo "Неудалось соединиться с MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
}
$db->set_charset("utf8");
$settingsQuery = $db->query("SELECT * FROM bit_settings ORDER BY id DESC LIMIT 1");
$settings = $settingsQuery->fetch_assoc();
include("includes/functions.php");
include(getLanguage($settings['url'],null,null));

function decodeGateway($gateway) {
	if($gateway == "PayPal") { return 'PP'; }
	elseif($gateway == "Skrill") { return 'SKL'; }
	elseif($gateway == "WebMoney") { return 'WMZ'; }
	elseif($gateway == "Perfect Money") { return 'PM'; }
	elseif($gateway == "Payeer") { return 'PR'; }
	elseif($gateway == "AdvCash") { return 'ADVC'; }
	elseif($gateway == "OKPay") { return 'OK'; }
	elseif($gateway == "Entromoney") { return 'EM'; }
	elseif($gateway == "SolidTrust Pay") { return 'STP'; }
	elseif($gateway == "Neteller") { return 'NTLR'; } 
	elseif($gateway == "UQUID") { } 
	elseif($gateway == "BTC-e") { return 'BTCE'; }
	elseif($gateway == "Yandex Money") { return 'YAM'; }
	elseif($gateway == "QIWI") { return 'QW'; }
	elseif($gateway == "Payza") { return 'PA'; }
	elseif($gateway == "Bitcoin") {	return ''; }
	elseif($gateway == "Litecoin") {	return ''; }
	elseif($gateway == "Dogecoin") {	return ''; }
	elseif($gateway == "Dash") {	return ''; }
	elseif($gateway == "Peercoin") {	return ''; }
	elseif($gateway == "Ethereum") {	return ''; }
	elseif($gateway == "TheBillioncoin") {	return ''; }
	elseif($gateway == "Bank Transfer") { return 'WIRE'; }
	elseif($gateway == "Western Union") { return 'WUU'; }
	elseif($gateway == "Moneygram") { return 'MGE'; }
	else {
		return 'Unknown';
	}
}

$getsend = $db->query("SELECT * FROM bit_gateways ORDER BY id");
if($getsend->num_rows>0) {
	while($ss = $getsend->fetch_assoc()) {
		$gateway_send = $ss['id'];
		$getquery = $db->query("SELECT * FROM bit_gateways ORDER BY id");
		if($getquery->num_rows>0) {
			while($get = $getquery->fetch_assoc()) {
				$gateway_receive = $get['id'];
				$currency_from = gatewayinfo($gateway_send,"currency");
				$currency_to = gatewayinfo($gateway_receive,"currency");
				$fee = gatewayinfo($gateway_receive,"fee");
				$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
						if($query->num_rows>0) {
							$row = $query->fetch_assoc();
							$data['status'] = 'success';
							$rate_from = $row['rate_from'];
							$rate_to = $row['rate_to'];
						} else {
								if($currency_from == $currency_to) { 
									$fee = str_ireplace("-","",$fee);
									$calculate1 = (1 * $fee) / 100;
									$calculate2 = 1 - $calculate1;
									$rate_from = 1;
									$rate_to = $calculate2;
								} 
////////////////////
elseif(array_search($currency_to, array("42","420G","8BIT","ABY","AC","ACCI","ACOIN","ADN","ADZ","AEON","AGRS","ALN","AM","AMBER","AMP","ANAL","ANC","ANS","ANT","APC","APEX","APX","ARCH","ARCO","ARDR","ARG","ARI","ARK","ASC","AU","AUR","AXR","BABCOIN","BANX","BAY","BBR","BCAP","BCN","BCX","BCY","BELA","BEN","BET","BFX","BIC","BILS","BITB","BITBTC","BITCNY","BITGOLD","BITS","BITUSD","BITZ","BKS","BLC","BLITZ","BLK","BLOCK","BLU","BLZ","BNT","BOB","BOST","BQC","BRIT","BRK","BRX","BSTY","BTA","BTB","BTC","BTCD","BTCRY","BTCS","BTG","BTM","BTQ","BTS","BTX","BUK","BUN","BURST","BVC","BYC","C2","CACH","CAGE","CAIX","CANN","CAP","CASH","CAT","CBX","CC","CCN","CDN","CELL","CESC","CF","CGA","CKC","CLAM","CLOAK","CLR","CNL","CNMT","COINO","COL","COMM","CON","COOL","COV","CRACK","CRAIG","CRAVE","CRB","CREDIT","CREVA","CRT","CRW","CRYPT","CSC","CTM","CURE","CVC","CYP","CZC","DAO","DARK","DASH","DBL","DCR","DEBUNE","DEM","DEX","DGB","DGC","DGD","DICE","DIEM","DIME","DMD","DOGE","DOPE","DRKC","DSB","DVC","EAC","ECC","EDR","EFL","EKN","ELC","ELS","ELT","EMC","EMC2","EMD","ENRG","EOS","ERY","ESP2","ETH","EUC","EXC","EXCL","EXE","EXP","FAIR","FC2","FCN","FCT","FFC","FIBRE","FIC","FIMK","FLAP","FLDC","FLO","FLT","FRC","FRK","FRSH","FST","FTC","FUN","GAIA","GAM","GAME","GAP","GB","GBYTE","GCN","GDC","GEMZ","GEO","GHC","GLC","GLD","GLYPH","GML","GNO","GNT","GP","GRC","GRE","GRS","GSM","GUA","GUE","GUN","GUP","HAL","HAM","HBN","HEAT","HEX","HIRO","HKG","HPC","HTML5","HUC","HVC","HYP","HYPER","HZ","I0C","ICASH","ICB","ICN","IEC","IFC","INFX","INSANE","IOC","ION","ISR","IXC","J","JAY","JBS","JINN","JKC","JLH","JUDGE","KARM","KDC","KMD","KOBO","KORE","KR","KUMA","LDOGE","LEAF","LEO","LFO","LGD","LIQUID","LKK","LKY","LOG","LOT","LSD","LSK","LTB","LTBC","LTC","LTCD","LXC","LYC","MAID","MARYJ","MAX","MCN","MEC","MED","MEOW","METAL","MGW","MINT","MIOTA","MLN","MMC","MMNXT","MNC","MNE","MONA","MOON","MRKT","MRS","MRY","MTL","MTR","MUE","MUSE","MUSIC","MWC","MYR","MZC","NAS","NAUT","NAV","NBT","NEC","NEOS","NET","NKA","NLG","NMB","NMC","NMR","NOBL","NODE","NOTE","NOXT","NRB","NRS","NSR","NTC","NTR","NTRN","NVC","NXC","NXT","NXTI","NXTPRIVACY","NXTTY","NXTV","NYAN","NYC","OBITS","OC","OK","OMC","ONEC","OPAL","ORB","ORO","P7C","PANGEA","PHO","PHS","PIGGY","PINK","PIVX","PLNC","PLS","PLU","PMP","PND","POP","POST","POSW","POT","PPC","PRIVATEBET","PRT","PSEUD","PTC","PTS","PXC","PYC","QB","QBK","QCN","QORA","QRK","QSLV","QTL","QUARK","RADS","RBBT","RBIES","RBR","RBT","RBY","RDD","RED","RIC","RIN","RIPO","RLC","ROOT","ROS","RPC","RT2","RZR","SAFEX","SAT2","SBD","SC","SCORE","SCOT","SDC","SEC","SF0","SFR","SHA","SHADE","SHIFT","SHLD","SIB","SJCX","SKYNET","SLG","SLING","SLK","SLM","SLR","SMBR","SMC","SMLY","SNT","SOL","SOLE","SONG","SOON","SPA","SPHR","SPR","SPT","SRC","SSD","START","STEEM","STRAT","STV","SUPER","SWARM","SWIFT","SWT","SXC","SYNC","SYS","TAG","TAGR","TAK","TBC","TCO","TEK","TES","THC","TIME","TIPS","TIT","TIX","TKN","TKS","TOP","TOR","TP1","TRC","TRI","TRIG","TRK","TRON","TRST","TRUMP","TRUST","U","UBQ","UFO","UIS","ULTC","UNB","UNC","UNITY","UNO","URC","URO","USDE","USDT","UTC","UTIL","VDO","VGC","VIA","VIOR","VLT","VNL","VOOT","VOX","VPN","VRC","VRS","VSL","VTA","VTC","VTR","WAVES","WBB","WDC","WINGS","XAI","XAU","XAUR","XBC","XBS","XBY","XC","XCH","XCN","XCO","XCP","XCR","XDN","XDP","XDQ","XEM","XGR","XJO","XLB","XLM","XMG","XMR","XPD","XPM","XPY","XQN","XRP","XSI","XST","XTC","XVC","XVG","XWC","XXX","XZC","YAC","YBC","YOC","YUM","ZCC","ZCL","ZED","ZEIT","ZET","ZRC","ZS"))) {
						if(checkCryptoExchange($gateway_sendname,$gateway_receivename)) {
							$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
							if($query->num_rows>0) {
								$row = $query->fetch_assoc();
								$data['status'] = 'success';
								$rate_from = $row['rate_from'];
								$rate_to = $row['rate_to'];
							} else {
								$data['status'] = 'error';
								$data['msg'] = '-';
							}
						} else {
							$ch = curl_init();
										$url = "http://coincap.io/page/".$currency_to;
										// Disable SSL verification
										curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
										// Will return the response, if false it print the response
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										// Set the url
										curl_setopt($ch, CURLOPT_URL,$url);
										// Execute
										$result=curl_exec($ch);
										// Closing
										curl_close($ch);
										$json = json_decode($result, true);
								$price = $json['price_usd'];
								$price = currencyConvertor($price,"USD",$currency_from);
								$calculate1 = ($price * $fee) / 100;
								$calculate2 = $price + $calculate1;
								$calculate2 = number_format($calculate2, 7, '.', '');
								$rate_to = 1;
								$rate_from = $calculate2;
						}
					}elseif(array_search($currency_from, array("42","420G","8BIT","ABY","AC","ACCI","ACOIN","ADN","ADZ","AEON","AGRS","ALN","AM","AMBER","AMP","ANAL","ANC","ANS","ANT","APC","APEX","APX","ARCH","ARCO","ARDR","ARG","ARI","ARK","ASC","AU","AUR","AXR","BABCOIN","BANX","BAY","BBR","BCAP","BCN","BCX","BCY","BELA","BEN","BET","BFX","BIC","BILS","BITB","BITBTC","BITCNY","BITGOLD","BITS","BITUSD","BITZ","BKS","BLC","BLITZ","BLK","BLOCK","BLU","BLZ","BNT","BOB","BOST","BQC","BRIT","BRK","BRX","BSTY","BTA","BTB","BTC","BTCD","BTCRY","BTCS","BTG","BTM","BTQ","BTS","BTX","BUK","BUN","BURST","BVC","BYC","C2","CACH","CAGE","CAIX","CANN","CAP","CASH","CAT","CBX","CC","CCN","CDN","CELL","CESC","CF","CGA","CKC","CLAM","CLOAK","CLR","CNL","CNMT","COINO","COL","COMM","CON","COOL","COV","CRACK","CRAIG","CRAVE","CRB","CREDIT","CREVA","CRT","CRW","CRYPT","CSC","CTM","CURE","CVC","CYP","CZC","DAO","DARK","DASH","DBL","DCR","DEBUNE","DEM","DEX","DGB","DGC","DGD","DICE","DIEM","DIME","DMD","DOGE","DOPE","DRKC","DSB","DVC","EAC","ECC","EDR","EFL","EKN","ELC","ELS","ELT","EMC","EMC2","EMD","ENRG","EOS","ERY","ESP2","ETH","EUC","EXC","EXCL","EXE","EXP","FAIR","FC2","FCN","FCT","FFC","FIBRE","FIC","FIMK","FLAP","FLDC","FLO","FLT","FRC","FRK","FRSH","FST","FTC","FUN","GAIA","GAM","GAME","GAP","GB","GBYTE","GCN","GDC","GEMZ","GEO","GHC","GLC","GLD","GLYPH","GML","GNO","GNT","GP","GRC","GRE","GRS","GSM","GUA","GUE","GUN","GUP","HAL","HAM","HBN","HEAT","HEX","HIRO","HKG","HPC","HTML5","HUC","HVC","HYP","HYPER","HZ","I0C","ICASH","ICB","ICN","IEC","IFC","INFX","INSANE","IOC","ION","ISR","IXC","J","JAY","JBS","JINN","JKC","JLH","JUDGE","KARM","KDC","KMD","KOBO","KORE","KR","KUMA","LDOGE","LEAF","LEO","LFO","LGD","LIQUID","LKK","LKY","LOG","LOT","LSD","LSK","LTB","LTBC","LTC","LTCD","LXC","LYC","MAID","MARYJ","MAX","MCN","MEC","MED","MEOW","METAL","MGW","MINT","MIOTA","MLN","MMC","MMNXT","MNC","MNE","MONA","MOON","MRKT","MRS","MRY","MTL","MTR","MUE","MUSE","MUSIC","MWC","MYR","MZC","NAS","NAUT","NAV","NBT","NEC","NEOS","NET","NKA","NLG","NMB","NMC","NMR","NOBL","NODE","NOTE","NOXT","NRB","NRS","NSR","NTC","NTR","NTRN","NVC","NXC","NXT","NXTI","NXTPRIVACY","NXTTY","NXTV","NYAN","NYC","OBITS","OC","OK","OMC","ONEC","OPAL","ORB","ORO","P7C","PANGEA","PHO","PHS","PIGGY","PINK","PIVX","PLNC","PLS","PLU","PMP","PND","POP","POST","POSW","POT","PPC","PRIVATEBET","PRT","PSEUD","PTC","PTS","PXC","PYC","QB","QBK","QCN","QORA","QRK","QSLV","QTL","QUARK","RADS","RBBT","RBIES","RBR","RBT","RBY","RDD","RED","RIC","RIN","RIPO","RLC","ROOT","ROS","RPC","RT2","RZR","SAFEX","SAT2","SBD","SC","SCORE","SCOT","SDC","SEC","SF0","SFR","SHA","SHADE","SHIFT","SHLD","SIB","SJCX","SKYNET","SLG","SLING","SLK","SLM","SLR","SMBR","SMC","SMLY","SNT","SOL","SOLE","SONG","SOON","SPA","SPHR","SPR","SPT","SRC","SSD","START","STEEM","STRAT","STV","SUPER","SWARM","SWIFT","SWT","SXC","SYNC","SYS","TAG","TAGR","TAK","TBC","TCO","TEK","TES","THC","TIME","TIPS","TIT","TIX","TKN","TKS","TOP","TOR","TP1","TRC","TRI","TRIG","TRK","TRON","TRST","TRUMP","TRUST","U","UBQ","UFO","UIS","ULTC","UNB","UNC","UNITY","UNO","URC","URO","USDE","USDT","UTC","UTIL","VDO","VGC","VIA","VIOR","VLT","VNL","VOOT","VOX","VPN","VRC","VRS","VSL","VTA","VTC","VTR","WAVES","WBB","WDC","WINGS","XAI","XAU","XAUR","XBC","XBS","XBY","XC","XCH","XCN","XCO","XCP","XCR","XDN","XDP","XDQ","XEM","XGR","XJO","XLB","XLM","XMG","XMR","XPD","XPM","XPY","XQN","XRP","XSI","XST","XTC","XVC","XVG","XWC","XXX","XZC","YAC","YBC","YOC","YUM","ZCC","ZCL","ZED","ZEIT","ZET","ZRC","ZS"))) {
						if(checkCryptoExchange($gateway_sendname,$gateway_receivename)) {
							$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
							if($query->num_rows>0) {
								$row = $query->fetch_assoc();
								$data['status'] = 'success';
								$rate_from = $row['rate_from'];
								$rate_to = $row['rate_to'];
							} else {
								$data['status'] = 'error';
								$data['msg'] = '-';
							}
						} else {
							$ch = curl_init();
										$url = "http://coincap.io/page/".$currency_from;
										// Disable SSL verification
										curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
										// Will return the response, if false it print the response
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										// Set the url
										curl_setopt($ch, CURLOPT_URL,$url);
										// Execute
										$result=curl_exec($ch);
										// Closing
										curl_close($ch);
										$json = json_decode($result, true);
								$price = $json['price_usd'];
							$price = currencyConvertor($price,"USD",$currency_to);
							$calculate1 = ($price * $fee) / 100;
							$calculate2 = $price - $calculate1;
							$calculate2 = number_format($calculate2, 7, '.', '');
							$rate_from = 1;
							$rate_to = $calculate2;
						}
					}
////////////////////
elseif(checkCryptoExchange($gateway_sendname,$gateway_receivename)) {
									$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
									if($query->num_rows>0) {
										$row = $query->fetch_assoc();
										$data['status'] = 'success';
										$rate_from = $row['rate_from'];
										$rate_to = $row['rate_to'];
									} else {
										$data['status'] = 'error';
										$data['msg'] = '-';
									}
								} else {
									if(isCrypto($gateway_sendname) == "1" && isCrypto($gateway_receivename) == "0") {
										$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
										if($query->num_rows>0) {
											$row = $query->fetch_assoc();
											$data['status'] = 'success';
											$rate_from = $row['rate_from'];
											$rate_to = $row['rate_to'];
										} else {
											$data['status'] = 'error';
											$data['msg'] = '-';
										}
									} elseif(isCrypto($gateway_sendname) == "0" && isCrypto($gateway_receivename) == "1") {
										$query = $db->query("SELECT * FROM bit_rates WHERE gateway_from='$gateway_send' and gateway_to='$gateway_receive'");
										if($query->num_rows>0) {
											$row = $query->fetch_assoc();
											$data['status'] = 'success';
											$rate_from = $row['rate_from'];
											$rate_to = $row['rate_to'];
										} else {
											$data['status'] = 'error';
											$data['msg'] = '-';
										}
									} else {
										$rate_from = 1;
										$calculate = currencyConvertor($rate_from,$currency_from,$currency_to);
										$calculate1 = ($calculate * $fee) / 100;
										$calculate2 = $calculate - $calculate1;
										if($calculate2 < 1) { 
											$calculate = currencyConvertor($rate_from,$currency_to,$currency_from);
											$calculate1 = ($calculate * $fee) / 100;
											$calculate2 = $calculate + $calculate1;
											$rate_from = number_format($calculate2, 7, '.', '');
											$rate_to = 1;
										} else {
											$rate_to = number_format($calculate2, 7, '.', '');
										}
									}
								}
				}
				
				$reserve = gatewayinfo($gateway_receive,"reserve");
				$gatsend = decodeGateway(gatewayinfo($gateway_send,"name")).gatewayinfo($gateway_send,"currency");
				$gatreceive = decodeGateway(gatewayinfo($gateway_receive,"name")).gatewayinfo($gateway_receive,"currency");
				echo '<item>';
				echo '<from>'.$gatsend.'</from>';
				echo '<to>'.$gatreceive.'</to>';
				echo '<in>'.$rate_from.'</in>';
				echo '<out>'.$rate_to.'</out>';
				echo '<amount>'.$reserve.'</amount>';
				echo '</item>';
			}
		}
	}
}

mysqli_close($db);
?>
</rates>