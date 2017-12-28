<?php
/**
 * Admin settings.
 *
 * @package   wp-bitcoin-chart
 * @author    1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @version   1.1.0
 * @copyright 1yaan, {@link https://github.com/1yaan https://github.com/1yaan}
 * @license   GPLv2 or later, {@link https://www.gnu.org/licenses/gpl.html https://www.gnu.org/licenses/gpl.html}
 */

?>
<div class="wrap">
<form action="" method="post">
<?php
// おまじない.
wp_nonce_field( 'wp-bitcoin-chart-settings', 'wbc-nonce' );
?>
	<h1 class-"wp-heading-inline">WP Bitcoin Chart</h1>
	<?php
	if ( ! empty( $_POST ) and check_admin_referer( 'wp-bitcoin-chart-settings', 'wbc-nonce' ) ) {
		$button_name = $_POST['Submit'];

		if ( 'Initialization' == $button_name ) {
			WBC_Common::wp_bitcoin_chart_restart();
			?>
			<div class="updated fade">
				<p><strong>保存していたデータを全て削除し、初期化しました。</strong></p>
			</div>
			<?php
		}
		if ( 'Settings' == $button_name ) {
			if ( array_key_exists( 'wp_bitcoin_chart_css', $_POST ) ) {
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
	} else {
		$wp_bitcoin_chart_css = get_option( 'wp_bitcoin_chart_css' );
	}
	?>
	<p>
		WP Bitcoin Chartは、ショートコードを使用することで簡単にBTC/JPYのグラフを投稿記事の中や固定ページに埋め込むことができるツールです。<br>
		詳しい説明は<a href="https://1yaan.github.io/wp-bitcoin-chart/" target="_blank">こちら</a>をご確認ください。
	</p>
	<hr class="wp-header-end">

	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div id="side-info-column" class="inner-sidebar">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">

				<div class="postbox ">
					<h2><span>オプションの設定</span></h2>
					<div class="inside">
						<div class="submitbox">
							<div>
								<label>独自のCSSを使用しますか？
								<?php
								$checked = checked( $wp_bitcoin_chart_css, 1, false );
								echo <<<EOD
<input type="checkbox" name="wp_bitcoin_chart_css" {$checked} value="1">
EOD;
?>
								</label>
								<br>
								<br>
								<input name="Submit" type="submit" class="button-primary" tabindex="4" value="Settings">
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>

				<div class="postbox ">
					<h2><span>データの初期化</span></h2>
					<div class="inside">
						<div class="submitbox">
							<div>
								<p>ボタンをクリックして、データを初期化してください。</p>
								<input name="Submit" type="submit" class="button-primary" tabindex="4" accesskey="p" value="Initialization">
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>

				<div class="postbox ">
					<h2><span>このプラグインについて</span></h2>
					<div class="inside">
						<div class="submitbox">
							<div>
								<dl>
									<dt>使い方の説明<dt>
									<dd><a href="https://1yaan.github.io/wp-bitcoin-chart/" target="_blank">GitHub Page</a></dd>
									<dt>ソースコード<dt>
									<dd><a href="https://github.com/1yaan/wp-bitcoin-chart" target="_blank">wp-bitcoin-chart</a></dd>
									<dt>Travis CI</dt>
									<dd><a href="https://travis-ci.org/1yaan/wp-bitcoin-chart" target="_blank">wp-bitcoin-chart</a></dd>
								</dl>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>

				<div id="contacttagsdiv" class="postbox ">
					<h2><span>PR</span></h2>
					<div class="inside">
						<div class="tagsdiv" id="flamingo_contact_tag">
							<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
							<!-- github-docs -->
							<ins class="adsbygoogle"
									style="display:block"
									data-ad-client="ca-pub-0119304545599366"
									data-ad-slot="8330348600"
									data-ad-format="auto"></ins>
							<script>
							(adsbygoogle = window.adsbygoogle || []).push({});
							</script>
						</div>
					</div>
				</div><!-- #contacttagsdiv -->
			</div><!-- #side-sortables -->
		</div><!-- #side-info-column -->

		<div id="post-body">
			<div id="post-body-content">
				<div id="normal-sortables">
					<div id="contactnamediv" class="postbox ">
						<h2><span>ショートコードについて</span></h2>
						<div class="inside">
							<h3>ショートコード 例</h3>
							<pre>[wp_bitcoin_chart_view name="WPBITCHART2" op=1 hp=1 op_color="PINK" hp_color="PURPLE" tool_position="none"]</pre>
							<figure>
								<?php
								$wbc_public = new WBC_Public();
								echo $wbc_public->wp_bitcoin_chart_view( array(
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
						</div>
					</div>
				</div>
				<div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
		</div><!-- #post-body-content -->
	</div>

</form>
</div>
