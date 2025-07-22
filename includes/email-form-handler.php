<?php

add_action('init', 'handle_email_form_submission');

function handle_email_form_submission()
{
    if (!isset($_POST['email_form_submit']) || !isset($_POST['form_id'])) return;

    global $wpdb;

    $form_id = intval($_POST['form_id']);
    $modulo = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}email_marketing_forms WHERE id = %d", $form_id));
    if (!$modulo) return;

    $campi = json_decode($modulo->campi);
    $dati = ['form_id' => $form_id]; // 🔁 aggiunto il campo form_id

    foreach ($campi as $campo) {
        $val = sanitize_text_field($_POST[$campo] ?? '');
        if ($campo === 'email' && !is_email($val)) return;
        $dati[$campo] = $val;
    }

    $wpdb->insert($wpdb->prefix . 'email_marketing', array_merge($dati, [
        'data_iscrizione' => current_time('mysql')
    ]));
}
