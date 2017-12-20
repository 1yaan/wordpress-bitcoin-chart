jQuery(document).ready(function() {
  console.log( 'WP Bitcoin Chart javascript file ready.' );
  jQuery( '.wp-bitcoin-chart-refresh-button' ).on( 'click', function(e) {
    e.preventDefault();
    wp_bitcoin_chart_data( this );
  });
});

/**
 * WP Bitcoin Chart data.
 *
 * @param  Object target_this
 * @return void
 */
function wp_bitcoin_chart_data( target_this ) {
  var post_data = jQuery( target_this ).parents( 'form' ).serialize();
  post_data += '&action=wp_bitcoin_chart';
  post_data += '&_security=' + wp_bitcoin_chart_ajax._security;

  var post_id = jQuery( target_this ).parents( 'form' ).attr( 'field-name' );

  // canvasをローダー表示する
  // console.log( '#' + post_id );
  // jQuery( '#' + post_id ).hide().before( '<div class="wp_bitcoin_chart_loading_img_field has-text-centered"><img class="wp_bitcoin_chart_loading_img" src="' + wp_bitcoin_chart_ajax.loader_url + '"></div>');

  jQuery.ajax({
    url : wp_bitcoin_chart_ajax.ajax_url,
    type : 'post',
    dataType: 'json',
    cache : false,
    data : post_data,
  })
  .then(
    // Success callback
    function( res ) {
      // グラフの表示
      // jQuery( '.wp_bitcoin_chart_loading_img' ).remove();
      // jQuery( '#' + post_id ).show();

      var ctx = document.getElementById(post_id).getContext('2d');
    	var chart = new Chart(ctx, res['atts']['chart']);
      chart.update();

      console.log( 'ajax success' );
    },
    // Failed callback
    function( jqXHR, textStatus, errorThrown ) {
      // エラーメッセージを表示する
      jQuery( '.wp_bitcoin_chart_loading_img' ).remove();
      console.log( 'ajax failed' );
    }
  );
}
