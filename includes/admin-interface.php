<?php

add_action('admin_menu', 'email_marketing_add_admin_menu');

function email_marketing_add_admin_menu()
{
    add_menu_page(
        'Email Marketing',
        'Email Marketing',
        'manage_options',
        'email-marketing',
        'email_marketing_admin_page',
        'dashicons-email',
        26
    );
}

function email_marketing_admin_page()
{
    global $wpdb;

    $form_table = $wpdb->prefix . 'email_marketing_forms';

    // Salvataggio nuovo modulo
    if (isset($_POST['crea_form']) && isset($_POST['nome_form']) && isset($_POST['campi'])) {
        $nome_form = sanitize_text_field($_POST['nome_form']);
        $campi = json_encode(array_map('sanitize_text_field', $_POST['campi']));
        $wpdb->insert($form_table, ['nome_form' => $nome_form, 'campi' => $campi]);
        echo '<div class="notice notice-success"><p>Modulo creato con successo!</p></div>';
    }

    // Salvataggio modifica modulo
    if (isset($_POST['salva_modifiche']) && isset($_POST['form_id'])) {
        $form_id = intval($_POST['form_id']);
        $nome_form = sanitize_text_field($_POST['nome_form']);
        $campi = json_encode(array_map('sanitize_text_field', $_POST['campi'] ?? []));

        $wpdb->update($form_table, [
            'nome_form' => $nome_form,
            'campi' => $campi
        ], ['id' => $form_id]);

        echo '<div class="notice notice-success"><p>Modulo aggiornato con successo!</p></div>';
    }

    // Recupero moduli
    $moduli = $wpdb->get_results("SELECT * FROM $form_table");

    // Modifica modulo specifico (se selezionato)
    $modifica = null;
    if (isset($_GET['edit'])) {
        $mod_id = intval($_GET['edit']);
        $modifica = $wpdb->get_row($wpdb->prepare("SELECT * FROM $form_table WHERE id = %d", $mod_id));
        $modifica->campi = json_decode($modifica->campi ?? '[]');
    }

?>
    <div class="wrap">
        <h1><?php echo $modifica ? 'Modifica Modulo' : 'Crea un nuovo Modulo'; ?></h1>
        <form method="post">
            <?php if ($modifica): ?>
                <input type="hidden" name="form_id" value="<?php echo $modifica->id; ?>">
            <?php endif; ?>
            <table class="form-table">
                <tr>
                    <th><label for="nome_form">Nome del Form</label></th>
                    <td><input type="text" name="nome_form" required value="<?php echo esc_attr($modifica->nome_form ?? '') ?>"></td>
                </tr>
                <tr>
                    <th>Campi da includere</th>
                    <td>
                        <?php
                        $campi_possibili = ['nome', 'cognome', 'email'];
                        foreach ($campi_possibili as $campo) {
                            $checked = ($modifica && in_array($campo, $modifica->campi)) || (!$modifica && $campo === 'email') ? 'checked' : '';
                            echo "<label><input type='checkbox' name='campi[]' value='$campo' $checked> $campo</label><br>";
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <?php submit_button($modifica ? 'Salva Modifiche' : 'Crea Modulo', 'primary', $modifica ? 'salva_modifiche' : 'crea_form'); ?>
        </form>

        <hr>
        <h2>Moduli Esistenti</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Shortcode</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($moduli as $mod): ?>
                    <tr>
                        <td><?php echo $mod->id; ?></td>
                        <td><?php echo esc_html($mod->nome_form); ?></td>
                        <td><code>[email_marketing_form id="<?php echo $mod->id; ?>"]</code></td>
                        <td><a href="?page=email-marketing&edit=<?php echo $mod->id; ?>">Modifica</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
}
