<?php
/*
Plugin Name: Email Marketing Plugin
Description: Un plugin semplice per raccogliere Nome, Cognome ed Email e inviare aggiornamenti sui nuovi post.
Version: 2.0
Author: Nicolò Silanos
Author URI: https://ns-developer.it
Text Domain: email-marketing-plugin
*/
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/admin-interface.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-subscribers.php'; // ✅ nuovo
require_once plugin_dir_path(__FILE__) . 'includes/email-form.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-form-handler.php';

register_activation_hook(__FILE__, 'email_marketing_create_tables');

function email_marketing_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table1 = $wpdb->prefix . 'email_marketing';
    $sql1 = "CREATE TABLE $table1 (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nome varchar(100),
        cognome varchar(100),
        email varchar(100) NOT NULL,
        data_iscrizione datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    $table2 = $wpdb->prefix . 'email_marketing_forms';
    $sql2 = "CREATE TABLE $table2 (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nome_form varchar(100) NOT NULL,
        campi text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql1);
    dbDelta($sql2);
}