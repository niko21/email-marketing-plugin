<?php
add_action('init', 'handle_email_form_submission');

function handle_email_form_submission()
{
    if (!isset($_POST['email_form_submit']) || !isset($_POST['form_id'])) return;

    if (
        !isset($_POST['email_form_nonce_field']) ||
        !wp_verify_nonce($_POST['email_form_nonce_field'], 'email_form_nonce_action')
    ) {
        return;
    }

    global $wpdb;

    $form_id = intval($_POST['form_id']);
    $modulo = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}email_marketing_forms WHERE id = %d",
        $form_id
    ));

    if (!$modulo) return;

    $campi = json_decode($modulo->campi);
    $dati = ['form_id' => $form_id];

    foreach ($campi as $campo) {
        $val = sanitize_text_field($_POST[$campo] ?? '');
        if ($campo === 'email' && !is_email($val)) return;
        $dati[$campo] = $val;
    }

    $wpdb->insert($wpdb->prefix . 'email_marketing', array_merge($dati, [
        'data_iscrizione' => current_time('mysql')
    ]));

    // Email di benvenuto (già presente)
    if (!empty($dati['email'])) {
        $to      = sanitize_email($dati['email']);
        $subject = 'Benvenuto nella nostra newsletter!';
        $message = '<html><body>';
        $message .= '<h2>Ciao ' . esc_html($dati['nome'] ?? '') . '!</h2>';
        $message .= '<p>Grazie per esserti iscritto alla nostra newsletter. Sarai aggiornato con tutte le novità!</p>';
        $message .= '<p>A presto,<br>Il team di ns-developer.it</p>';
        $message .= '</body></html>';
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        wp_mail($to, $subject, $message, $headers);
    }

    // ✅ Redirect con messaggio di conferma
    // Usa l'URL corrente se il referer non è disponibile (alcuni browser lo bloccano)
    $current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $ref        = wp_get_referer();
    $base       = $ref ? $ref : $current_url;

    // Pulisce eventuale output buffer prima del redirect
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Aggiunge/emenda i parametri di conferma
    $redirect = add_query_arg(['em_success' => 1, 'form_id' => $form_id], $base);
    wp_safe_redirect($redirect);
    exit;
}
