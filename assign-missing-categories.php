<?php
/*
Plugin Name: Assign Missing Categories
Plugin URI: http://sillybean.net/code/
Description: Assigns categories to posts incorrectly stripped of all categories (showing unlinked "Uncategorized" in Manage &rarr; Posts).
Version: 1.0
Author: Stephanie Leary
Author URI: http://sillybean.net/

Changelog:
1.0 (November 17, 2008)
	First release

Copyright 2008  Stephanie Leary  (email : steph@sillybean.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Hook for adding admin menus
//add_action('admin_menu', 'assign_missing_categories_add_pages');
add_action('admin_menu', 'assign_missing_categories_add_pages');

// action function for above hook
function assign_missing_categories_add_pages() {
    // Add a new submenu under Options:
	add_submenu_page('edit.php', 'Assign Missing Categories', 'Assign Missing Categories', 8, __FILE__, 'assign_missing_categories_options');
}

// displays the options page content
function assign_missing_categories_options() {
	
	// variables for the field and option names 
		$hidden_field_name = 'assign_missing_categories_submit_hidden';
	
		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if( $_POST[ $hidden_field_name ] == 'Y' ) {
			assign_missing_categories();
			// Put an options updated message on the screen ?>
			<div class="updated"><p><strong><?php _e('Categories assigned.'); ?></strong></p></div>
		<?php } // Now display the options editing screen ?>
	
    <div class="wrap">
    <?php if( $_POST[ $hidden_field_name ] != 'Y' ) { ?>
	<form method="post" id="assign_missing_categories_form">
    <h2><?php _e( 'Assign Missing Categories'); ?></h2>
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p>Press the button below to assign the default category to all posts that are not assigned to any categories.</p>

	<p class="submit">
	<input type="submit" name="submit" value="<?php _e('Assign the default category &raquo;'); ?>" />
	</p>
	</form>
    <?php } // if ?>
    
	<p><?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds.</p>
    </div>
    
<?php } // end function assign_missing_categories_options() 

function assign_missing_categories() {
	global $wpdb;
	// Read in existing option value from database
	$default = get_option('default_category');
	$cats = get_all_category_ids();
	$allposts = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'post'");	
	?>
	<div class="wrap">
	<h2><?php _e( 'Assign Missing Categories'); ?></h2>
	<?php
    foreach ($allposts as $thispost) {
		// clear from previous iteration
		$terms = array();
		$terms = wp_get_post_categories($thispost->ID);
		if (empty($terms)) { 
			_e("Post $thispost->ID has no categories assigned.");
			wp_set_post_categories($thispost->ID, $default);
			_e( " Assigned default category.<br />");
		}
		else _e( "Post $thispost->ID has a category assigned.<br />");
	}
	echo "</div>";
} 
?>