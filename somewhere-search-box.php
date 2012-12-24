<?php
/*
 Plugin Name: Somewhere search box
Plugin URI: http://elearn.jp/wpman/column/somewhere-search-box.html
Description: Search box widget add to the admin post editor.
Author: tmatsuur
Version: 1.0.0
Author URI: http://12net.jp/
*/

/*
 Copyright (C) 2012 tmatsuur (Email: takenori dot matsuura at 12net dot jp)
This program is licensed under the GNU GPL Version 2.
*/

define( 'SOMEWHERE_SEARCH_BOX_DOMAIN', 'somewhere-search-box' );
define( 'SOMEWHERE_SEARCH_BOX_DB_VERSION_NAME', 'somewhere-search-box-db-version' );
define( 'SOMEWHERE_SEARCH_BOX_DB_VERSION', '1.0.0' );

$plugin_somewhere_search_box = new somewhere_search_box();
class somewhere_search_box {
	function __construct() {
		global $pagenow;
		register_activation_hook( __FILE__ , array( &$this , 'init' ) );
		if ( in_array( $pagenow, array( 'index.php', 'post.php', 'post-new.php' ) ) ) {
			add_action( 'admin_init', array( &$this, 'setup' ) );
		}
	}
	function init() {
		if ( get_option( SOMEWHERE_SEARCH_BOX_DB_VERSION_NAME ) != SOMEWHERE_SEARCH_BOX_DB_VERSION ) {
			update_option( SOMEWHERE_SEARCH_BOX_DB_VERSION_NAME, SOMEWHERE_SEARCH_BOX_DB_VERSION );
		}
	}
	function setup() {
		global $pagenow;
		$title = __( 'Search Posts' );
		if ( in_array( $pagenow, array( 'index.php' ) ) )
			add_meta_box( 'meta_box_somewhere_search_box', $title, array( &$this, 'meta_box' ), 'dashboard', 'side', 'high' );
		else
			add_meta_box( 'meta_box_somewhere_search_box', $title, array( &$this, 'meta_box' ), 'post', 'side', 'high' );
	}
	function meta_box() {
		global $post;
		if ( isset( $post->post_status ) && $post->post_status != 'auto-draft' ) {
			$edit_post_link = '';
			$prev_post = get_previous_post();
			if ( isset( $prev_post->ID ) ) {
				$title = trim( $prev_post->post_title ) != ''? $prev_post->post_title: 'ID:'.$prev_post->ID;
				$edit_post_link = '&nbsp;<a href="?post='.intval( $prev_post->ID ).'&action=edit" title="Edit '.esc_attr( '"'.$title.'"' ).'" class="button">'.__( '&laquo; Previous' ).'</a>';
			}
			$next_post = get_next_post();
			if ( isset( $next_post->ID ) ) {
				$title = trim( $next_post->post_title ) != ''? $next_post->post_title: 'ID:'.$next_post->ID;
				$edit_post_link .= '&nbsp;<a href="?post='.intval( $next_post->ID ).'&action=edit" title="Edit '.esc_attr( '"'.$title.'"' ).'" class="button">'.__( 'Next &raquo;' ).'</a>';
			}
		}
?>
<input type="text" id="somewhere-search-input" name="s" value="" style="width: 70%;" />
<a class="button" href="javascript:post_searchbox( '<?php echo admin_url( 'edit.php' ); ?>' );"><?php _e( 'Search' ); ?></a>
<script type="text/javascript">
jQuery( '.add-new-h2' ).each( function () {
	jQuery(this).removeClass( 'add-new-h2' ).addClass( 'button' ).parent().addClass( 'wp-core-ui' );
<?php if ( $edit_post_link != '' ) { ?>
	jQuery(this).after( '<?php echo $edit_post_link; ?>' );
<?php } ?>
} );
jQuery( '#somewhere-search-input' ).keypress( function ( e ) {
	if ( e.which == 13 ) { post_searchbox( '<?php echo admin_url( 'edit.php' ); ?>' ); return false; }
} );
function post_searchbox( url ) {
	var post_search_input = jQuery.trim( jQuery( '#somewhere-search-input' ).val() );
	if ( post_search_input != '' )
		location.href = url+'?s='+encodeURI( post_search_input );
}
</script>
<?php
	}
}
?>