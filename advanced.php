<?php
//Block direct access to the file
if ( !function_exists( 'add_action' ) ) {
	exit; 
}
	
require_once( 'vulnerabilities-table-class.php' );

?>	

<div class="wrap">
<h2><?php _e('Plugin Vulnerabilities', 'plugin-vulnerabilities' );?></h2>
<?php
$installed_plugins = plugin_vulnerabilities_gather_plugins();
$plugin_api_data = plugin_vulnerabilities_prepare_plugin_data_for_api_request($installed_plugins);
if ( count($installed_plugins) > 100 )
	printf(__('The service only currently handles checking 100 plugins at a time and you have %s installed. Please let us know that we need to increase the number of plugins that can be checked by contacting us <a href="https://www.pluginvulnerabilities.com/contact/" target="_blank" rel="noopener noreferrer">here</a>.', 'plugin-vulnerabilities'), count($installed_plugins));
else {
	$results = plugin_vulnerabilities_send_request ('all', $plugin_api_data);
	//Store vulnerability data for use on Installed Plugins page
	if ( isset($results["Current Vulnerabilites"]) )
		update_option('plugin_vulnerabilities_vulnerability_data_cache',$results["Current Vulnerabilites"]);
	if ($results["Status"] == "Could not connect.")
		echo "<b>".__('Could not connect to service, error message given:', 'plugin-vulnerabilities')."<br> ".esc_attr($results[Error]);
	else if ($results["Status"] == "Website is down.")
		 _e( 'Could not check plugins for vulnerabilities because the Plugin Vulnerabilities service is down.' , 'plugin-vulnerabilities' );
	else if ($results["Status"] == "Not valid request.")
		 _e( 'Could not check plugins for vulnerabilities because something went wrong with the request sent to the Plugin Vulnerabilities service.' , 'plugin-vulnerabilities' );
	else if ($results["Status"] == "API license key is not active.")
		 _e( 'Could not check plugins for vulnerabilities because your API license key is not active.' , 'plugin-vulnerabilities' );
	else if ($results["Status"] == "API license key is active.") {
	
		$current_vulnerabilities = array();
		$old_vulnerabilities = array();
		$false_vulnerabilities = array();
			if ( is_int ($results["Last Update"]) )
				printf( '<div  class="plugin_vulnerabilities_date"><b>'.__('Most Recent Vulnerability Data Added On</b>: %s</div>', 'plugin-vulnerabilities' ),date ("F d, Y",$results["Last Update"]));
		if ($results["Current Vulnerabilites"] == "None" )
			echo "<h3><b>".__('No installed plugins have known vulnerabilities in the installed versions of the plugin.', 'plugin-vulnerabilities')."</b></h3>";
		else {
			echo "<h3><b>".__('Installed plugins that have known vulnerabilities in the installed version of the plugin:', 'plugin-vulnerabilities')."</b></h3>";
			foreach ($results["Current Vulnerabilites"] as $vulnerability) {
				$current_vulnerabilities[] = array(
					'name' => '<a href="'.get_admin_url().'plugin-install.php?tab=plugin-information&#038;plugin='.esc_attr($vulnerability["Path"]).'&#038;TB_iframe=true&#038;width=600&#038;height=550" class="thickbox open-plugin-details-modal">'.$installed_plugins[$vulnerability["Path"]]["Name"].'</a>',
					'versions' => esc_attr($vulnerability["First Version"]).'-'.esc_attr($vulnerability["Last Version"]),
					'type' => ucwords( esc_attr($vulnerability["Type Of Vulnerability"]) ),
					'exploitation'=> ucwords( esc_attr($vulnerability["Likelihood Of Exploitation"]) ),
					'details' => '<a href="'.esc_url($vulnerability["URL"]).'" target="_blank" rel="noopener noreferrer">View Details</a>'
				);
			}
			$current_vulnerabilities_table = new Vulnerabilities_Table();
			$current_vulnerabilities_table->add_data($current_vulnerabilities);
			$current_vulnerabilities_table->prepare_items();
			$current_vulnerabilities_table->display();
		}
		
		if ($results["Other Vulnerabilites"] == "None" )
			echo "<h3><b>".__('No installed plugins have known vulnerabilities in other versions of the plugin.', 'plugin-vulnerabilities')."</b></h3>";
		else {
			echo "<h3><b>".__('Installed plugins that have known vulnerabilities in other versions plugin:', 'plugin-vulnerabilities')."</b></h3>";
			foreach ($results["Other Vulnerabilites"] as $vulnerability) {
				$old_vulnerabilities[] = array(
					'name' => '<a href="'.get_admin_url().'plugin-install.php?tab=plugin-information&#038;plugin='.esc_attr($vulnerability["Path"]).'&#038;TB_iframe=true&#038;width=600&#038;height=550" class="thickbox open-plugin-details-modal">'.esc_attr($installed_plugins[$vulnerability["Path"]]["Name"]).'</a>',
					'versions' => esc_attr($vulnerability["First Version"]).'-'.esc_attr($vulnerability["Last Version"]),
					'type' => ucwords( esc_attr($vulnerability["Type Of Vulnerability"]) ),
					'exploitation'=> ucwords( esc_attr($vulnerability["Likelihood Of Exploitation"]) ),
					'details' => '<a href="'.esc_url( $vulnerability["URL"]).'" target="_blank" rel="noopener noreferrer">View Details</a>'
				);
			}
			$old_vulnerabilities_table = new Vulnerabilities_Table();
			$old_vulnerabilities_table->add_data($old_vulnerabilities);
			$old_vulnerabilities_table->prepare_items();
			$old_vulnerabilities_table->display();
		}
		if ($results["False Vulnerabilites"] != "None" ) {
			echo "<br /><br />";
			echo "<h3><b>".__('False reports of vulnerablities in installed plugins:', 'plugin-vulnerabilities')."</b></h3>";
			foreach ($results["False Vulnerabilites"] as $falseVulnerability) {
				$false_vulnerabilities[] = array(
					'name' => '<a href="'.get_admin_url().'plugin-install.php?tab=plugin-information&#038;plugin='.esc_attr($falseVulnerability["Path"]).'&#038;TB_iframe=true&#038;width=600&#038;height=550" class="thickbox open-plugin-details-modal">'.esc_attr($installed_plugins[$falseVulnerability["Path"]]["Name"]).'</a>',
					'type' => ucwords( esc_attr($falseVulnerability["Claimed Type Of Vulnerability"]) ),
					'details' => '<a href="'.esc_url( $falseVulnerability["URL"]).'" target="_blank" rel="noopener noreferrer">View Details</a>'
				);
			}
			$false_vulnerabilities_table = new False_Vulnerabilities_Table();
			$false_vulnerabilities_table->add_data($false_vulnerabilities);
			$false_vulnerabilities_table->prepare_items();
			$false_vulnerabilities_table->display();
		}
	}
	echo "<br /><br /><h4><b>".__('You can use our <a href="https://wordpress.org/plugins/plugin-security-checker/">Plugin Security Checker plugin</a> to check if a plugin might contain additional security issues.', 'plugin-vulnerabilities')."</b></h3>";
}
?>	
<br><br><hr><h3>Settings</h3>
<form action="plugins.php?page=plugin-vulnerabilities" method="post">
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="api-license-key"><?php _e('API License key:', 'plugin-vulnerabilities' );?></label></th>
<td><fieldset>
<span name="api-license-key"><?php if ( get_option('plugin_vulnerabilities_api_license_key') )
								echo esc_attr(get_option('plugin_vulnerabilities_api_license_key'));
							 ?></span>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="api-license-key"><?php _e('API License Email:', 'plugin-vulnerabilities' );?></label></th>
