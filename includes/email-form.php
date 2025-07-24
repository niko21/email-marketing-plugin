<?php
// Shortcode: [email_marketing_form id="1"]
add_shortcode('email_marketing_form', 'render_dynamic_email_form');

function render_dynamic_email_form($atts)
{
    global $wpdb;
    $atts = shortcode_atts(['id' => 0], $atts);
    $id   = intval($atts['id']);

    if (!$id) return 'Modulo non trovato.';

    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}email_marketing_forms WHERE id = %d",
        $id
    ));

    if (!$row) return 'Modulo non valido.';

    $campi = json_decode($row->campi);

    ob_start();

    // ✅ Banner di conferma se la query string è presente
    if (isset($_GET['em_success']) && isset($_GET['form_id']) && intval($_GET['form_id']) === $id) {
        echo '<div class="em-success-msg" style="background:#d1e7dd;color:#0f5132;padding:12px 16px;border-radius:6px;margin-bottom:15px;">✅ Grazie per l\'iscrizione! Controlla la tua casella email per la conferma.</div>';
    }
    ?>
    <form method="post" action="" class="email-marketing-form"><!-- classe CSS per styling -->
        <?php wp_nonce_field('email_form_nonce_action', 'email_form_nonce_field'); ?>
        <input type="hidden" name="form_id" value="<?php echo esc_attr($id); ?>">

        <?php
        // Stampa Nome + Cognome inline se entrambi esistono nel modulo
        if (in_array('nome', $campi) && in_array('cognome', $campi)) {
            echo '<div class="flex-inline">';
            echo '<div class="field">';
            echo '<label for="nome">Nome</label>';
            echo '<input type="text" name="nome" id="nome" required>';
            echo '</div>';
            echo '<div class="field">';
            echo '<label for="cognome">Cognome</label>';
            echo '<input type="text" name="cognome" id="cognome" required>';
            echo '</div>';
            echo '</div>';
        }
        // Altri campi
        foreach ($campi as $campo):
            if ($campo === 'nome' || $campo === 'cognome') continue; ?>
            <label for="<?php echo esc_attr($campo); ?>"><?php echo ucfirst(esc_html($campo)); ?></label><br>
            <input type="<?php echo ($campo === 'email') ? 'email' : 'text'; ?>" name="<?php echo esc_attr($campo); ?>" id="<?php echo esc_attr($campo); ?>" required><br><br>
        <?php endforeach; ?>
        <input type="submit" name="email_form_submit" value="Iscrivimi">
    </form>
    <?php
    return ob_get_clean();
}

// Enqueue CSS file
function email_marketing_enqueue_styles() {
    $css_relative = '../assets/css/email-marketing-form.css';
    $css_url      = plugins_url($css_relative, __FILE__);
    $css_path     = plugin_dir_path(__FILE__) . $css_relative;

    if (file_exists($css_path)) {
        wp_enqueue_style('email-marketing-form', $css_url, [], '1.2');
    }
}
add_action('wp_enqueue_scripts', 'email_marketing_enqueue_styles');
