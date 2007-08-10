<?php
$fp=fopen("pl/cfg.php", "w");
##環境設定変更用テンプレートファイル
$out = <<<EOM
<?php
#一般の共通設定
\$EST=array(
'host'=>XOOPS_DB_HOST, #MySQLのホスト名
'sqlid'=>XOOPS_DB_USER,#MySQLのユーザ名
'sqldb'=>XOOPS_DB_NAME,#MySQLのデータベース名
'sqlpass'=>XOOPS_DB_PASS, #MySQLのパスワード
'sqltb'=>XOOPS_DB_PREFIX.'_yomi_', #テーブルのプレフィックス
'pass'=>'$EST[pass]', #管理用パスワード(WEB上で設定後は暗号化されます)
'home'=>'$EST[home]', #サーチエンジンのトップへのパスorURL
'script'=>'$EST[script]', #yomi.phpのファイル名
'search'=>'$EST[search]', #検索用のPHPファイル名
'rank'=>'$EST[rank]', #ランキング用のPHPファイル名
'admin'=>'$EST[admin]', #管理処理用のPHPファイル名

'login_ip'=>'$EST[login_ip]', #管理メニューを実行できるIPアドレスの設定リスト

'top'=>'$EST[top]', #サーチエンジンのトップ(CGI=0/HTML=1)

##ユーザ権限設定
'user_change_kt'=>'$EST[user_change_kt]', #カテゴリ変更(可能=0/不可=1)
'user_check'=>'$EST[user_check]', #仮登録モードに(しない=0/する=1)

'hyouji'=>'$EST[hyouji]', #カテゴリの表示数

'log_path'=>'$EST[log_path]', #ログディレクトリのパス(URLは不可)
'temp_path'=>'$EST[temp_path]', #テンプレートディレクトリのパス(URLは不可)
'html_path'=>'$EST[html_path]', #HTMLファイル用ディレクトリのパス(URLは不可)

'html_path_url'=>'$EST[html_path_url]', #HTMLファイルへのURL

'cgi_path_url'=>'$EST[cgi_path_url]', #PHPファイルを置くディレクトリのURL

'img_path_url'=>'$EST[img_path_url]', #画像ファイルを置くディレクトリのURL

'temp_logfile'=>'$EST[temp_logfile]', #仮登録用ファイル

'search_name'=>'$EST[search_name]', #サーチエンジンの名称
'admin_name'=>'$EST[admin_name]', #管理人の名前
'admin_email'=>'$EST[admin_email]', #管理人のE-Mail
'admin_hp'=>'$EST[admin_hp]', #管理人のホームページのURL

'new_time'=>'$EST[new_time]', #新着・更新期間の日数

##名称設定
'name_new'=>'$EST[name_new]',
'name_renew'=>'$EST[name_renew]',
'name_m1'=>'$EST[name_m1]',
'name_m2'=>'$EST[name_m2]',
'name_rank'=>'$EST[name_rank]',
'name_rank_bf'=>'$EST[name_rank_bf]',
'name_rank_rui'=>'$EST[name_rank_rui]',
'name_rev'=>'$EST[name_rev]',
'name_rev_bf'=>'$EST[name_rev_bf]',
'name_rev_rui'=>'$EST[name_rev_rui]',

##メールの設定
'mail_to_admin'=>'$EST[mail_to_admin]', #管理人にメールを送信(しない=0/する=1)
'mail_to_register'=>'$EST[mail_to_register]', #登録者にメールを送信(しない=0/する=1)
'mail_new'=>'$EST[mail_new]', #新規登録完了メールを送信(しない=0/する=1)
'mail_ch'=>'$EST[mail_ch]', #登録内容変更完了メールを送信(しない=0/する=1)
'mail_temp'=>'$EST[mail_temp]', #仮登録完了メールを送信(しない=0/する=1)
'mail_pass'=>'$EST[mail_pass]', #パスワード変更メールを送信(しない=0/する=1)

're_pass_fl'=>'$EST[re_pass_fl]', #パスワード再発行を(しない=0/する=1)

'syoukai_br'=>'$EST[syoukai_br]', #紹介文の改行(無効=0/有効=1)

#標準のログ表示順(mark/id_new/id_old/time_new/time_old/ac_new/ac_old)
'defo_hyouji'=>'$EST[defo_hyouji]',

##ジャンプ処理
'location'=>'$EST[location]', #(Locationを使う=1/メタタグを使う=0)

##キーワードランキングの設定
'keyrank'=>'$EST[keyrank]', #(実施しない=0/実施する=1)
'keyrank_min'=>'$EST[keyrank_min]', #管理室で表示する最低数
'keyrank_kikan'=>'$EST[keyrank_kikan]', #集計期間(日数)
'keyrank_hyouji'=>'$EST[keyrank_hyouji]', #表示数
'keyrank_cut'=>'$EST[keyrank_cut]', #一日ごとに指定数以下のデータは削除

#人気(OUT)ランキングの設定
'rank_fl'=>'$EST[rank_fl]', #(実施しない=0/実施する=1)
'rank_min'=>'$EST[rank_min]', #ランクインさせる最低アクセス数
'rank_kikan'=>'$EST[rank_kikan]', #集計期間(日数)
'rank_time'=>'$EST[rank_time]', #ランキング更新頻度(時間)
'rank_best'=>'$EST[rank_best]', #ランキングデータの最大保持件数
'rank_ref'=>'$EST[rank_ref]', #集計対象のURL(rank.phpやyomi.phpを置くディレクトリのURL)の一部(指定しない場合は未記入)

#アクセス(IN)ランキングの設定
'rev_fl'=>'$EST[rev_fl]', #(実施しない=0/実施する=1)
'rev_min'=>'$EST[rev_min]', #ランクインさせる最低アクセス数
'rev_kikan'=>'$EST[rev_kikan]', #集計期間(日数)
'rev_best'=>'$EST[rev_best]', #ランキングデータの最大保持件数
'rev_url'=>'$EST[rev_url]', #アクセスランキング時のリンクジャンプ先URL

##管理人への通知フォームの設定
'no_link_min'=>'$EST[no_link_min]', #報告する最低値
'no_link_ip'=>'$EST[no_link_ip]', #通知を拒否するIPアドレス(の一部)リスト

##アクセスカウンタを(使用しない=0/使用する=1)
'count'=>'$EST[count]',

#下層カテゴリデータ(表示しない=0/表示する=1)
'kt_child_show'=>'$EST[kt_child_show]',

#検索窓を外部から使用許可するURL
'allow_search_form'=>'$EST[allow_search_form]',
);

