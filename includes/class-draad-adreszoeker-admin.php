<?php
class Draad_Adreszoeker_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'handle_import_request']);
        add_action('admin_notices', [$this, 'show_completion_notice']);
    }

    public function show_completion_notice() {
        if (get_transient('draad_adreszoeker_import_complete')) {
            delete_transient('draad_adreszoeker_import_complete');
            echo '<div class="notice notice-success is-dismissible">
                    <p>' . esc_html__('Import process has completed!', 'draad-adreszoeker') . '</p>
                </div>';
        }
    }

    public function add_admin_menu() {
        add_submenu_page(
            'tools.php',
            __('Adreszoeker Import', 'draad-adreszoeker'),
            __('Adreszoeker Import', 'draad-adreszoeker'),
            'manage_options',
            'draad-adreszoeker-import',
            [$this, 'render_import_page']
        );
    }

    public function handle_import_request() {
        if (!isset($_POST['draad_adreszoeker_import_nonce']) ||
            !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['draad_adreszoeker_import_nonce'] ) ), 'import_action' )) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die( esc_html__('You do not have sufficient permissions', 'draad-adreszoeker') );
        }

        $importer = new Draad_Adreszoeker_Import();
        if (!isset($_POST['sql_path']) || !isset($_POST['import_type'])) { 
            return;
        }
        
        $sql_path = sanitize_text_field(wp_unslash( $_POST['sql_path'] ) ?: '');
        $type = sanitize_text_field(wp_unslash( $_POST['import_type'] ) ?: '');

        if ($importer->import($sql_path, $type)) {
            set_transient( 'draad_adreszoeker_import_complete', 1 );
            add_option( 'draad_az_import_' . basename( $sql_path ) . '_completed', 1 );
        } else {
            add_action('admin_notices', function() {
                echo esc_html( '<div class="notice notice-error"><p>'
                   . __('Failed to start import. Check error logs.', 'draad-adreszoeker')
                   . '</p></div>' );
            });
        }
    }

    public function render_import_page() {
        // Define the SQL files we expect to find
        $sql_files = [
            'create_table' => [
                'label' => __('Create Address Table', 'draad-adreszoeker'),
                'filename' => 'addresses.sql',
                'type' => 'create_table',
                'description' => __('Creates the address lookup table with all data', 'draad-adreszoeker')
            ]
        ];

        // Check which files actually exist
        $plugin_path = DRAAD_ADRESZOEKER_DIR . 'data/';
        $available_imports = [];

        foreach ($sql_files as $key => $file) {
            $full_path = $plugin_path . $file['filename'];
            if (file_exists($full_path)) {
                $file['path'] = $full_path;
                $available_imports[$key] = $file;
            }
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Adreszoeker Import', 'draad-adreszoeker'); ?></h1>

            <?php if (empty($available_imports)): ?>
                <div class="notice notice-warning">
                    <p><?php esc_html_e('No import files found. Please ensure the SQL files are present in the plugin\'s sql/ directory.', 'draad-adreszoeker'); ?></p>
                </div>
            <?php else: ?>
                <p><?php esc_html_e('Select which data you want to import. Each import will run in the background.', 'draad-adreszoeker'); ?></p>

                <div class="draad-import-buttons">
                    <?php 
                        foreach ($available_imports as $import):
                            $isImported = get_option( 'draad_az_import_' . basename( $import['path'] ) . '_completed' );
                    ?>
                        <div class="draad-import-card">
                            <h3><?php echo esc_html($import['label']); ?><?php echo $isImported ? '<span class="check"></span>' : ''; ?></h3>
                            <p><?php echo esc_html($import['description']); ?></p>
                            <form method="post">
                                <?php wp_nonce_field('import_action', 'draad_adreszoeker_import_nonce'); ?>
                                <input type="hidden" name="sql_path" value="<?php echo esc_attr($import['path']); ?>">
                                <input type="hidden" name="import_type" value="<?php echo esc_attr($import['type']); ?>">
                                <p class="submit">
                                    <input type="submit"
                                           class="button button-primary <?php echo $isImported ? 'disabled' : ''; ?>"
                                           value="<?php esc_attr_e( 'Import', 'draad-adreszoeker' ); ?>"
                                           <?php echo $isImported ? 'disabled' : ''; ?>>
                                </p>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <style>
                    .draad-import-buttons {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                        gap: 20px;
                        margin-top: 20px;
                    }

                    .draad-import-card h3 {
                        display: flex;
                        align-items: center;
                        gap: 1ch;
                    }

                    .draad-import-card .check {
                        background: #d2f9dc;
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 640' fill='%2300a32a'%3E%3Cpath d='M530.8 134.1C545.1 144.5 548.3 164.5 537.9 178.8L281.9 530.8C276.4 538.4 267.9 543.1 258.5 543.9C249.1 544.7 240 541.2 233.4 534.6L105.4 406.6C92.9 394.1 92.9 373.8 105.4 361.3C117.9 348.8 138.2 348.8 150.7 361.3L252.2 462.8L486.2 141.1C496.6 126.8 516.6 123.6 530.9 134z'/%3E%3C/svg%3E");
                        background-size: 60%;
                        background-position: center;
                        background-repeat: no-repeat;
                        display: inline-block;
                        inline-size: 1lh;
                        block-size: 1lh;
                        border: 1px solid #00a32a;
                        border-radius: 50%;
                        vertical-align: middle;
                    }

                    .draad-import-card {
                        border: 1px solid #ddd;
                        padding: 20px;
                        border-radius: 4px;
                        background: #fff;
                    }

                    .draad-import-card h3 {
                        margin-top: 0;
                        color: #2271b1;
                    }
                </style>
            <?php endif; ?>
        </div>
        <?php
    }
}