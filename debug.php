<?php 
/* Useful for debugging when working on posts (add/edit/trash), taxonomies or any hard debugging functions */
function wpdebug() {
        $content = ['object_id' => $object_id, 'terms' => $terms];
        $debugs = debug_backtrace();
        foreach ($debugs as $debug) {
            //we can comment this out to check our plugin
            if (preg_match('(plugin.php|plugins/myplugin)', $debug['file']) === 1 || strpos($debug['function'], 'apply_filters') !== false) continue;

            $file = str_replace('/var/www/html', '', $debug['file']);
            $content['debug'][] = array('file' => $file, 'func' => $debug['function']);
        }
        // Read the existing log file and decode it
        $log_file = WP_CONTENT_DIR . '/debug.json';
        $existing_logs = file_exists($log_file) ? json_decode(file_get_contents($log_file), true) : array();

        // Append the new log entry
        $existing_logs[] = $content;

        // Encode the updated array back to JSON without pretty print
        $logs = json_encode($existing_logs);

        // Write the JSON back to the file
        file_put_contents($log_file, $logs);
}
?>