EOM;
fputs($fp, $out);

##sub EST_regを更新
$out = <<<EOM
function EST_reg(){
#登録処理関係の設定(regist_ys.php)
#記入必須はFxxx=☆/制限文字数はMxxx=文字数
#カテゴリ選択上限=kt_max/選択下限=kt_min/二重URL登録=nijyu_url(可能=0/不可=1)
#禁止ワード=kt_no_word
#新規登録権限=no_regist(管理人のみ=1/すべての訪問者=0/登録ユーザーのみ=2)
#管理人のみが修正・削除できるモード=no_mente(ON=1/OFF=0)
#バナーURL登録項目=bana_url(ON=1/OFF=0)
#追加希望カテゴリ項目=add_kt(ON=1/OFF=0)/管理人へのコメント項目=to_admin(ON=1/OFF=0)
#新規登録時の相互リンク連絡項目=sougo(ON=1/OFF=0)
#登録者のメッセージ=look_mes(見る=1/見ない=0)
global \$EST_reg;
\$EST_reg=array(

EOM;
fputs($fp, $out);
ksort($EST_reg);
foreach($EST_reg as $key=>$val){
	fputs($fp, "'$key'=>'$val',\n");
}
fputs($fp,");\n}\n");

##%ganesを更新
fputs($fp,"\$ganes=array(\n");
foreach($ganes as $key=>$val){
	$val=str_replace(array(" ","'"), array("","\\'"), $val);
	fputs($fp, "'$key'=>'$val',\n");
}
fputs($fp, ");\n\n");

##sub gane_stを更新
$out = <<<EOM
function gane_st(){
##カテゴリ属性
#トップページに表示する(サブカテゴリの場合のみ)
global \$gane_top, \$gane_ref, \$gane_UR, \$gane_other;
\$gane_top=array(

EOM;
fputs($fp, $out);
foreach($gane_top as $key=>$val){
	fputs($fp, "'$key'=>'$val',\n");
}
$out = <<<EOM
);
#関連カテゴリ設定
#([例]'Aのカテゴリ番号'=>'Bのカテゴリ番号&Cのカテゴリ番号')
#の場合にはAのカテゴリを表示した際にBとCのカテゴリが関連カテゴリとして表示される
\$gane_ref=array(

EOM;
fputs($fp, $out);
foreach($gane_ref as $key=>$val){
	fputs($fp, "'$key'=>'$val',\n");
}
$out = <<<EOM
);
#訪問者が登録不可のカテゴリ
\$gane_UR=array(

EOM;
fputs($fp, $out);
foreach($gane_UR as $key=>$val){
	fputs($fp, "'$key'=>'$val',\n");
}
$out = <<<EOM
);
#その他のカテゴリに表示するカテゴリ
\$gane_other=array(

EOM;
fputs($fp, $out);
foreach($gane_other as $val){
	fputs($fp, "'$val',");
}
fputs($fp, ");\n}\n");

##sub gane_guideを更新
$out = <<<EOM
function gane_guide(){
##カテゴリ別の説明表示(「★」は必要なければ削除しても構いません)
global \$KTEX;
\$KTEX=array(

EOM;
fputs($fp, $out);
foreach($KTEX as $key=>$val){
	$val = str_replace("'","\\'",$val);
	fputs($fp, "'$key'=>'$val',\n");
}
fputs($fp, ");\n}\n");

##sub search_formを更新
$out = <<<EOM
##検索フォームの設定
function search_form(){

EOM;
fputs($fp, $out);
#ここに書く($PR_search_form)
fputs($fp, $PR_search_form);
fputs($fp, "} #end of &search_form\n");
##sub menu_barを更新
$out = <<<EOM
##メニューバーの設定
function menu_bar(){

EOM;
fputs($fp, $out);
#ここに書く($PR_menu_bar)
fputs($fp, $PR_menu_bar);
fputs($fp, "} #end of &menu_bar\n");
##sub head_spを更新
$out = <<<EOM
##メニューバーの設定
function head_sp(){

EOM;
fputs($fp, $out);
#ここに書く($PR_head_sp)
fputs($fp, $PR_head_sp);
fputs($fp, "} #end of &head_sp\n");
##sub foot_spを更新
$out = <<<EOM
##メニューバーの設定
function foot_sp(){

EOM;
fputs($fp, $out);
#ここに書く($PR_foot_sp)
fputs($fp, $PR_foot_sp);
fputs($fp, "} #end of &foot_sp\n");
fputs($fp, "?>");
fclose($fp);
?>