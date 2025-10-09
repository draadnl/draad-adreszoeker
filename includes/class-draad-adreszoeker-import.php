<?php

if ( !class_exists( 'Draad_Adreszoeker_Import' ) ) {
    class Draad_Adreszoeker_Import {

        private $wpdb;

        public function __construct() {
            global $wpdb;
            $this->wpdb = $wpdb;
        }
        
        public function import( $sql_path, $type ) {

            // Validate SQL file
            if ( !$this->validate_sql( $sql_path ) ) {
                error_log( 'SQL file not valid: ' . $sql_path );
                return false;
            }

            return match ( $type ) {
                'create_table' => $this->create_table( $sql_path ),
                'insert_options' => $this->insert_options( $sql_path ),
            };
            
        }

        private function validate_sql( $sql_path ) {

            // Return if file does not exist
            if ( !file_exists( $sql_path ) ) {
                error_log( 'SQL file not found: ' . $sql_path );
                return false;
            }
            
            return true;

        }

        private function insert_options( $sql_path ) {

            $sql_content = file_get_contents($sql_path);

            // Extract VALUES section
            if (!preg_match('/INSERT INTO.*VALUES\s*(.*)/is', $sql_content, $matches)) {
                error_log('Could not parse options SQL file format');
                return false;
            }

            $values = trim($matches[1]);
            $values = rtrim($values, ",");
            $values = rtrim($values, ";");

            $query = "INSERT INTO `{$this->wpdb->options}` (`option_name`, `option_value`, `autoload`)
                    VALUES " . $values;

            $this->wpdb->query('START TRANSACTION');

            try {
                $result = $this->wpdb->query($query);

                if ($result === false) {
                    throw new Exception('Options SQL import failed: ' . $this->wpdb->last_error);
                }

                $this->wpdb->query('COMMIT');
                error_log('Successfully imported ' . $result . ' options');
                return true;

            } catch (Exception $e) {
                $this->wpdb->query('ROLLBACK');
                error_log('Options import error: ' . $e->getMessage());
                return false;
            }

        }

        private function create_table( $sql_path ) {

            // Extract table name
            $table_name = $this->extract_table_name( $sql_path );
            if ( !$table_name ) {
                error_log('Could not parse table name from SQL file');
                return false;
            }

            $table_name = str_replace('wp_', $this->wpdb->prefix, $table_name);

            // Return if table exists
            $table_exists = $this->wpdb->get_var( $this->wpdb->prepare("SHOW TABLES LIKE %s", $table_name) ) === $table_name;
            if ($table_exists) {
                
                // Return if table has rows
                $row_count = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                if ($row_count) {
                    error_log('Table ' . $table_name . ' already has rows');
                    return false;
                }
            }

            // Process the SQL file
            return $this->process_sql_file($sql_path, $table_name);
        }

        /**
         * Extract table name from SQL file
         */
        private function extract_table_name($sql_path) {

            $content = file_get_contents($sql_path);

            // Try to find table name in CREATE TABLE statement
            if (preg_match('/CREATE TABLE [`\']?([^\s`\']+)/i', $content, $matches)) {
                return $matches[1];
            }

            // Try to find table name in INSERT statement
            if (preg_match('/INSERT INTO [`\']?([^\s`\']+)/i', $content, $matches)) {
                return $matches[1];
            }

            error_log('Could not parse table name from SQL file: ' . $sql_path);
            return false;
        }

        private function process_sql_file($sql_path, $table_name) {
            error_log("Starting SQL import from: $sql_path");

            $batch_size = 1000;
            $query_buffer = '';
            $in_create_table = false;
            $line_count = 0;

            $handle = fopen($sql_path, 'r');
            if (!$handle) {
                error_log('Failed to open SQL file: ' . $sql_path);
                return false;
            }

            set_time_limit(0);
            $this->wpdb->query('START TRANSACTION');

            try {
                while (($line = fgets($handle)) !== false) {
                    $line = trim($line);
                    $line_count++;

                    // Skip comments and empty lines
                    if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0) {
                        continue;
                    }

                    // Handle CREATE TABLE
                    if (strpos($line, 'CREATE TABLE') !== false) {
                        $in_create_table = true;
                        $query_buffer = $line;
                        continue;
                    }

                    if ($in_create_table) {
                        $query_buffer .= ' ' . $line;

                        if (strpos($line, ';') !== false) {
                            $in_create_table = false;
                            $query = str_replace('wp_', $this->wpdb->prefix, $query_buffer);

                            if (!$this->wpdb->query($query)) {
                                throw new Exception('Failed to create table: ' . $this->wpdb->last_error);
                            }
                            $query_buffer = '';
                        }
                        continue;
                    }

                    // Handle INSERT statements
                    if (strpos($line, 'INSERT INTO') !== false || !empty($query_buffer)) {
                        $query_buffer .= ' ' . $line;

                        if (strpos($line, ';') !== false) {
                            $query = str_replace('wp_', $this->wpdb->prefix, $query_buffer);

                            if (!$this->wpdb->query($query)) {
                                throw new Exception('Failed to insert data: ' . $this->wpdb->last_error);
                            }

                            $query_buffer = '';

                            // Commit periodically
                            if ($line_count % $batch_size === 0) {
                                $this->wpdb->query('COMMIT');
                                $this->wpdb->query('START TRANSACTION');
                                usleep(100000);
                            }
                        }
                    }
                }

                $this->wpdb->query('COMMIT');
                $this->wpdb->query("OPTIMIZE TABLE $table_name");
                fclose($handle);

                return true;

            } catch (Exception $e) {
                $this->wpdb->query('ROLLBACK');
                fclose($handle);
                error_log('SQL import failed: ' . $e->getMessage());
                return false;
            }
        }

    }
}