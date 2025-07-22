<?php

function email_marketing_subscribers_page()
{
    global $wpdb;

    $forms_table = $wpdb->prefix . 'email_marketing_forms';
    $subs_table  = $wpdb->prefix . 'email_marketing';

    $moduli = $wpdb->get_results("SELECT * FROM $forms_table");

    $form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;

    // === Esportazione CSV sicura ===
    if (isset($_GET['action']) && $_GET['action'] === 'export_csv' && $form_id > 0 && current_user_can('manage_options')) {
        $iscritti = $wpdb->get_results(
            $wpdb->prepare("SELECT nome, cognome, email, data_iscrizione FROM $subs_table WHERE form_id = %d", $form_id),
            ARRAY_A
        );

        if ($iscritti) {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="iscritti_modulo_' . $form_id . '.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, array('Nome', 'Cognome', 'Email', 'Data Iscrizione'));
            foreach ($iscritti as $riga) {
                fputcsv($output, $riga);
            }
            fclose($output);
            exit;
        } else {
            wp_die('Nessun dato da esportare.', 'Errore esportazione');
        }
    }

?>
    <div class="wrap">
        <h1>Iscritti</h1>

        <form method="get" style="margin-bottom: 20px;">
            <input type="hidden" name="page" value="email-marketing-iscritti">
            <label for="form_id">Filtra per modulo:</label>
            <select name="form_id" id="form_id" onchange="this.form.submit()">
                <option value="0">-- Tutti i moduli --</option>
                <?php foreach ($moduli as $mod): ?>
                    <option value="<?php echo $mod->id; ?>" <?php selected($form_id, $mod->id); ?>>
                        <?php echo esc_html($mod->nome_form); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($form_id): ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=email-marketing-iscritti&form_id=' . $form_id . '&action=export_csv')); ?>" class="button button-primary">Esporta CSV</a>
        <?php endif; ?>

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
                if ($form_id > 0) {
                    $query .= $wpdb->prepare(" WHERE form_id = %d", $form_id);
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
