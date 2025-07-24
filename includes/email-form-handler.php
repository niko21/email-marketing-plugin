<?php
add_action('init', 'handle_email_form_submission');

function handle_email_form_submission() {
    if (!isset($_POST['email_form_submit']) || !isset($_POST['form_id'])) return;

    if (
        !isset($_POST['email_form_nonce_field']) ||
        !wp_verify_nonce($_POST['email_form_nonce_field'], 'email_form_nonce_action')
    ) {
        return;
    }

    global $wpdb;

    $forms_table = $wpdb->prefix . 'email_marketing_forms';
    $subs_table  = $wpdb->prefix . 'email_marketing';
    $tokens_table = $wpdb->prefix . 'email_marketing_tokens';

    $form_id = intval($_POST['form_id']);
    $modulo  = $wpdb->get_row($wpdb->prepare("SELECT * FROM $forms_table WHERE id = %d", $form_id));
    if (!$modulo) return;

    $campi = json_decode($modulo->campi);
    $dati  = ['form_id' => $form_id, 'attivo' => 0]; // attivo=0 in attesa conferma

    foreach ($campi as $campo) {
        $val = sanitize_text_field($_POST[$campo] ?? '');
        if ($campo === 'email' && !is_email($val)) return; // email non valida
        $dati[$campo] = $val;
    }

    // Inserisci iscritto
    $wpdb->insert($subs_table, array_merge($dati, [
        'data_iscrizione' => current_time('mysql')
    ]));
    $subscriber_id = $wpdb->insert_id;

    // === Genera token di conferma ===
    $token  = wp_generate_password(32, false);
    $expiry = gmdate('Y-m-d H:i:s', strtotime('+2 days'));
    $wpdb->insert($tokens_table, [
        'email_id' => $subscriber_id,
        'token'    => $token,
        'type'     => 'confirm',
        'expires'  => $expiry
    ]);

    // === Invia email di conferma ===
    if (!empty($dati['email'])) {
        $to      = sanitize_email($dati['email']);
        $subject = 'Conferma la tua iscrizione alla newsletter';
        $confirm_url = add_query_arg('em_confirm', $token, home_url('/'));
        $message = '<html><body>';
        $message .= '<h2>Ciao ' . esc_html($dati['nome'] ?? '') . '!</h2>';
        $message .= '<p>Grazie per esserti iscritto alla nostra newsletter. Sarai aggiornato con tutte le novità!</p>';
        $message .= '<p>Per completare la tua iscrizione clicca sul seguente link:<br><a href="' . esc_url($confirm_url) . '">Conferma iscrizione</a></p>';
        $message .= '<p>Se non sei stato tu, ignora questa email.</p>';
        $message .= '<p>A presto,<br>Il team di maiacar.it</p>';
        $message .= '</body></html>';
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        wp_mail($to, $subject, $message, $headers);
    }

    // Redirect con banner conferma invio (ma non conferma iscrizione)
    $ref = wp_get_referer();
    $base = $ref ? $ref : (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    while (ob_get_level()) ob_end_clean();
    wp_safe_redirect(add_query_arg(['em_success' => 1, 'form_id' => $form_id], $base));
    exit;
}
