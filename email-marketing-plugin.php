<?php
/*
Plugin Name: Email Marketing Plugin
Description: Un semplice plugin WordPress per raccogliere Nome, Cognome ed Email, e inviare notifiche automatiche agli iscritti ogni volta che pubblichi un nuovo post.
Version: 3.0
Author: Nicolò Silanos
Author URI: https://ns-developer.it
Text Domain: email-marketing-plugin
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/admin-interface.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-subscribers.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-form.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-form-handler.php';

register_activation_hook(__FILE__, 'email_marketing_create_tables');

function email_marketing_create_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $email_table = $wpdb->prefix . 'email_marketing';
    $forms_table = $wpdb->prefix . 'email_marketing_forms';

    // Rimuove le tabelle esistenti per pulizia (solo per reinstallazione controllata)
    $wpdb->query("DROP TABLE IF EXISTS $email_table");
    $wpdb->query("DROP TABLE IF EXISTS $forms_table");

    // Tabella iscritti
    $sql1 = "CREATE TABLE $email_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        form_id mediumint(9) NOT NULL,
        nome varchar(100),
        cognome varchar(100),
        email varchar(100) NOT NULL,
        data_iscrizione datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Tabella moduli configurabili
    $sql2 = "CREATE TABLE $forms_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nome_form varchar(100) NOT NULL,
        campi text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql1);
    dbDelta($sql2);
}
