<?php
/**
 * Admin settings.
 *
 * @package   wp-bitcoin-chart
 * @author    1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @version   0.1
 * @copyright 1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license   GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

?>
<div class="wrap">

	<h2>WP Bitcoin Chart</h2>

	<?php
	if ( ! empty( $_POST ) and check_admin_referer( 'wp-bitcoin-chart-settings', 'wbc-nonce' ) ) {
		print_r($_REQUEST);
		$button_name = $_POST['Submit'];

		if ( 'Initialization' == $button_name ) {
			WP_Bitcoin_Chart::wp_bitcoin_chart_restart();
			?>
			<div class="updated fade">
				<p><strong>保存していたデータを全て削除し、初期化しました。</strong></p>
			</div>
			<?php
		}
		if ( 'Settings' == $button_name ) {
			if ( array_key_exists( 'wp_bitcoin_chart_css', $_POST) ) {
				$wp_bitcoin_chart_css = $_POST['wp_bitcoin_chart_css'];
			} else {
				$wp_bitcoin_chart_css = 0;
			}

			update_option( 'wp_bitcoin_chart_css', $wp_bitcoin_chart_css );
			?>
			<div class="updated fade">
				<p><strong>オプションを登録しました。</strong></p>
			</div>
			<?php
		}
	}

	$wp_bitcoin_chart_css = get_option( 'wp_bitcoin_chart_css' );
	?>

	<p>WP Bitcoin Chartは、ショートコードを使用することで簡単にBTC/JPYのグラフを投稿記事の中や固定ページに埋め込むことができるツールです。</p>

	<div class="wp_bitcoin_chart_settings_main">
		<section>
			<h3>ショートコード 例</h3>
			<pre>[wp_bitcoin_chart_view name="WPBITCHART2" op=1 hp=1 op_color="PINK" hp_color="PURPLE" tool_position="none"]</pre>
			<figure>
				<?php
				echo WP_Bitcoin_Chart::wp_bitcoin_chart_view_shortcode( array(
					'name'          => 'WPBITCHART2',
					'periods'       => WBC__DEFAULT_CHART_PERIODS,
					'op_color'      => 'PINK',
					'hp_color'      => 'PURPLE',
					'op'            => 1,
					'hp'            => 1,
					'from'          => date( 'Y-m-d', strtotime( '-1 month' ) ),
					'to'            => date( 'Y-m-d' ),
					'tool_position' => 'none', // none, top, bottom or both.
				) );
				?>
				<figcaption>上の例のショートコードから表示されるグラフ</figcaption>
			</figure>

			<h3>ショートコードで使用できるパラメーター</h3>
			<table class="table wp-list-table widefat">
				<thead>
					<tr>
						<th>オプション</th>
						<th>概要</th>
						<th>初期値</th>
						<th>入力できる値</th>
						<th>備考</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>name</td>
						<td>このショートコードの名前</td>
						<td>WPBITCHART</td>
						<td>半角英数字（小文字/大文字）</td>
						<td>それぞれのグラフで、UNIQUEになるように設定してください。</td>
					</tr>
					<tr>
						<td>periods</td>
						<td>データの間隔</td>
						<td>86400</td>
						<td>300, 1800, 3600, 86400の4種類を指定可能です。</td>
						<td>300秒=5分、1800秒=30分、3600秒=1時間、86400秒=1日です。</td>
					</tr>
					<tr>
						<td>op_color</td>
						<td>開始金額のグラフの色</td>
						<td>Red</td>
						<td>CSSで利用可能なカラーネームを指定可能です。</td>
						<td>
							<a href="http://www.htmq.com/color/colorname.shtml" target="_blank">参考</a><br>
							英名："Red", 16進表記："#FF0000"
						</td>
					</tr>
					<tr>
						<td>hp_color</td>
						<td>最高金額のグラフの色</td>
						<td>Green</td>
						<td>CSSで利用可能なカラーネームを指定可能です。</td>
						<td>
							<a href="http://www.htmq.com/color/colorname.shtml" target="_blank">参考</a><br>
							英名："Red", 16進表記："#FF0000"
						</td>
					</tr>
					<tr>
						<td>lp_color</td>
						<td>最低金額のグラフの色</td>
						<td>Blue</td>
						<td>CSSで利用可能なカラーネームを指定可能です。</td>
						<td>
							<a href="http://www.htmq.com/color/colorname.shtml" target="_blank">参考</a><br>
							英名："Red", 16進表記："#FF0000"
						</td>
					</tr>
					<tr>
						<td>cp_color</td>
						<td>終了金額のグラフの色</td>
						<td>Yellow</td>
						<td>CSSで利用可能なカラーネームを指定可能です。</td>
						<td>
							<a href="http://www.htmq.com/color/colorname.shtml" target="_blank">参考</a><br>
							英名："Red", 16進表記："#FF0000"
						</td>
					</tr>
					<tr>
						<td>vo_color</td>
						<td>取引量のグラフの色</td>
						<td>Magenta</td>
						<td>CSSで利用可能なカラーネームを指定可能です。</td>
						<td>
							<a href="http://www.htmq.com/color/colorname.shtml" target="_blank">参考</a><br>
							英名："Red", 16進表記："#FF0000"
						</td>
					</tr>
					<tr>
						<td>op</td>
						<td>開始金額のグラフを表示するかどうか</td>
						<td>0</td>
						<td>0=表示しない, 1=表示する</td>
						<td></td>
					</tr>
					<tr>
						<td>hp</td>
						<td>最大金額のグラフを表示するかどうか</td>
						<td>0</td>
						<td>0=表示しない, 1=表示する</td>
						<td></td>
					</tr>
					<tr>
						<td>lp</td>
						<td>最低金額のグラフを表示するかどうか</td>
						<td>0</td>
						<td>0=表示しない, 1=表示する</td>
						<td></td>
					</tr>
					<tr>
						<td>cp</td>
						<td>終了金額のグラフを表示するかどうか</td>
						<td>0</td>
						<td>0=表示しない, 1=表示する</td>
						<td></td>
					</tr>
					<tr>
						<td>vo</td>
						<td>取引量のグラフを表示するかどうか</td>
						<td>0</td>
						<td>0=表示しない, 1=表示する</td>
						<td></td>
					</tr>
					<tr>
						<td>from</td>
						<td>グラフをいつから表示するか</td>
						<td>今日から1ヶ月前</td>
						<td>"Y-m-d H:i"形式で入力してください。</td>
						<td></td>
					</tr>
					<tr>
						<td>to</td>
						<td>グラフをいつまで表示するか</td>
						<td>今日まで</td>
						<td>"Y-m-d H:i"形式で入力してください。</td>
						<td></td>
					</tr>
					<tr>
						<td>tool_position</td>
						<td>グラフ切替ツールをどこに表示するか</td>
						<td>top</td>
						<td>
							top=グラフの上<br>
							bottom=グラフの下<br>
							both=グラフの上下両方<br>
							none=表示しない
						</td>
						<td></td>
					</tr>
				</tbody>
			</table>

			<form action="" method="post">
				<?php
				// おまじない.
				wp_nonce_field( 'wp-bitcoin-chart-settings', 'wbc-nonce' );
				?>
				<h3>オプションの設定</h3>
				<?php
				$checked = checked( $wp_bitcoin_chart_css, 1 );
				echo <<<EOD
<table class="form-table">
	<tr valign="top">
		<th>独自のCSSを使用しますか？</th>
		<td>
			<label>使用します
				<input type="checkbox" name="wp_bitcoin_chart_css" {$checked} value="1">
			</label>
		</td>
	</tr>
</table>
EOD;
?>
				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Settings" /></p>

				<hr>

				<h3>データの初期化</h3>
				<p>ボタンをクリックして、データを初期化してください。</p>
				<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Initialization" /></p>
			</form>
		</section>
	</div>
	<div class="wp_bitcoin_chart_settings_right">
		<dl>
			<dt>Github<dt>
			<dd><a href="https://github.com/1yaan/wp-bitcoin-chart" target="_blank">1yaan/wp-bitcoin-chart</a></dd>
			<dt>Travis CI</dt>
			<dd><a href="https://travis-ci.org/1yaan/wp-bitcoin-chart" target="_blank">1yaan/wp-bitcoin-chart</a></dd>
		</dl>
	</div>
</div>

<style>
.wp_bitcoin_chart_settings_main {
	background: none repeat scroll 0 0 #F3F1EB;
	border: 1px solid #DEDBD1;
	padding: 10px;
	width: 750px;
	height: auto;
	float: left;
}

.wp_bitcoin_chart_settings_right {
	float: right;
	width: 222px;
}
</style>
