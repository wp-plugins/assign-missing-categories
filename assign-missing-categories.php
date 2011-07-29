<?php
/*
Plugin Name: Assign Missing Categories
Plugin URI: http://sillybean.net/code/
Description: Assigns categories to posts incorrectly stripped of all categories (showing unlinked "Uncategorized" under Posts).
Version: 1.2
Author: Stephanie Leary
Author URI: http://sillybean.net/

== Changelog ==
= 1.2 =
* stop using deprecated junk
* clean the term cache after assigning posts
* localized strings (July 29, 2011)
= 1.1 =
* user capability check (August 3, 2009)
= 1.0 =
* first release (November 17, 2008)

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

add_action('admin_menu', 'assign_missing_categories_add_pages');

function assign_missing_categories_add_pages() {
	add_submenu_page('edit.php', 'Assign Missing Categories', 'Assign Missing Categories', 'manage_categories', __FILE__, 'assign_missing_categories_options');
}

// displays the options page content
function assign_missing_categories_options() {
	if ( current_user_can('edit_posts') && current_user_can('edit_pages') ) {  
	// variables for the field and option names 
		$hidden_field_name = 'assign_missing_categories_submit_hidden';
	
		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if ( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
			assign_missing_categories();
			// Put an options updated message on the screen ?>
			<div class="updated"><p><strong><?php _e('Categories assigned.', 'assign-missing-categories'); ?></strong></p></div>
		<?php } // Now display the options editing screen ?>
	
    <div class="wrap">
    <?php if ( !isset($_POST[ $hidden_field_name ]) || $_POST[ $hidden_field_name ] != 'Y' ) { ?>
	<form method="post" id="assign_missing_categories_form">
    <h2><?php _e( 'Assign Missing Categories', 'assign-missing-categories'); ?></h2>
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

	<p>Press the button below to assign the default category to all posts that are not assigned to any categories.</p>

	<p class="submit">
	<input type="submit" name="submit" value="<?php _e('Assign the default category &raquo;', 'assign-missing-categories'); ?>" class="button-primary" />
	</p>
	</form>
    <?php } // if ?>
    
	<p><?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds.</p>
    </div>
    
<?php } // if user can
} // end function assign_missing_categories_options() 

function assign_missing_categories() {
	global $wpdb;
	// Read in existing option value from database
	$default = get_option('default_category');
	$allposts = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'post'");	
	?>
	<div class="wrap">
	<h2><?php _e( 'Assign Missing Categories', 'assign-missing-categories'); ?></h2>
	<?php
	flush();
    foreach ($allposts as $thispost) {
		// clear from previous iteration
		$terms = array();
		$terms = wp_get_post_categories($thispost->ID);
		if (empty($terms)) { 
			printf(__("Post %d has no categories assigned.", 'assign-missing-categories'), $thispost->ID);
			wp_set_post_categories($thispost->ID, $default);
			_e( " Assigned default category.<br />", 'assign-missing-categories');
		}
		else printf(__( "Post %d has a category assigned.<br />", 'assign-missing-categories'), $thispost->ID);
		flush();
	}
	clean_term_cache($default, 'post_category', true);
	echo "</div>";
} 

// i18n
$plugin_dir = basename(dirname(__FILE__)). '/languages';
load_plugin_textdomain( 'assign-missing-categories', WP_PLUGIN_DIR.'/'.$plugin_dir, $plugin_dir );
?>