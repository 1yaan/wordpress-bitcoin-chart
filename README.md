# Wordpress Bitcoin Chart Plugin
This is WordPress plugin. This plugin views BTC/JPY chart by Chart.js

Github page
https://1yaan.github.io/wp-bitcoin-chart/

Travis CI
https://travis-ci.org/1yaan/wp-bitcoin-chart

## ショートコード

  ```
  [wp_bitcoin_chart_view name="WPBITCHART2" op=1 hp=1 hp_color="PURPLE" op_color="PINK" lp_color="BLACK" cp_color="ORANGE"]
  [wp_bitcoin_chart_view name="WPBITCHART3" cp=1 lp=1 hp_color="GREEN" cp_color="YELLOW" hp_color="TURQUOISE"]
  ```

## Themeに直接挿入する場合

WordPress は do_shortcode という関数を準備してくれていますので、こちらをご利用ください。

  ```
  <?php echo do_shortcode('[wp_bitcoin_chart_view name="WPBITCHART2" op=1 hp=1 hp_color="PURPLE" op_color="PINK" lp_color="BLACK" cp_color="ORANGE"]'); ?>
  <?php echo do_shortcode('[wp_bitcoin_chart_view name="WPBITCHART3" cp=1 lp=1 hp_color="GREEN" cp_color="YELLOW" hp_color="TURQUOISE"]'); ?>
  ```

## 謝辞

ビットコインのアイコン[こちら](http://icooon-mono.com/10328-%E7%99%BD%E6%8A%9C%E3%81%8D%E3%81%AE%E3%83%93%E3%83%83%E3%83%88%E3%82%B3%E3%82%A4%E3%83%B3%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B32/)から  
ICOOON MONO 商用利用可能なモノトーンのアイコン素材をフリー(無料)でダウンロードできる素材配布サイトです。  
http://icooon-mono.com/
ビットコインのアイコンを使用させていただきました！ありがとうございました！！
