## WP Cronjob - Host Europe
Wordpress already has an internal "WP-Cron", but it only runs when visitors are on your site. Without visitors there is therefore no WP-Cron. On sites with many users, however, it can also happen that WP-Cron runs far too often and thus causes unnecessary load on the server. In this case it is better to replace the WP cron with a real cron job. To do this, proceed as follows:
1. First, disable WP Cron by typing the following line above "That's all, stop editing! Happy blogging." enter in your wp-config.php: `define('DISABLE_WP_CRON', true);`
2. Now create a file *wp-cron.shin* your Wordpress directory with the following content:
`#!/bin/sh`\
`wget -O - -q "http://www.nur-ein-beispiel.de/wp-cron.php?doing_wp_cron" > /dev/null`\
`exit 0`
3. Now assign the rights 750 for this file and then enter this as a cron job in the HIS.
