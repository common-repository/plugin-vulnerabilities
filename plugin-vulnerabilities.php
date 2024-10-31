<?php
/*
Plugin Name: Plugin Vulnerabilities
Plugin URI: https://www.pluginvulnerabilities.com/
Description: Alerts you when exploited vulnerabilities are in your installed plugins and provides access to our more comprehensive Plugin Vulnerabilities service.
Version: 2.0.69
Author: White Fir Design
Author URI: https://www.pluginvulnerabilities.com/
License: GPLv2
Text Domain: plugin-vulnerabilities
Domain Path: /languages

Copyright 2014-2017 White Fir Design

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; only version 2 of the License is applicable.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Block direct access to the file
if ( !function_exists( 'add_action' ) ) { 
	exit; 
} 

add_action( 'admin_init', 'plugin_vulnerabilities_admin_init' );
   
function plugin_vulnerabilities_admin_init() {
       wp_register_style( 'PluginVulnerabilitiesStylesheet', plugins_url('stylesheet.css', __FILE__) );
}

function plugin_vulnerabilities_admin_styles() {
       wp_enqueue_style( 'PluginVulnerabilitiesStylesheet' );
}
add_action( 'admin_print_styles-plugin-install.php', 'plugin_vulnerabilities_admin_styles' );

function plugin_vulnerabilities_init() {
	load_plugin_textdomain( 'plugin-vulnerabilities', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('init', 'plugin_vulnerabilities_init');


function plugin_vulnerabilities_add_pages() {
	$page = add_plugins_page( 'Plugin Vulnerabilities', 'Plugin Vulnerabilities', 'manage_options', 'plugin-vulnerabilities', 'plugin_vulnerabilities_page'  );
}
add_action('admin_menu', 'plugin_vulnerabilities_add_pages');

add_action("after_plugin_row", 'plugin_vulnerabilities_add_plugin_row', 10, 3 );

function plugin_vulnerabilities_add_plugin_row ( $plugin_file, $plugin_data, $status ) {

	//Load vulnerabilities
	if (!isset($GLOBALS['plugin_vulnerabilities'])) {
		if (get_option('plugin_vulnerabilities_api_license_key') && get_option('plugin_vulnerabilities_vulnerability_data_cache') ) {
			$vulnerability_cache = get_option('plugin_vulnerabilities_vulnerability_data_cache');
			$GLOBALS['plugin_vulnerabilities'] = array();
			if ($vulnerability_cache == "None" )
				return;
			else {
				foreach ($vulnerability_cache as $vulnerability) {
					if ( !array_key_exists ($vulnerability["Path"],$GLOBALS['plugin_vulnerabilities']) )
						$GLOBALS['plugin_vulnerabilities'][$vulnerability["Path"]] =array();
					array_push($GLOBALS['plugin_vulnerabilities'][$vulnerability["Path"]], array(
							"FirstVersion" => esc_attr($vulnerability["First Version"]),
							"LastVersion" => esc_attr($vulnerability["Last Version"]),
							"TypeOfVulnerability" => esc_attr($vulnerability["Type Of Vulnerability"]),
							"URL" => esc_url($vulnerability["URL"])
						)
					);
				}
			}				
		}
		else {
			$plugin_vulnerabilities;
			include_once('vulnerabilities.php');
			$GLOBALS['plugin_vulnerabilities'] = $plugin_vulnerabilities;
		}
	}

	//Check if installed plugins have vulnerabilities
	preg_match_all('/([a-z0-9\-]+)\//', $plugin_file, $plugin_path);
	if (isset($plugin_path[1][0])) {
		$plugin_path=$plugin_path[1][0];
		if (array_key_exists($plugin_path, $GLOBALS['plugin_vulnerabilities'])) {
			$plugin = get_plugin_data( WP_PLUGIN_DIR."/".$plugin_file, $markup = true, $translate = true );
			foreach ($GLOBALS['plugin_vulnerabilities'][$plugin_path] as &$vulnerability) {
				if ( version_compare( $plugin["Version"], $vulnerability["FirstVersion"], '>=') && version_compare( $plugin["Version"], $vulnerability["LastVersion"], '<=') ) {
					//Handle a/an in message
					if ( preg_match( '/^[aeiou]/' , substr($vulnerability["TypeOfVulnerability"], 0, 1) ) ) {
						echo '<tr style="background-color: #FF8080;"><td colspan="3"><div>';
						echo sprintf(__('The %1$s plugin has an %2$s vulnerability.', 'plugin-vulnerabilities'), $plugin["Name"], '<a href="'.$vulnerability["URL"].'" target="_blank" rel="noopener noreferrer">'.$vulnerability["TypeOfVulnerability"].'</a>');
						echo '</div></td></tr>';	
					}
					else {
						echo '<tr style="background-color: #FF8080;"><td colspan="3"><div>';
						echo sprintf(__('The %1$s plugin has a %2$s vulnerability.', 'plugin-vulnerabilities'), $plugin["Name"], '<a href="'.$vulnerability["URL"].'" target="_blank" rel="noopener noreferrer">'.$vulnerability["TypeOfVulnerability"].'</a>');
						echo '</div></td></tr>';	
					}				}
			}
		}
	}
}

function plugin_vulnerabilities_page() {

	//Store submitted data
	if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && wp_verify_nonce($_POST['plugin_vulnerabilities'],'plugin_vulnerabilities') ) {
		if ( isset( $_POST['api-license-key'] ) && isset( $_POST['api-license-email'] ) ) {
		
			//clean up input
			while (substr($_POST['api-license-key'], 0, 1) === " ")
				 $_POST['api-license-key'] = substr_replace($_POST['api-license-key'],"",0,1);
			while (substr($_POST['api-license-key'], -1) === " ")
				 $_POST['api-license-key'] = substr_replace($_POST['api-license-key'],"",-1);
			while (substr($_POST['api-license-email'], 0, 1) === " ")
				 $_POST['api-license-email'] = substr_replace($_POST['api-license-email'],"",0,1);
			while (substr($_POST['api-license-email'], -1) === " ")
				 $_POST['api-license-email'] = substr_replace($_POST['api-license-email'],"",-1);
			$instance = trim( wp_generate_password( 12, false ) );
			$response = wp_remote_post( 'https://www.pluginvulnerabilities.com/?wc-api=am-software-api', array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array( 'email' => $_POST['api-license-email'], 'licence_key' => $_POST['api-license-key'], 'request' => 'activation', 'product_id' => 'Subscription', 'instance' => $instance, 'platform' => get_site_url()),
				'cookies' => array()
    			)
			);
			if ( is_wp_error( $response ) )
				$activation_error = $response->get_error_message();
			else {
				$activation_response = json_decode($response["body"], true);
				if ( isset($activation_response['activated']) && $activation_response['activated'] == 'active' ) {
					update_option( 'plugin_vulnerabilities_api_license_key', $_POST['api-license-key'] );
					update_option( 'plugin_vulnerabilities_api_license_email', $_POST['api-license-email'] );
					update_option( 'plugin_vulnerabilities_api_license_instance', $instance );
					wp_clear_scheduled_hook( 'plugin_vulnerabilities_cron' );
					if (get_option('plugin_vulnerabilities_check_frequency') && ( get_option('plugin_vulnerabilities_check_frequency')=="hourly" || get_option('plugin_vulnerabilities_check_frequency')=="twicedaily" || get_option('plugin_vulnerabilities_check_frequency')=="daily" ) )
						wp_schedule_event( time(), get_option('plugin_vulnerabilities_check_frequency'), 'plugin_vulnerabilities_cron' );
					else 
						wp_schedule_event( time(), 'twicedaily', 'plugin_vulnerabilities_cron' );
				}
				else if ( isset($activation_response['error']) ) {
					$activation_error = $activation_response['error'];
				}
				else
					$activation_error = "Unknown Error";
			}
		}
		if ( isset( $_POST['deactivate'] ) ) {
			wp_remote_post( 'https://www.pluginvulnerabilities.com/?wc-api=am-software-api', array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => array( 'email' => get_option( 'plugin_vulnerabilities_api_license_email'), 'licence_key' => get_option( 'plugin_vulnerabilities_api_license_key'), 'request' => 'deactivation', 'product_id' => 'Subscription', 'instance' => get_option( 'plugin_vulnerabilities_api_license_instance'), 'platform' => get_site_url()),
				'cookies' => array()
    			)
			);
			delete_option( 'plugin_vulnerabilities_api_license_key');
			delete_option( 'plugin_vulnerabilities_api_license_email');
			delete_option( 'plugin_vulnerabilities_api_license_instance');
			if (!get_option('plugin_vulnerabilities_check_frequency') || ( get_option('plugin_vulnerabilities_check_frequency')!="hourly") ) {
				wp_clear_scheduled_hook( 'plugin_vulnerabilities_cron' );
				wp_schedule_event( time(), 'hourly', 'plugin_vulnerabilities_cron' );
			}
		}
		if ( isset( $_POST['email-address'] ) )
			update_option( 'plugin_vulnerabilities_email_address', $_POST['email-address'] );
		if ( isset( $_POST['check-frequency'] ) && ( $_POST['check-frequency']=="hourly" || $_POST['check-frequency']=="twicedaily" || $_POST['check-frequency']=="daily" ) ) {
			if ( ( get_option('plugin_vulnerabilities_check_frequency') && ( get_option('plugin_vulnerabilities_check_frequency')!= $_POST['check-frequency'] ) ) || ( !get_option('plugin_vulnerabilities_check_frequency') && $_POST['check-frequency'] != "twicedaily" ) ) {
				update_option( 'plugin_vulnerabilities_check_frequency', $_POST['check-frequency'] );
				wp_clear_scheduled_hook( 'plugin_vulnerabilities_cron' );
				wp_schedule_event( time(), get_option('plugin_vulnerabilities_check_frequency'), 'plugin_vulnerabilities_cron' );
			}
		}
	}

	wp_enqueue_script('plugin-install');
	add_thickbox();

	if (get_option('plugin_vulnerabilities_api_license_key'))
		include( 'advanced.php' );
	else 
		include( 'basic.php' );
}

//Handle activation
register_activation_hook( __FILE__, 'plugin_vulnerabilities_activation' );
function plugin_vulnerabilities_activation() {
	if (get_option('plugin_vulnerabilities_api_license_key')) {
		if (get_option('plugin_vulnerabilities_check_frequency') && ( get_option('plugin_vulnerabilities_check_frequency')=="hourly" || get_option('plugin_vulnerabilities_check_frequency')=="twicedaily" || get_option('plugin_vulnerabilities_check_frequency')=="daily" ) )
			wp_schedule_event( time(), get_option('plugin_vulnerabilities_check_frequency'), 'plugin_vulnerabilities_cron' );
		else 
			wp_schedule_event( time(), 'twicedaily', 'plugin_vulnerabilities_cron' );
	}
	else
		wp_schedule_event( time(), 'hourly', 'plugin_vulnerabilities_cron' );
}


//Cron
add_action( 'plugin_vulnerabilities_cron', 'plugin_vulnerabilities_cron' );

function plugin_vulnerabilities_cron() {

	if (get_option('plugin_vulnerabilities_api_license_key'))
		plugin_vulnerabilities_advanced_cron();
	else
		plugin_vulnerabilities_basic_cron();
}

function plugin_vulnerabilities_advanced_cron() {
	//Load previously alerted vulnerabilities
	if (get_option('plugin_vulnerabilities_email_alerts_sent'))
		$email_alerts_sent = get_option('plugin_vulnerabilities_email_alerts_sent');
	else
		$email_alerts_sent = array();
		
	$installed_plugins = plugin_vulnerabilities_gather_plugins();
	$plugin_api_data = plugin_vulnerabilities_prepare_plugin_data_for_api_request($installed_plugins);
	if ( count($installed_plugins) > 100 ) {
		$subject = sprintf(__('[%s] Plugin Vulnerabilities Could Not Run', 'plugin-vulnerabilities'), get_option( 'blogname' ));
		$message = sprintf(__('WordPress site: %s', 'plugin-vulnerabilities'),get_option( 'siteurl' ));
		$message .= "\n\n";
		$message .= sprintf(__('The Plugin Vulnerabilities service only currently handles checking 100 plugins at a time and you have %d installed. Please let us know that we need to increase the number of plugins that can be checked by contacting us at https://www.pluginvulnerabilities.com/contact/.','plugin-vulnerabilities'), count($installed_plugins));
		if ( get_option( 'plugin_vulnerabilities_email_address' ) )
			wp_mail( get_option( 'plugin_vulnerabilities_email_address' ), $subject, $message);
		else
			wp_mail( get_option( 'admin_email' ), $subject, $message);
	}
	else {
		$results = plugin_vulnerabilities_send_request ('current', $plugin_api_data);
		
		if ($results["Status"] == "API license key is active.") {
			update_option('plugin_vulnerabilities_vulnerability_data_cache',$results["Vulnerabilites"]);
			if ($results["Vulnerabilites"] != "None" ) {
				foreach ($results["Vulnerabilites"] as $vulnerability) {
					$name = $installed_plugins[$vulnerability["Path"]]["Name"];
					if ( (array_key_exists($name, $email_alerts_sent) && (!in_array($vulnerability["URL"], $email_alerts_sent[$name]))) || !array_key_exists($name, $email_alerts_sent) ) {
						$version = $installed_plugins[$vulnerability["Path"]]["Version"];
						$vulnerabilty = esc_attr($vulnerability["Type Of Vulnerability"]);
						$likelihoodOfExploitation = esc_attr($vulnerability["Likelihood Of Exploitation"]);
						$vulnerabiltyURL = esc_url($vulnerability["URL"]);
			
						$subject = sprintf(__('[%1$s] Vulnerability in Installed Version of %2$s', 'plugin-vulnerabilities'),get_option( 'blogname' ),$name);
						$message = sprintf(__('WordPress site: %s','plugin-vulnerabilities'), get_option( 'siteurl' ));
						$message .= "\n\n";
						//Handle a/an in message
						if ( preg_match( '/^[aeiou]/' , substr($vulnerabilty, 0, 1) ) )
							$message .= sprintf(__('The installed version of the %1$s plugin, %2$s, contains an %3$s vulnerability. We estimate the likelihood of the vulnerability being exploited to be %4$s. More details on the vulnerability can be found at %5$s.', 'plugin-vulnerabilities'), $name, $version, $vulnerabilty, $likelihoodOfExploitation, $vulnerabiltyURL);
						else
							$message .= sprintf(__('The installed version of the %1$s plugin, %2$s, contains a %3$s vulnerability. We estimate the likelihood of the vulnerability being exploited to be %4$s. More details on the vulnerability can be found at %5$s.', 'plugin-vulnerabilities'), $name, $version, $vulnerabilty, $likelihoodOfExploitation, $vulnerabiltyURL);
						$message .= "\n\n";
						$message .= __('If you have any questions about dealing with this vulnerability please get in touch with us at contact@pluginvulnerabilities.com.', 'plugin-vulnerabilities');
						$message .= "\n\n\n";
						$message .= __('Warning sent from the Plugin Vulnerabilities plugin.', 'plugin-vulnerabilities');
						if ( get_option( 'plugin_vulnerabilities_email_address' ) )
							wp_mail( get_option( 'plugin_vulnerabilities_email_address' ), $subject, $message);
						else
							wp_mail( get_option( 'admin_email' ), $subject, $message);
			
						if (!array_key_exists($name, $email_alerts_sent)) {
							$email_alerts_sent[$name] = array($vulnerability["URL"]);
						}
						else
							array_push($email_alerts_sent[$name], $vulnerability["URL"]);
						update_option('plugin_vulnerabilities_email_alerts_sent',$email_alerts_sent);
					}
				}
			}
		}
	}
}

function plugin_vulnerabilities_basic_cron() {
	if ( ! function_exists( 'get_plugins' ) )
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
    $plugin_file = basename( ( __FILE__ ) );

	if ( ( get_option('plugin_vulnerabilities_version') && version_compare( get_option('plugin_vulnerabilities_version'),$plugin_folder[$plugin_file]['Version'],'<') ) || (!get_option('plugin_vulnerabilities_version')) ) {
	
		//Load vulnerabilities
		$plugin_vulnerabilities;
			include_once('vulnerabilities.php');
		
		//Load previously alerted vulnerabilities
		if (get_option('plugin_vulnerabilities_email_alerts_sent'))
			$email_alerts_sent = get_option('plugin_vulnerabilities_email_alerts_sent');
		else
			$email_alerts_sent = array();
		
		//Check if installed plugins have vulnerabilities
		$plugin_list = get_plugins();
		$plugin_list_paths = array_keys($plugin_list);
		$current = "";
		$old = "";
		foreach ($plugin_list_paths as &$value) {
			preg_match_all('/([a-z0-9\-]+)\//', $value, $plugin_path);
			if (isset($plugin_path[1][0])) {
				$plugin_path=$plugin_path[1][0];
			
				if (array_key_exists($plugin_path, $plugin_vulnerabilities)) {
					$plugin = get_plugin_data( WP_PLUGIN_DIR."/".$value, $markup = true, $translate = true );
					foreach ($plugin_vulnerabilities[$plugin_path] as &$vulnerability) {
						if ( version_compare( $plugin["Version"], $vulnerability["FirstVersion"], '>=') && version_compare( $plugin["Version"], $vulnerability["LastVersion"], '<=') ) {
							if ( (array_key_exists($plugin["Name"], $email_alerts_sent) && (!in_array($vulnerability["URL"], $email_alerts_sent[$plugin["Name"]]))) || !array_key_exists($plugin["Name"], $email_alerts_sent) ) {
								$name = $plugin["Name"];
								$version = $plugin["Version"];
								$vulnerabilty = $vulnerability["TypeOfVulnerability"];
								$vulnerabiltyURL = $vulnerability["URL"];
								
								$subject = sprintf(__('[%1$s] Vulnerability in Installed Version of %2$s', 'plugin-vulnerabilities'),get_option( 'blogname' ),$name);
								$message = sprintf(__('WordPress site: %s','plugin-vulnerabilities'), get_option( 'siteurl' ));
								$message .= "\n\n";
								//Handle a/an in message
								if ( preg_match( '/^[aeiou]/' , substr($vulnerabilty, 0, 1) ) )
									$message .= sprintf(__('The installed version of the %1$s plugin, %2$s, contains an %3$s vulnerability. More details on the vulnerability can be found at %4$s.', 'plugin-vulnerabilities'), $name, $version, $vulnerabilty, $vulnerabiltyURL);
								else
									$message .= sprintf(__('The installed version of the %1$s plugin, %2$s, contains a %3$s vulnerability. More details on the vulnerability can be found at %4$s.', 'plugin-vulnerabilities'), $name, $version, $vulnerabilty, $vulnerabiltyURL);
								$message .= "\n\n";
								$message .= __('You can get alerted for known vulnerabilities in all of the plugins you use, not just ones that we have seen evidence that hackers are targeting, when you sign up for our Plugin Vulnerabilities service: https://www.pluginvulnerabilities.com/', 'plugin-vulnerabilities');
								$message .= "\n\n";
								$message .= __('As the data for that service comes from checking with our service\'s API, you don\'t need to update the plugin to get alerted to new issues and you can have checks done as often as hourly.', 'plugin-vulnerabilities');
								$message .= "\n\n\n";
								$message .= __('Currently our service warns about vulnerabilities in the most recent version of plugins with over one million active installs that are still available in the Plugin Directory.', 'plugin-vulnerabilities');
								$message .= "\n\n\n";
								$message .= __('Through the service you also have access to a number of other important features including the ability to suggest/vote for which plugins we will do security reviews of and help when dealing with a situation where you are using a plugin where the vulnerability has yet to be fixed (we can usually provide a temporary fix for the issue).', 'plugin-vulnerabilities');
								$message .= "\n\n\n";
								$message .= __('You can currently sign up for half off when you use the coupon code "HalfOff" when signing up.', 'plugin-vulnerabilities');
								$message .= "\n\n\n";
								$message .= __('Warning sent from the Plugin Vulnerabilities plugin.', 'plugin-vulnerabilities');

								wp_mail( get_option( 'admin_email' ), $subject, $message);
								
								if (!array_key_exists($plugin["Name"], $email_alerts_sent)) {
									$email_alerts_sent[$plugin["Name"]] = array($vulnerability["URL"]);
								}
								else
									array_push($email_alerts_sent[$plugin["Name"]], $vulnerability["URL"]);
								update_option('plugin_vulnerabilities_email_alerts_sent',$email_alerts_sent);
							}
						}
					}
				}
			}
		}
		update_option('plugin_vulnerabilities_version',$plugin_folder[$plugin_file]['Version']);
	}
}

register_deactivation_hook( __FILE__, 'plugin_vulnerabilities_deactivation' );
function plugin_vulnerabilities_deactivation() {
	wp_clear_scheduled_hook( 'plugin_vulnerabilities_cron' );
}

function plugin_vulnerabilities_gather_plugins () {
	if ( ! function_exists( 'get_plugins' ) )
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        
	$installed_plugins = array();
	$plugin_list = get_plugins();
	$plugin_list_paths = array_keys($plugin_list);

	foreach ($plugin_list_paths as &$value) {
		preg_match_all('/([a-z0-9\-]+)\//', $value, $plugin_path);
		if (isset($plugin_path[1][0])) {
			$plugin_path=$plugin_path[1][0];
			$plugin = get_plugin_data( WP_PLUGIN_DIR."/".$value, $markup = true, $translate = true );
			$installed_plugins[$plugin_path] = array ("Name" => $plugin["Name"],
													  "Version" => $plugin["Version"]
																   );
		}
	}
	return $installed_plugins;
}

function plugin_vulnerabilities_prepare_plugin_data_for_api_request ($plugins) {
	$pluging_api_data = array();
	foreach ($plugins as $name => $plugin_data) {
		$pluging_api_data[$name] = $plugin_data["Version"];
	}
	return $pluging_api_data;
}

function plugin_vulnerabilities_send_request ($request_type, $plugins) {
	$response = wp_remote_post( 'https://www.pluginvulnerabilities.com/api/', array(
	'method' => 'POST',
	'timeout' => 45,
	'redirection' => 5,
	'httpversion' => '1.0',
	'blocking' => true,
	'headers' => array(),
	'body' => array( 'site-url' => get_site_url(), 'api-license-key' => get_option('plugin_vulnerabilities_api_license_key'), 'api-license-email' => get_option('plugin_vulnerabilities_api_license_email'), 'api-license-instance' => get_option('plugin_vulnerabilities_api_license_instance'), 'request-type' => $request_type, 'plugins' => json_encode($plugins) ),
	'cookies' => array()
    )
);
	if ( is_wp_error( $response ) )
		return array ("Status" => "Could not connect.",
					  "Error" => $response->get_error_message()
					);
	else
		return json_decode($response["body"], true);
}

//Changes Plugin Details Page
function plugin_vulnerabilities_developer_advisory ( $res, $action, $args ) {
	if ( $action = "plugin_information" && isset ($res->sections['description']) ) {
		//Developer Advisories
		include_once( 'developer-advisories.php' );
		if ( array_key_exists($res->slug, $developer_advisories) )
			$res->sections['description']='<div class="notice notice-warning notice-alt"><p><strong>Warning:</strong> The developer of this plugin is the subject of a <a href="'.$developer_advisories[$res->slug].'" target="blank">security advisory</a> by Plugin Vulnerabilities.</p></div>'.$res->sections['description'];
		
		//Vulnerabilites Page
		$vulnerabilities = "";
		if (get_option('plugin_vulnerabilities_api_license_key')) {
			$plugin_api_data = array();
			$plugin_api_data[$res->slug]="";
			$results = plugin_vulnerabilities_send_request ('single', $plugin_api_data);
			if ($results["Status"] == "API license key is active." && $results["Vulnerabilites"] != "None") {
				$vulnerabilities .= '<ul>';
				foreach ($results["Vulnerabilites"] as $vulnerability) {
					//Add new Entry to Vulnerabilities Tab
					$vulnerabilities .= '<li>';
					$vulnerabilities .= '<a href="'.esc_url( $vulnerability["URL"]).'" target="_blank" rel="noopener noreferrer">'.ucfirst(esc_attr($vulnerability["Type Of Vulnerability"])).' vulnerability</a>';
					$vulnerabilities .= ' in versions ';
					$vulnerabilities .= esc_attr($vulnerability["First Version"]).'-'.esc_attr($vulnerability["Last Version"]);
					$vulnerabilities .= '</li>';
					//Show Warning on Description Page
					if ( version_compare( $res->version, $vulnerability["First Version"], '>=') && version_compare( $res->version, $vulnerability["Last Version"], '<=') ) {
						//Handle a/an in message
						if ( preg_match( '/^[aeiou]/' , substr($vulnerability["Type Of Vulnerability"], 0, 1) ) )
							$message = sprintf(__('The current version of this plugin contains an %1$s vulnerability. More details on the vulnerability can be found %2$shere%3$s.', 'plugin-vulnerabilities'), esc_attr($vulnerability["Type Of Vulnerability"]), '<a href="'.esc_url( $vulnerability["URL"]).'" target="_blank" rel="noopener noreferrer">','</a>');
						else
							$message = sprintf(__('The current version of this plugin contains a %1$s vulnerability. More details on the vulnerability can be found %2$shere%3$s.', 'plugin-vulnerabilities'), esc_attr($vulnerability["Type Of Vulnerability"]), '<a href="'.esc_url( $vulnerability["URL"]).'" target="_blank" rel="noopener noreferrer">','</a>');
						$res->sections['description']='<div class="notice notice-warning notice-alt plugin_vulnerabilities_current_version"><p><strong>Warning:</strong> '.$message.'</p></div>'.$res->sections['description'];
					}
				}
				$vulnerabilities .= '</ul>';
				$res->sections['vulnerabilities'] = $vulnerabilities;	
			}
		}
		else {
			$plugin_vulnerabilities;
			include_once('vulnerabilities.php');
			if ( array_key_exists($res->slug, $plugin_vulnerabilities) ) {
				$vulnerabilities .= '<ul>';
				foreach ($plugin_vulnerabilities[$res->slug] as &$vulnerability) {
					//Add new Entry to Vulnerabilities Tab
					$vulnerabilities .= '<li>';
					$vulnerabilities .= '<a href="'.$vulnerability["URL"].'" target="_blank" rel="noopener noreferrer">'.ucfirst($vulnerability["TypeOfVulnerability"]).' vulnerability</a>';
					$vulnerabilities .= ' in versions ';
					$vulnerabilities .= $vulnerability["FirstVersion"].'-'.$vulnerability["LastVersion"];
					$vulnerabilities .= '</li>';
					//Show Warning on Description Page
					if ( version_compare( $res->version, $vulnerability["FirstVersion"], '>=') && version_compare( $res->version, $vulnerability["LastVersion"], '<=') ) {
						//Handle a/an in message
						if ( preg_match( '/^[aeiou]/' , substr($vulnerability["TypeOfVulnerability"], 0, 1) ) )
							$message = sprintf(__('The current version of this plugin contains an %1$s vulnerability. More details on the vulnerability can be found %2$shere%3$s.', 'plugin-vulnerabilities'), $vulnerability["TypeOfVulnerability"], '<a href="'.$vulnerability["URL"].'" target="_blank" rel="noopener noreferrer">','</a>');
						else
							$message = sprintf(__('The current version of this plugin contains a %1$s vulnerability. More details on the vulnerability can be found %2$shere%3$s.', 'plugin-vulnerabilities'), $vulnerability["TypeOfVulnerability"], '<a href="'.$vulnerability["URL"].'" target="_blank" rel="noopener noreferrer">','</a>');
						$res->sections['description']='<div class="notice notice-warning notice-alt plugin_vulnerabilities_current_version"><p><strong>Warning:</strong> '.$message.'</p></div>'.$res->sections['description'];
					}
				}
				$vulnerabilities .= '</ul>';
				$res->sections['vulnerabilities'] = $vulnerabilities;	
			}
		}
	}

	return $res;
}
add_filter ( 'plugins_api_result', 'plugin_vulnerabilities_developer_advisory', 10, 3);