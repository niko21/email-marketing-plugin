<?php

// Shortcode: [email_marketing_form id="1"]
add_shortcode('email_marketing_form', 'render_dynamic_email_form');

function render_dynamic_email_form($atts)
{
    global $wpdb;
    $atts = shortcode_atts(['id' => 0], $atts);
    $id = intval($atts['id']);

    if (!$id) return 'Modulo non trovato.';

    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}email_marketing_forms WHERE id = %d",
        $id
    ));

    if (!$row) return 'Modulo non valido.';

    $campi = json_decode($row->campi);

    ob_start(); ?>
    <form method="post" action="">
        <?php wp_nonce_field('email_form_nonce_action', 'email_form_nonce_field'); ?>
        <input type="hidden" name="form_id" value="<?php echo esc_attr($id); ?>">
        <?php foreach ($campi as $campo): ?>
            <label><?php echo ucfirst(esc_html($campo)); ?></label><br>
            <input type="<?php echo ($campo === 'email') ? 'email' : 'text'; ?>" name="<?php echo esc_attr($campo); ?>" required><br><br>
        <?php endforeach; ?>
        <input type="submit" name="email_form_submit" value="Iscrivimi">
    </form>
<?php
    return ob_get_clean();
}
