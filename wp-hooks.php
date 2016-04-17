<?PHP
/*
Plugin Name: WP Hooks
Plugin URI: http://amwhalen.com/blog/projects/wp-hooks/
Description: Add JavaScript, CSS, meta tags, etc. to your header and footer.
Version: 1.0.6
Author: Andrew M. Whalen
Author URI: http://amwhalen.com
*/

/*  Copyright 2016  Andrew M. Whalen  (email : wp-hooks@amwhalen.com)

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

define('AMW_HOOKS_VERSION', '1.0.6');

/**
 * Echoes whatever the user wants in the header.
 */
function amw_hooks_head() { echo get_option('amw_hooks_head'); }

/**
 * Echoes whatever the user wants in the footer.
 */
function amw_hooks_footer() { echo  get_option('amw_hooks_footer'); }

/**
 * The hooks options page
 */
function amw_hooks_options_page() {

	// implode the config option keys for use in the page_options hidden field
	$plugin_options = implode(',', array_keys(amw_hooks_get_default_options()));

	?>
	<div class="wrap">

	<h2>Header and Footer Settings (WP Hooks)</h2>

	<p>
		Add JavaScript, CSS, meta tags, etc. to your header and footer.
	</p>

	<form method="post" action="options.php">

		<?php

		// WP and WPMU 2.7+ use settings_fields(), 2.1+ use wp_nonce_field()
		if (function_exists('settings_fields')) {

			settings_fields('amw-hooks-options');

		} else {

			wp_nonce_field('update-options');

			?>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="<?php echo $plugin_options; ?>" />
			<?php

		}

		?>

		<table class="form-table amw_hooks_options">

			<tr valign="top">
				<td>
					<h3>Header Content</h3>
					<textarea rows="10" cols="85" name="amw_hooks_head"><?php echo htmlentities(get_option('amw_hooks_head')); ?></textarea>
				</td>
			</tr>

			<tr valign="top">
				<td>
					<h3>Footer Content</h3>
					<textarea rows="10" cols="85" name="amw_hooks_footer"><?php echo htmlentities(get_option('amw_hooks_footer')); ?></textarea>
				</td>
			</tr>

		</table>

    	<p class="submit">
			<button class="button-primary"><?php _e('Save Changes','wphooks'); ?></button>
		</p>

	</form>

	<p><a href="http://amwhalen.com/blog/projects/wp-hooks/">WP Hooks</a> is by <a href="http://amwhalen.com">Andrew M. Whalen</a>.</p>

	</div>
	<?php

}

/**
 * Admin init
 */
function amw_hooks_init() {

	// register all options
	$opts = amw_hooks_get_default_options();
	foreach ($opts as $k=>$v) {
		register_setting('amw-hooks-options', $k);
	}

}

/**
 * Adds admin nav.
 */
function amw_hooks_admin() {

	add_options_page('Header and Footer (WP Hooks)', 'Header and Footer (WP Hooks)', 'edit_files', __FILE__, 'amw_hooks_options_page');

}

/**
 * Adds settings link in the plugin listing.
 */
function amw_hooks_filter_plugin_actions($links, $file){

	static $this_plugin;

	if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=wp-hooks/wp-hooks.php">' . __('Settings') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;

}

/**
 * Install this plugin
 */
function amw_hooks_install() {

	// add any new options, leaving old values untouched
	$opts = amw_hooks_get_default_options();
	foreach($opts as $k=>$v) {
		if (get_option($k) === FALSE) {
			add_option($k, $v);
		}
	}

}

/**
 * Uninstall this plugin
 */
function amw_hooks_uninstall() {

	// remove options
	$opts = amw_hooks_get_default_options();
	foreach($opts as $k=>$v) {
		delete_option($k);
	}

}

/**
 * Default options.
 */
function amw_hooks_get_default_options() {

	$opts = array(
		'amw_hooks_version' => AMW_HOOKS_VERSION,
		'amw_hooks_head' => '',
		'amw_hooks_footer' => ''
	);

	return $opts;

}

// install / uninstall
register_activation_hook(__FILE__, 'amw_hooks_install');
register_uninstall_hook(__FILE__, 'amw_hooks_uninstall');

// admin stuff
add_action('admin_init', 'amw_hooks_init');
add_action('admin_menu', 'amw_hooks_admin');
add_filter('plugin_action_links', 'amw_hooks_filter_plugin_actions', 10, 2);

// do the work of this plugin!
add_action('wp_head', 'amw_hooks_head');
add_action('wp_footer', 'amw_hooks_footer');
