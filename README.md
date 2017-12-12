# Wordpress Bitcoin Chart Plugin
This is WordPress plugin. This plugin views BTC/JPY chart by Chart.js

Github page
https://1yaan.github.io/wp-bitcoin-chart/


## ただいま作成中でっす！＼(^o^)／

## 以下、いろいろメモも入ってるけどあとでまとめます！！

参考

https://github.com/zedzedzed/table-of-contents-plus/tree/master/trunk
https://github.com/makotokw/wp-amazonjs
https://github.com/Automattic/amp-wp
https://github.com/cabrerahector/wordpress-popular-posts

Travis導入参考
https://firegoby.jp/archives/5909

Travis CI: https://travis-ci.org/1yaan/wp-bitcoin-chart

default 10min

ZOOM 10min(1day), 30min(3day), 1hour(1week), 1day(1month)

http://blog.shoby.jp/entry/2017/05/24/093733
http://uxmilk.jp/19351
https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=60&after=1512658800

秒計算

1 s *********1
1 m ********60
1 h *****3,600
1 d ****86,400
1 w ***604,800
1 m *2,592,000 ( 30 days )
1 y 31,536,000 ( 365 days )

cryptowatにてperiodsを設定しない場合に取得可能なデータ
60 ,180,300,900 ,1800,3600 ,7200,14400,21600, 43200,86400,259200,604800
1分,3分 ,5分,15分,30分 ,1時間,2時間, 4時間, 6時間,12時間,   1日,   3日,   7日

// 2017.1.2 10:10から、1日足のデータを取得
https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?after=1483319400

periods 300 (5分)
periods 1800 (30分)
periods 3600 (1時間)
periods 86400 (1日)

afterを保存しておく

wp_bitcoin_chart_check_periods_300 = 1483319400
wp_bitcoin_chart_check_periods_1800 = 1483319400
wp_bitcoin_chart_check_periods_3600 = 1483319400
wp_bitcoin_chart_check_periods_86400 = 1483319400

https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=300&after=1483319400
https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=1800&after=1483319400
https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=3600&after=1483319400
https://api.cryptowat.ch/markets/bitflyer/btcjpy/ohlc?periods=86400&after=1483319400

UNIX TIMESTAMP を取得しておき、3600秒毎にデータを取得するようにする。
http://blog.goo.ne.jp/xmldtp/e/95890e6dd83d76d601b66513bf3f1993

Chartjsをリアルタイムへ
http://blog.goo.ne.jp/xmldtp/e/95890e6dd83d76d601b66513bf3f1993
プラグインでAjaxを使う
https://wpdocs.osdn.jp/%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3%E3%81%A7_AJAX_%E3%82%92%E4%BD%BF%E3%81%86

## ショートコード

  ```
  [wp_bitcoin_chart_view name="WPBITCHART2" op=1 hp=1 hp_color="PURPLE" op_color="PINK" lp_color="BLACK" cp_color="ORANGE"]
  [wp_bitcoin_chart_view name="WPBITCHART3" cp=1 lp=1 hp_color="GREEN" cp_color="YELLOW" hp_color="TURQUOISE"]
  ```

ビットコインのアイコンこちらから
http://icooon-mono.com/10328-%E7%99%BD%E6%8A%9C%E3%81%8D%E3%81%AE%E3%83%93%E3%83%83%E3%83%88%E3%82%B3%E3%82%A4%E3%83%B3%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B32/
ICOOON MONO
商用利用可能なモノトーンのアイコン素材をフリー(無料)でダウンロードできる素材配布サイトです。
http://icooon-mono.com/
icooon-mono-logo.png
ビットコインのアイコンを使用させていただきました！ありがとうございました！！
