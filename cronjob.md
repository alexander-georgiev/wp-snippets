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
