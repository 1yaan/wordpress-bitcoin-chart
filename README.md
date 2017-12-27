WordPress Bitcoin Chart Plugin ( wp_bitcoin_chart.php )
========

WordPress Bitcoin Chart Plugin は、 WordPress のプラグインです。

誰でも簡単に、ショートコードを置くだけで、ビットコインのグラフを、 WordPress の記事や固定ページへ表示することができます。

Details and examples: https://1yaan.github.io/wp-bitcoin-chart/

Your feedback is highly appreciated. You can send requests and bug reports as [Issues on GitHub](https://github.com/1yaan/wp-bitcoin-chart).

## Requirements

* WordPress 4.7 or above.
* PHP 5.6+ or above.

## For users

The master branch is development version.
Instead you can get a stable version from [released tags](https://github.com/1yaan/wp-bitcoin-chart/releases).

## For developers

An automated testing status of *master* branch: [![Build Status](https://travis-ci.org/1yaan/wp-bitcoin-chart.svg?branch=master)](https://travis-ci.org/1yaan/wp-bitcoin-chart)

## Usage

### ショートコード

グラフを表示するショートコード.

  ```
  [wp_bitcoin_chart_view name="WPBITCHART2" op=1 hp=1 hp_color="PURPLE" op_color="PINK" lp_color="BLACK" cp_color="ORANGE"]
  [wp_bitcoin_chart_view name="WPBITCHART3" cp=1 lp=1 hp_color="GREEN" cp_color="YELLOW" hp_color="TURQUOISE"]
  ```

最終取引価格を表示するショートコード.

  ```
  [wp_bitcoin_chart_transaction_price]
  ```

24時間の相場変動を表示するショートコード.

  ```
  [wp_bitcoin_chart_market_price]
  ```

### Themeに直接挿入する場合

WordPress は do_shortcode という関数を準備してくれていますので、こちらをご利用ください。

  ```
  <?php echo do_shortcode('[wp_bitcoin_chart_view name="WPBITCHART2" op=1 hp=1 hp_color="PURPLE" op_color="PINK" lp_color="BLACK" cp_color="ORANGE"]'); ?>
  <?php echo do_shortcode('[wp_bitcoin_chart_view name="WPBITCHART3" cp=1 lp=1 hp_color="GREEN" cp_color="YELLOW" hp_color="TURQUOISE"]'); ?>
  ```

### CSSについて

表示されるチャートに独自のCSSを適用したい場合は、設定画面にて、「独自のCSSを使用しますか？」に「使用します」のチェックをつけて登録してください。

## License

[GNU General Public License version 2 or later](http://www.gnu.org/licenses/gpl-2.0.html)

Copyright (c) 2017- [@1yaan](https://twitter.com/1yaan)

The WordPress Popular Posts plugin is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

The WordPress Popular Posts plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with the WordPress Popular Posts plugin; if not, see [http://www.gnu.org/licenses](http://www.gnu.org/licenses/).

## Thanks

ビットコインのアイコン[こちら](http://icooon-mono.com/10328-%E7%99%BD%E6%8A%9C%E3%81%8D%E3%81%AE%E3%83%93%E3%83%83%E3%83%88%E3%82%B3%E3%82%A4%E3%83%B3%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B32/)から  
ICOOON MONO 商用利用可能なモノトーンのアイコン素材をフリー(無料)でダウンロードできる素材配布サイトです。  
http://icooon-mono.com/
ビットコインのアイコンを使用させていただきました！ありがとうございました！！
