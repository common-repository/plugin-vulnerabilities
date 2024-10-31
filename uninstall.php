<?php 
if( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) 
	exit;
delete_option('plugin_vulnerabilities_version');
delete_option('plugin_vulnerabilities_email_alerts');
delete_option('plugin_vulnerabilities_email_alerts_sent');
delete_option('plugin_vulnerabilities_api_license_key');
delete_option('plugin_vulnerabilities_api_license_email');
delete_option('plugin_vulnerabilities_api_license_instance');
delete_option('plugin_vulnerabilities_email_address');
delete_option('plugin_vulnerabilities_check_frequency');
delete_option('plugin_vulnerabilities_vulnerability_data_cache');