<td><fieldset>
<span name="api-license-email"><?php if ( get_option('plugin_vulnerabilities_api_license_email') )
								echo esc_attr(get_option('plugin_vulnerabilities_api_license_email'));
							 ?></span>
</fieldset></td>
</tr>
</table>
<?php wp_nonce_field( 'plugin_vulnerabilities' , 'plugin_vulnerabilities' ); ?>
<input type="hidden" name="deactivate" value="true">
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Deactivate API License Key' , 'plugin-vulnerabilities' ); ?>"  /></p>
</form>

<form action="plugins.php?page=plugin-vulnerabilities" method="post">
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="check-frequency"><?php _e('Check For Vulnerabilities Every:', 'plugin-vulnerabilities' )?></label></th>
<td><fieldset>
<select name="check-frequency"><option value="hourly"
<?php if ( get_option('plugin_vulnerabilities_check_frequency') && (get_option('plugin_vulnerabilities_check_frequency')=='hourly') )
	echo 'selected';
?>
>
<?php _e( 'Hour', 'plugin-vulnerabilities' ); ?>
</option><option value="twicedaily"
<?php if ( ( !get_option('plugin_vulnerabilities_check_frequency') ) || (get_option('plugin_vulnerabilities_check_frequency') && (get_option('plugin_vulnerabilities_check_frequency')=='twicedaily') ) )
	echo 'selected';
?>
>
<?php _e( '12 Hours', 'plugin-vulnerabilities' ); ?>
</option><option value="daily"
<?php if ( get_option('plugin_vulnerabilities_check_frequency') && (get_option('plugin_vulnerabilities_check_frequency')=='daily') )
	echo 'selected';
?>
>
<?php _e( 'Day', 'plugin-vulnerabilities' ); ?>
</option></select>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="email-address"><?php _e('Custom Email Address for Alerts:', 'plugin-vulnerabilities' )?></label></th>
<td><fieldset>
<input name="email-address" value="<?php if ( get_option('plugin_vulnerabilities_email_address') )
											echo esc_attr(get_option('plugin_vulnerabilities_email_address'));
								   ?>">
</fieldset>
<?php	if ( !get_option('plugin_vulnerabilities_email_address') )
	echo '<span>'.__('Currently using admin email address set for the website: ' , 'plugin-vulnerabilities' ).get_option( 'admin_email' ).'</span>';
else if ( !is_email( get_option('plugin_vulnerabilities_email_address') ) )
	echo '<span style="color:red">'.__('Invalid email address', 'plugin-vulnerabilities' ).'.</span>';
?>
</td>
</tr>
</table>
<?php wp_nonce_field( 'plugin_vulnerabilities' , 'plugin_vulnerabilities' ); ?>

<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' , 'plugin-vulnerabilities' ); ?>"  /></p>
</form>