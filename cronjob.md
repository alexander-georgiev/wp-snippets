## WP Cronjob - Host Europe
Wordpress already has an internal "WP-Cron", but it only runs when visitors are on your site. Without visitors there is therefore no WP-Cron. On sites with many users, however, it can also happen that WP-Cron runs far too often and thus causes unnecessary load on the server. In this case it is better to replace the WP cron with a real cron job. To do this, proceed as follows:
1. First, disable WP Cron by typing the following line above "That's all, stop editing! Happy blogging." enter in your wp-config.php: `define('DISABLE_WP_CRON', true);`
2. Now create a file **wp-cron.shin** your Wordpress directory with the following content: \
`#!/bin/sh`\
`wget -O - -q "https://website-name.com/wp-cron.php?doing_wp_cron" > /dev/null`\
`exit 0`
    1. If there is no file field, you can add it as command - `wget -q -O /dev/null 'https://denzweine.ch/wp-cron.php' >/dev/null 2>&1` - Cyon
4. Now assign the rights 750 for this file and then enter this as a cron job in the HIS.
    1. Add Script file path (/www/kinderschuh2022/wp-cron.sh) and set time (30min e.g.)
    2. No Parameter is needed
5. Specify time/recurrance -> `*/5 * * * * /home/oracle/scripts/export_dump.sh` - every 5mins
    1. Reference - https://en.wikipedia.org/wiki/Cron
## Example Cron PHP
```php
public function __construct() {
        add_action('vinx_update_products', array($this, 'processProduct'));
        add_action('vinx_update_products', array($this, 'vinx_update_products_function'));
        add_action('admin_init', array($this, 'initCrons'));
        add_filter('cron_schedules', [$this, 'example_add_cron_interval']);
}
public function initCrons() {
 if (!wp_next_scheduled('vinx_update_products')) {
            wp_schedule_event(time(), 'every_minute', 'vinx_update_products'); //every min
            //or
            wp_schedule_event(strtotime(21:00:00), 'daily_at_21', 'vinx_update_products'); //daily at 21
        }
}
public function example_add_cron_interval($schedules) {
    $schedules['daily_at_21'] = array(
        'interval' => 86400, // 24 hours in seconds
        'display' => esc_html__('Daily at 21:00'),
    );      
    return $schedules;
}
public function vinx_update_products_function() {
    // Add code to perform the cron task here
    // For debugging, you can log to a file
    $date_format = get_option('date_format');
    $time_format = get_option('time_format');
    $time_format .= ':s';
    $current_date = date_i18n($date_format . ' ' . $time_format);
    file_put_contents(wp_normalize_path(__DIR__) . '/cron_debug.log', 'Process Products Cron ran at: ' . $current_date . "\n", FILE_APPEND);
}
```
