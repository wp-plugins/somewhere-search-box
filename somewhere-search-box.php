<?php
/*
 Plugin Name: Somewhere search box
Plugin URI: http://elearn.jp/wpman/column/somewhere-search-box.html
Description: Search box widget add to the admin post editor.
Author: tmatsuur
Version: 1.1.0
Author URI: http://12net.jp/
*/

/*
 Copyright (C) 2012-2013 tmatsuur (Email: takenori dot matsuura at 12net dot jp)
This program is licensed under the GNU GPL Version 2.
*/

define( 'SOMEWHERE_SEARCH_BOX_DOMAIN', 'somewhere-search-box' );
define( 'SOMEWHERE_SEARCH_BOX_DB_VERSION_NAME', 'somewhere-search-box-db-version' );
define( 'SOMEWHERE_SEARCH_BOX_DB_VERSION', '1.1.0' );

class somewhere_search_box {
	var $post_type;
	function __construct() {
		global $pagenow;
		register_activation_hook( __FILE__ , array( &$this , 'init' ) );
		if ( in_array( $pagenow, array( 'index.php', 'post.php', 'post-new.php' ) ) ) {
			add_action( 'admin_init', array( &$this, 'setup' ) );
			add_action( 'admin_footer', array( &$this, 'footer' ) );
		}
	}
	function init() {
		if ( get_option( SOMEWHERE_SEARCH_BOX_DB_VERSION_NAME ) != SOMEWHERE_SEARCH_BOX_DB_VERSION ) {
			update_option( SOMEWHERE_SEARCH_BOX_DB_VERSION_NAME, SOMEWHERE_SEARCH_BOX_DB_VERSION );
		}
	}
	function setup() {
		global $pagenow;
		$_title = __( 'Search Posts' );
		$this->post_type = '';
		if ( in_array( $pagenow, array( 'index.php' ) ) )
			add_meta_box( 'meta_box_somewhere_search_box', $_title, array( &$this, 'meta_box' ), 'dashboard', 'side', 'high' );
		else {
			if ( isset( $_GET['post_type'] ) )
				$this->post_type = $_GET['post_type'];
			else if ( isset( $_GET['post'] ) ) {
				$_post = get_post( $_GET['post'] );
				if ( isset( $_post->post_type ) )
					$this->post_type = $_post->post_type;
			}
			if ( $this->post_type != '' )
				$_title = get_post_type_object( $this->post_type )->labels->search_items;
			add_meta_box( 'meta_box_somewhere_search_box', $_title, array( &$this, 'meta_box' ), $this->post_type != ''? $this->post_type: 'post', 'side', 'high' );
		}
	}
	function meta_box() {
		$add_post_type_param = '';
		if ( $this->post_type != '' )
			$add_post_type_param = '&post_type='.$this->post_type;
?>
<input type="text" id="somewhere-search-input" name="s" value="" style="width: 70%;" />
<a class="button" href="javascript:post_searchbox( '<?php echo admin_url( 'edit.php' ); ?>' );"><?php _e( 'Search' ); ?></a>
<?php
	}
	function footer() {
		global $post;
		if ( isset( $post->post_status ) && $post->post_status != 'auto-draft' ) {
			$edit_post_link = '';
			$prev_post = get_previous_post();
			if ( isset( $prev_post->ID ) ) {
				$title = trim( $prev_post->post_title ) != ''? $prev_post->post_title: 'ID:'.$prev_post->ID;
				$edit_post_link = '&nbsp;<a href="?post='.intval( $prev_post->ID ).'&action=edit" title="'.esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ) .'" class="button">'.__( '&laquo; Previous' ).'</a>';
			}
			$next_post = get_next_post();
			if ( isset( $next_post->ID ) ) {
				$title = trim( $next_post->post_title ) != ''? $next_post->post_title: 'ID:'.$next_post->ID;
				$edit_post_link .= '&nbsp;<a href="?post='.intval( $next_post->ID ).'&action=edit" title="'.esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ).'" class="button">'.__( 'Next &raquo;' ).'</a>';
			}
		}
		$add_post_type_param = '';
		if ( $this->post_type != '' )
			$add_post_type_param = '&post_type='.$this->post_type;
?>
<script type="text/javascript">
jQuery(document).ready( function () {
	jQuery( '.add-new-h2' ).each( function () {
		jQuery(this).removeClass( 'add-new-h2' ).addClass( 'button' ).parent().addClass( 'wp-core-ui' );
		<?php if ( $edit_post_link != '' ) { ?>
			jQuery(this).after( '<?php echo $edit_post_link; ?>' );
		<?php } ?>
		} );
	jQuery( '#somewhere-search-input' ).keypress( function ( e ) {
		if ( e.which == 13 ) { post_searchbox( '<?php echo admin_url( 'edit.php' ); ?>' ); return false; }
	} );
} );
function post_searchbox( url ) {
	var post_search_input = jQuery.trim( jQuery( '#somewhere-search-input' ).val() );
	if ( post_search_input != '' )
		location.href = url+'?s='+encodeURI( post_search_input )+'<?php echo $add_post_type_param; ?>';
}
</script>
<?php
	}
}
$plugin_somewhere_search_box = new somewhere_search_box();
?>