<?php

function email_marketing_subscribers_page()
{
    if (!current_user_can('manage_options')) {
        wp_die('Accesso negato.');
    }

    global $wpdb;

    $forms_table = $wpdb->prefix . 'email_marketing_forms';
    $subs_table  = $wpdb->prefix . 'email_marketing';

    $moduli = $wpdb->get_results("SELECT * FROM $forms_table");

    $form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;

?>
    <div class="wrap">
        <h1>Iscritti</h1>

        <form method="get" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="email-marketing-iscritti">
            <label for="form_id">Filtra per modulo:</label>
            <select name="form_id" id="form_id" onchange="this.form.submit()">
                <option value="0">-- Tutti i moduli --</option>
                <?php foreach ($moduli as $mod): ?>
                    <option value="<?php echo esc_attr($mod->id); ?>" <?php selected($form_id, $mod->id); ?>>
                        <?php echo esc_html($mod->nome_form); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <table class="widefat">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Email</th>
                    <th>Data Iscrizione</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM $subs_table";
                $params = [];

                if ($form_id > 0) {
                    $query .= " WHERE form_id = %d";
                    $query = $wpdb->prepare($query, $form_id);
                }

                $iscritti = $wpdb->get_results($query);

                if ($iscritti) :
                    foreach ($iscritti as $riga) :
                        echo "<tr>
                            <td>" . esc_html($riga->nome) . "</td>
                            <td>" . esc_html($riga->cognome) . "</td>
                            <td>" . esc_html($riga->email) . "</td>
                            <td>" . esc_html($riga->data_iscrizione) . "</td>
                        </tr>";
                    endforeach;
                else :
                    echo '<tr><td colspan="4">Nessun iscritto trovato.</td></tr>';
                endif;
                ?>
            </tbody>
        </table>
    </div>
<?php
}
