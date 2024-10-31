<?php
//Block direct access to the file
if ( !function_exists( 'add_action' ) ) {
	exit; 
}

require_once( 'vulnerabilities-table-class.php' );

//Load vulnerabilities
$plugin_vulnerabilities = array();
include_once('vulnerabilities.php');

//Check if installed plugins have vulnerabilities
$plugin_list = get_plugins();
$plugin_list_paths = array_keys($plugin_list);
$current_vulnerabilities = array();
$old_vulnerabilities = array();
foreach ($plugin_list_paths as &$value) {
	preg_match_all('/([a-z0-9\-]+)\//', $value, $plugin_path);
	if (isset($plugin_path[1][0])) {
		$plugin_path=$plugin_path[1][0];

		if (array_key_exists($plugin_path, $plugin_vulnerabilities)) {
			$plugin = get_plugin_data( WP_PLUGIN_DIR."/".$value, $markup = true, $translate = true );
			foreach ($plugin_vulnerabilities[$plugin_path] as &$vulnerability) {
				if ( version_compare( $plugin["Version"], $vulnerability["FirstVersion"], '>=') && version_compare( $plugin["Version"], $vulnerability["LastVersion"], '<=') ) {
					$current_vulnerabilities[] = array(
						'name' => '<a href="'.get_admin_url().'plugin-install.php?tab=plugin-information&#038;plugin='.$plugin_path.'&#038;TB_iframe=true&#038;width=600&#038;height=550" class="thickbox open-plugin-details-modal">'.$plugin["Name"].'</a>',
						'versions' => $vulnerability["FirstVersion"].'-'.$vulnerability["LastVersion"],
						'type' => ucwords( $vulnerability["TypeOfVulnerability"] ),
						'exploitation'=> "Data available when using the Plugin Vulnerabilites service.",
						'details' => '<a href="'.$vulnerability["URL"].'" target="_blank" rel="noopener noreferrer">View Details</a>'
					);
				}
				else
					$old_vulnerabilities[] = array(
						'name' => '<a href="'.get_admin_url().'plugin-install.php?tab=plugin-information&#038;plugin='.$plugin_path.'&#038;TB_iframe=true&#038;width=600&#038;height=550" class="thickbox open-plugin-details-modal">'.$plugin["Name"].'</a>',
						'versions' => $vulnerability["FirstVersion"].'-'.$vulnerability["LastVersion"],
						'type' => ucwords( $vulnerability["TypeOfVulnerability"] ),
						'exploitation'=> "Data available when using the Plugin Vulnerabilites service.",
						'details' => '<a href="'.$vulnerability["URL"].'" target="_blank" rel="noopener noreferrer">View Details</a>'
					);
			}
		}
	}
}

?>

<div class="wrap">
<h2><?php _e('Plugin Vulnerabilities', 'plugin-vulnerabilities' );?></h2>
<br />
<h3><?php _e('Results for plugins we have seen hackers attempting to exploit:', 'plugin-vulnerabilities' );?></h3>
<br />
<?php if ( !empty($current_vulnerabilities)  ) {
		echo "<h4><b>".__('Installed plugins that have known vulnerabilities in the installed version of the plugin:', 'plugin-vulnerabilities')."</b></h3>";
		$current_vulnerabilities_table = new Vulnerabilities_Table();
		$current_vulnerabilities_table->add_data($current_vulnerabilities);
		$current_vulnerabilities_table->prepare_items();
		$current_vulnerabilities_table->display();
	}
	else 
		echo "<h4><b>".__('No installed plugins have known vulnerabilities in the installed versions.', 'plugin-vulnerabilities')."</b></h3><br />";
	if ( !empty($old_vulnerabilities) ) {
		echo "<h4><b>".__('Installed plugins that have known vulnerabilities in other versions of the plugin:', 'plugin-vulnerabilities')."</b></h3>";
		$old_vulnerabilities_table = new Vulnerabilities_Table();
		$old_vulnerabilities_table->add_data( $old_vulnerabilities );
		$old_vulnerabilities_table->prepare_items();
		$old_vulnerabilities_table->display();
	}
	else 
		echo "<h4><b>".__('No installed plugins have known vulnerabilities in other versions.', 'plugin-vulnerabilities')."</b></h3>";
	echo "<br /><br /><h4><b>".__('You can use our <a href="https://wordpress.org/plugins/plugin-security-checker/">Plugin Security Checker plugin</a> to check if a plugin might contain additional security issues.', 'plugin-vulnerabilities')."</b></h3>";
?>

<br><br>
<hr>
<h3><?php _e('Sign Up For Our Plugin Vulnerabilities Service', 'plugin-vulnerabilities' );?></h3>
<p><?php _e('You can get alerted for known vulnerabilities in all the plugins you use, not just ones that we are already seeing evidence that hackers are targeting, when you <a href="https://www.pluginvulnerabilities.com/product/subscription/">sign up</a> for our Plugin Vulnerabilities service. As the data for that comes from checking with our service\'s API, you don\'t need to update the plugin to get alerted to new issues and you can have checks done as often as hourly.', 'plugin-vulnerabilities' );?></p>
<p><?php _e('Currently our service warns about vulnerabilities in the most recent version of plugins with over one million active installs that are still available in the Plugin Directory.', 'plugin-vulnerabilities' );?></p>
<p><?php _e('Through the service you also have access to a number of <a href="https://www.pluginvulnerabilities.com/why-plugin-vulnerabilities/">other important features</a> including the ability to <a href="https://www.pluginvulnerabilities.com/wordpress-plugin-security-reviews/">suggest/vote for which plugins we will do security reviews of</a> and help when dealing with a situation where you are using a plugin where the vulnerability has yet to be fixed (we can usually provide a temporary fix for the issue).', 'plugin-vulnerabilities' );?></p>
<p><?php _e('You can currently try the service for free when you use the coupon code "FirstMonthFree" when <a href="https://www.pluginvulnerabilities.com/product/subscription/">signing up</a>.', 'plugin-vulnerabilities' );?>
<?php _e(' We include a free lifetime subscription to the service when <a href="https://www.whitefirdesign.com/services/hacked-wordpress-website-cleanup.html">we do a WordPress Hack Cleanup</a>.', 'plugin-vulnerabilities' );?></p>
<p><?php _e("Once you have <a href='https://www.pluginvulnerabilities.com/product/subscription/'>signed up</a>, please enter your account's API details below:", 'plugin-vulnerabilities' );?></p>
<form  action="plugins.php?page=plugin-vulnerabilities" method="post">
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="api-license-key"><?php _e('API License Key:', 'plugin-vulnerabilities' );?></label></th>
<td><fieldset>
<input name="api-license-key" value="<?php if ( isset ( $_POST['api-license-key'] ) )
										   		echo esc_attr($_POST['api-license-key']); ?>">
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="api-license-email"><?php _e('API License Email:', 'plugin-vulnerabilities' );?></label></th>
<td><fieldset>
<input name="api-license-email" value="<?php if ( isset ( $_POST['api-license-email'] ) )
												echo esc_attr($_POST['api-license-email']); ?>">
</fieldset></td>
</tr>
</table>
<?php if ( isset ($activation_error ) ): ?>
	<span style="color:red">
	<?php  _e('Could not activate API license key.', 'plugin-vulnerabilities' );?>
	<br>
	<?php  echo __('Error Given', 'plugin-vulnerabilities').': '.$activation_error; ?>
	</span>
<?php endif; ?>
<?php wp_nonce_field('plugin_vulnerabilities','plugin_vulnerabilities'); ?>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Activate API License Key', 'plugin-vulnerabilities' );?>"  /></p>
</form>
