<?php
##########################################################
## meta_ys.pl (Yomi-Search用メタ検索処理用ファイル)     ##
##########################################################

###   目次   ###
#(1)メタ検索処理(&meta)
#(2)メタ検索結果画面表示(&meta_page)
################

#(1)メタ検索処理(&meta)
##第一引数=>モード選択(選択表示=select/メタサーチ画面表示=meta_page)
##※使用する検索エンジンは$_GET["検索エンジン名"]は"on"を格納
##※使用する検索エンジンは$_GET["engine"]は使用検索エンジンがひとつの場合の検索エンジン名

function meta($T_mode, $cut=""){
	#$arg1=検索モード(select | meta_page)
	$T_word=$_GET['word'];

	# %engine------サーチエンジンのプログラムの場所（http://を取り除く)
	# %engine_top--サーチエンジンのトップページ
	# %engine_name--サーチエンジンの名称
	# %keyword-----検索キーワードの変数名及びキーワード
	# %option------検索オプション
	# ----------------------------初期設定----------------------------- #
	$K_pp=$_GET['word'];
	$K_pp2=$_GET['word'];
	$K_pp3=$_GET['word'];
	$K_euc=$_GET['word'];
	$K_book=$_GET['word'];
	$K_plus=$_GET['word'];
	$WORD_url=$_GET['word'];
	$Sjis=mb_convert_encoding($_GET['word'], "SJIS", "EUC-JP");
	$K_book=str_replace("-", "", $K_book);
	##メソッド等の設定=>$methodに格納
	if ($_GET['method'] == 'and') {
		$Myahoo='s';
		$Minfoseek='0';
		$Mgoo='MC';
		$Mlycos='and';
		$Mhihing='AND';
		$K_pp =str_replace(" ", "+and+", $K_pp);
		$K_pp2 =str_replace(" ", "+", $K_pp2);
		$K_pp3 =str_replace(" ", " and ", $K_pp3);
	}
	else {
		$Myahoo = 'w';
		$Minfoseek='1';
		$Mgoo='SC';
		$Mlycos='or';
		$Mhihing='OR';
		$K_pp =str_replace(" ", "+or+", $K_pp);
		$K_pp2 =str_replace(" ", "+or+", $K_pp2);
		$K_pp3 =str_replace(" ", " or ", $K_pp3);
	}
	##その他の設定
	$K_plus=str_replace(" ", "+", $K_plus);
	$WORD_url=str_replace("http://", "", $WORD_url);
	if($_GET['www']){$WORD_url ="www." . $WORD_url;}

	##URLエンコード
	$K_plus =urlencode($K_plus);
	//$_GET[word] =urlencode($_GET['word']);
	$K_pp =urlencode($K_pp);
	$K_pp2 =urlencode($K_pp2);
	$K_pp3 =urlencode($K_pp3);
	$K_euc =urlencode($K_euc);
	
	$xoops = str_replace("http://","",XOOPS_URL);

/*
	$name = array(
	  'xoops',
	  'yahoo',
	  'infoseek',
	  'google',
	  'goo',
	  'inetguide',
	  'excite',
	  'joy',
	  'csj',
	  'FRESHEYE',
	  'InfoNavigator',
	  'hihing',
	  'findx',
	  'yomimono',
	  'chance',
	  'vector',
	  'ys-link',

	  'yahoo_s',
	  'rakuten',

	  'hmv_a',
	  'hmv_t',

	  'bk1',

	  'bk1_i',
	  'amazon_i',

	  'com',
	  'cojp',
	);

	$engine = array(
	  'xoops'		=>	"$xoops/search.php",
	  'yahoo'		=>	'search.yahoo.co.jp/bin/search',
	  'infoseek'	=>	'www.infoseek.co.jp/Titles',
	  'google'		=>	'www.google.com/search',
	  'goo'			=>	'search.goo.ne.jp/web.jsp',
	  'inetguide'	=>	'inetg.com/cgi-bin/fsearch/fsearch.cgi',
	  'excite'		=>	'www.excite.co.jp/search.gw',
	  'joy'			=>	'search.netjoy.ne.jp/cgi-bin/joy/nph-search.cgi',
	  'csj'			=>	'www.csj.co.jp/whatsbest/admin/lookup.cgi',
	  'FRESHEYE'	=>	'search.fresheye.com/',
	  'InfoNavigator'=>	'para.cab.infoweb.ne.jp/cgi-bin/para',
	  'hihing'		=>	'www.my-idea.net/cgi-bin/hihing.cgi',
	  'findx'		=>	'findx.nikkeibp.co.jp/cgi-bin/outsearch',
	  'yomimono'	=>	'www.yomimono.co.jp/mag/search.cgi',
	  'chance'		=>	'211.2.202.6/chance.nsf/All+By+Date',
	  'vector'		=>	'search.download.yahoo.co.jp/bin/v_searchf',
	  'ys-link'		=>	'ys-link.com/search.cgi',

	  'yahoo_s'		=>	'search.shopping.yahoo.co.jp/search',
	  'rakuten'		=>	'search.rakuten.co.jp/search.cgi',

	  'hmv_a'		=>	'www.hmv.co.jp/search/searchresults.asp',
	  'hmv_t'		=>	'www.hmv.co.jp/search/searchresults.asp',

	  'bk1'			=>	'www.bk1.co.jp/cgi-bin/srch/srch_result_book.cgi/',

	  'bk1_i'		=>	'www.bk1.co.jp/cgi-bin/srch/srch_result_book.cgi/',
	  'amazon_i'	=>	"www.amazon.co.jp/exec/obidos/ASIN/$K_book/",

	  'com'			=>	"$WORD_url.com",
	  'cojp'		=>	"$WORD_url.co.jp",
	);

	$engine_top = array(
	  'xoops'		=>	"$xoops/xoops/",
	  'yahoo'		=>	'www.yahoo.co.jp/',
	  'infoseek'	=>	'www.infoseek.co.jp/',
	  'google'		=>	'www.google.com/intl/ja/',
	  'goo'			=>	'www.goo.ne.jp/',
	  'inetguide'	=>	'www.inetg.com/',
	  'excite'		=>	'www.excite.co.jp/',
	  'joy'			=>	'joyjoy.com/JOY.html',
	  'csj'			=>	'www.csj.co.jp/whatsbest/',
	  'FRESHEYE'	=>	'www.fresheye.com/index.html',
	  'InfoNavigator'=>	'infonavi.infoweb.ne.jp/',
	  'hihing'		=>	'www.hihing.com/',
	  'findx'		=>	'findx.nikkeibp.co.jp/',
	  'yomimono'	=>	'www.yomimono.co.jp/',
	  'chance'		=>	'www.chance-it.com/',
	  'vector'		=>	'www.vector.co.jp/',
	  'ys-link'		=>	'ys-link.com/',

	  'yahoo_s'		=>	'shopping.yahoo.co.jp/',
	  'rakuten'		=>	'www.rakuten.co.jp/',
	  'hmv_a'		=>	'www.hmv.co.jp/mu/',
	  'hmv_t'		=>	'www.hmv.co.jp/mu/',

	  'bk1'			=>	'www.bk1.co.jp/',

	  'bk1_i'		=>	'www.bk1.co.jp/',
	  'amazon_i'	=>	'www.amazon.co.jp/',

	  'com'			=>	'www.google.com/intl/ja/',
	  'cojp'		=>	'www.google.com/intl/ja/',
	);

	$engine_name = array(
	  'xoops'		=>	'このサイト内',
	  'yahoo'		=>	'YAHOO! JAPAN',
	  'infoseek'	=>	'Infoseek',
	  'google'		=>	'Google',
	  'goo'			=>	'goo',
	  'inetguide'	=>	'iNetGuide',
	  'excite'		=>	'Excite Japan',
	  'joy'			=>	'JOY',
	  'csj'			=>	'CSJ What\'s Best!',
	  'FRESHEYE'	=>	'フレッシュアイ',
	  'InfoNavigator'=>	'InfoNavigator',
	  'hihing'		=>	'HiHing',
	  'findx'		=>	'Find\'X',
	  'yomimono'		=>	'よみものサーチ',
	  'chance'		=>	'Chance It!',
	  'vector'		=>	'Vector',
	  'ys-link'		=>	'YS-Link',

	  'yahoo_s'		=>	'Yahoo!ショッピング',
	  'rakuten'		=>	'楽天市場',

	  'hmv_a'		=>	'HMV(アーティスト名検索)',
	  'hmv_t'		=>	'HMV(タイトル名検索)',

	  'bk1'			=>	'bk1',

	  'bk1_i'		=>	'bk1(ISBN)',
	  'amazon_i'	=>	'amazon.co.jp(ISBN)',

	  'com'			=>	'.com',
	  'cojp'		=>	'.co.jp',
	);

	$keyword = array(
	  'xoops'		=>	"query=".$_GET['word'],
	  'yahoo'		=>	"p=$K_plus",
	  'infoseek'	=>	"qt=$K_plus",
	  'google'		=>	"q=$K_pp2",
	  'goo'			=>	"MT=$K_euc",
	  'inetguide'	=>	"key=$K_plus",
	  'excite'		=>	"s=$K_plus",
	  'joy'			=>	"key=$_GET[word]",
	  'csj'			=>	"key=$K_plus",
	  'FRESHEYE'	=>	"kw=$K_pp",
	  'InfoNavigator'=>	"Querystring=$K_pp2",
	  'hihing'		=>	"Keywords=$_GET[word]",
	  'findx'		=>	"kw=$K_pp3",
	  'yomimono'	=>	"word=$K_plus",
	  'chance'		=>	"Query=$K_plus",
	  'vector'		=>	"p=$_GET[word]",
	  'ys-link'		=>	"word=$_GET[word]",

	  'yahoo_s'		=>	"cp=$_GET[word]",
	  'rakuten'		=>	"sitem=$_GET[word]",

	  'hmv_a'		=>	"keyword=$Sjis",
	  'hmv_t'		=>	"keyword=$Sjis",

	  'bk1'			=>	"ti=$_GET[word]&au=&idx=3",

	  'bk1_i'		=>	"idx=3&isbn=$K_book",
	  'amazon_i'	=>	'',

	  'com'			=>	'',
	  'cojp'		=>	'',
	);

	$option = array(
	  'xoops'		=>	'action=results',
	  'yahoo'		=>	"n=$_GET[hyouji]&w=$Myahoo",
	  'infoseek'	=>	"sv=JP&lk=noframes&rt=JG&qp=$method&nh=$_GET[hyouji]",
	  'google'		=>	"num=$_GET[hyouji]&hl=ja&ie=EUC-JP&oe=EUC-JP",
	  'goo'			=>	"SM=".($_GET['method'] == 'and'?"MC":"SC")."&DC=$_GET[hyouji]",
	  'inetguide'	=>	"from=0&n=20&index=netguide",
	  'excite'		=>	"lk=excite_jp&c=japan",
	  'joy'			=>	"",
	  'csj'			=>	"ope=and&Submit=%91%97%90M&max=$_GET[hyouji]&off=0",
	  'FRESHEYE'	=>	"term=monthly",
	  'InfoNavigator'=>	"Page=page_howlink&DB=DB&template=results_rbt.html&OPE=AND&Max=$_GET[hyouji]",
	  'hihing'		=>	"action=search&bool=$Mhihing",
	  'findx'		=>	"",
	  'yomimono'	=>	"default=%8C%9F%8D%F5",
	  'chance'		=>	"SearchView&SearchMax=0&SearchOrder=3&SearchWV=FALSE&SearchThesaurus=TRUE",
	  'vector'		=>	"y=y",
	  'ys-link'		=>	 "mode=search",

	  'yahoo_s'		=>	'',
	  'rakuten'		=>	'',

	  'hmv_a'		=>	'category=ARTISTS',
	  'hmv_t'		=>	'category=TITLE',

	  'bk1'			=>	'',

	  'bk1_i'		=>	'',
	  'amazon_i'	=>	'',

	  'com'			=>	'',
	  'cojp'		=>	'',
	);
*/
	//error_reporting(E_ALL);
	$data = @file("./pl/search.dat");
	if (is_array($data))
	{
		$name = array();
		foreach($data as $line)
		{
			if (strpos($line,"//") === 0 || strpos($line,"-") === 0) continue;
			$lines = explode("\t",str_replace(array("\n","\r"),"",$line));
			$name[] = str_replace('XOOPS_URL',XOOPS_URL,$lines[0]);
			$engine[$lines[0]] = str_replace("http://","",str_replace('XOOPS_URL',XOOPS_URL,$lines[3]));
			$engine_top[$lines[0]] = $lines[2];
			$engine_name[$lines[0]] = $lines[1];
			$option[$lines[0]] = (!empty($lines[5]))? "&".$lines[5] : "";
			// keyword の設定
			if (!empty($_GET['word']))
			{
				$word = $_GET['word'];
				if (!empty($lines[6]))
				{
					if ($lines[6] == "sjis") $word = mb_convert_encoding($_GET['word'], "SJIS", "EUC-JP");
					if ($lines[6] == "utf8") $word = mb_convert_encoding($_GET['word'], "UTF-8", "EUC-JP");
				}
				if ($_GET['method'] == 'and')
				{
					if (!empty($lines[7])) $word = str_replace(" ",$lines[7],$word);
				}
				else
				{
					if (!empty($lines[8])) $word = str_replace(" ",$lines[8],$word);
				}
				$word = $lines[4]."=".urlencode($word);
				if (!empty($_GET['hyouji']) && !empty($lines[9])) $option[$lines[0]] .= "&".$lines[9]."=".htmlspecialchars($_GET['hyouji']);
			}
			$keyword[$lines[0]] = $word;
		}
	}

	# ----------------------------メインルーチン----------------------- #

	if($T_mode == "select"){
		$tmp=$_GET['engine'];
		if (!$_GET['word']){
			location("http://$engine_top[$tmp]");
		}
		else{
			if($_GET['engine'] == "com" || $_GET['engine'] == "cojp"){ #?&をカット
				location("http://$engine[$tmp]");
			}
			else{
				location("http://$engine[$tmp]&$keyword[$tmp]");
			}
		}
	}
	else{
		$location_list=array();
		foreach ($name as $tmp){
			if($_GET[$tmp] == "on" || $T_mode == "meta_page")
			{
				if(!$_GET['word'])
				{
					array_push($location_list,"{$engine_name[$tmp]}<>http://{$engine_top[$tmp]}<>\n");
				}
				else
				{
					$url = str_replace("&","&amp;","http://{$engine[$tmp]}&{$keyword[$tmp]}{$option[$tmp]}");
					array_push($location_list,"{$engine_name[$tmp]}<>{$url}<>\n");
				}
			}
		}
	}

	if($cut == "on"){return $location_list;}
	if($T_mode == "meta_page"){meta_page($T_word, $location_list);}

	exit;
}

#(2)メタ検索結果画面表示(&meta_page)
function meta_page($T_word, $location_list){
	$_GET['word']=$T_word;
	PR_meta_page($location_list);
	
	exit;
}
?>