<?php

/**
 * Gestisce conferma email e disiscrizione tramite token
 */
add_action('init', 'email_marketing_handle_tokens');

function email_marketing_handle_tokens()
{
    if (!isset($_GET['em_confirm']) && !isset($_GET['em_unsub'])) return;

    global $wpdb;
    $subs_table   = $wpdb->prefix . 'email_marketing';
    $tokens_table = $wpdb->prefix . 'email_marketing_tokens';

    // Conferma iscrizione
    if (isset($_GET['em_confirm'])) {
        $token = sanitize_text_field($_GET['em_confirm']);
        $row   = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tokens_table WHERE token = %s AND type = 'confirm' AND expires >= NOW()", $token));
        if ($row) {
            // Attiva iscritto
            $wpdb->update($subs_table, ['attivo' => 1], ['id' => $row->email_id]);
            // Elimina token
            $wpdb->delete($tokens_table, ['id' => $row->id]);
            // Banner OK
            add_action('wp_head', function () {
                echo '<script>window.addEventListener("DOMContentLoaded",function(){alert("✅ Iscrizione confermata con successo!");});</script>';
            });
        }
    }

    // Disiscrizione
    if (isset($_GET['em_unsub'])) {
        $token = sanitize_text_field($_GET['em_unsub']);
        $row   = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tokens_table WHERE token = %s AND type = 'unsub'", $token));
        if ($row) {
            // Disattiva iscritto
            $wpdb->update($subs_table, ['attivo' => 0], ['id' => $row->email_id]);
            // Elimina token
            $wpdb->delete($tokens_table, ['id' => $row->id]);
            add_action('wp_head', function () {
                echo '<script>window.addEventListener("DOMContentLoaded",function(){alert("✅ Sei stato disiscritto con successo.");});</script>';
            });
        }
    }
}
