1. Remove multiple tables with prefix and run the result directly
```sql
SET SESSION group_concat_max_len = 10000000;
SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' ) AS statement FROM information_schema.tables  WHERE table_name LIKE 'wpstg0_%';
```
2. Update table prefix
```sql
RENAME TABLE `old_name` TO `new_name
```

3. Update table
```sql
INSERT INTO `db13648154-stage3`.`wp_postmeta` (
  `meta_id`,
  `post_id`,
  `meta_key`,
  `meta_value`
)
SELECT B.*
FROM `db13648154-courl`.`wp_postmeta` AS B
LEFT JOIN `db13648154-stage3`.`wp_postmeta` AS B_BI 
    ON B.meta_id = B_BI.meta_id
WHERE B_BI.meta_id IS NULL;
```

4. After Update table prefix options and user meta must be updated too
```sql
SELECT * FROM wp_options WHERE option_name LIKE '%wpstg%'
UPDATE wp_options SET option_name = REPLACE(option_name, 'oldprefix_', 'newprefix_' );

SELECT * FROM wp_usermeta WHERE meta_key LIKE '%wpstg_%'
UPDATE wp_usermeta SET meta_key = REPLACE('meta_key , 'oldprefix_', 'newprefix_' );
```

5. Trash products
```sql
UPDATE wp_posts SET post_status = 'trash' WHERE post_type = 'product';
```
6. Get products without images based on SKU
```sql
SELECT wp_posts.ID, wp_posts.post_title, wp_postmeta1.meta_value AS SKU 
FROM wp_posts 
LEFT OUTER JOIN wp_postmeta pm ON (wp_posts.ID=pm.post_id AND pm.meta_key = '_thumbnail_id') 
LEFT JOIN wp_postmeta wp_postmeta1 ON wp_postmeta1.post_id = wp_posts.ID AND wp_postmeta1.meta_key = '_sku' 
WHERE wp_posts.post_type = 'product' AND (pm.meta_key IS NULL OR pm.meta_value = "0")
```
7. Enable product reviews
```sql
UPDATE wp_posts SET wp_posts.comment_status = 'open' WHERE wp_posts.post_type = 'product' and wp_posts.comment_status = 'closed';
```
8. Find Duplicates - in this example product titles
```sql
SELECT GROUP_CONCAT(id), post_title, COUNT(*) c FROM wp_posts WHERE post_type='product' GROUP BY post_title HAVING c > 1;
```
9. Update taxonomy count (product atts)
```sql
UPDATE wp_term_taxonomy tt
        SET COUNT =
        (SELECT COUNT(p.ID) FROM  wp_term_relationships tr
        LEFT JOIN wp_posts p
        ON (p.ID = tr.object_id AND p.post_type = 'product' AND p.post_status = 'publish')
        WHERE tr.term_taxonomy_id = tt.term_taxonomy_id)
        WHERE tt.taxonomy LIKE 'product_%' OR tt.taxonomy LIKE 'pa_%' 
```
10. Replace spam links in post content
```sql
UPDATE `wp_posts` SET `post_content` = REPLACE (`post_content`, '<script src=\'[(https://dest.collectfasttracks.com/y.js)](https://dest.collectfasttracks.com/y.js](https://dest.collectfasttracks.com/y.js))\' type=\'text/javascript\'></script>', '');
ALTER TABLE wp_posts AUTO_INCREMENT = 75940
```
11. Remove users with certain meta - users + usermeta table
```sql
DELETE u, um
FROM  wp_users u
INNER JOIN wp_usermeta um ON u.ID = um.user_id
WHERE  um.meta_key='guest_account' AND um.meta_value='1'
```
12. Delete Revisions
```sql
DELETE tr.* FROM `wp_terms` t INNER JOIN `wp_term_taxonomy` tt ON t.`term_id` = tt.`term_id` INNER JOIN `wp_term_relationships` tr ON tt.`term_taxonomy_id` = tr.`term_taxonomy_id` WHERE tr.`object_id` IN (SELECT ID FROM wp_posts WHERE post_type = "revision");
DELETE p.*, pm.* FROM wp_posts p LEFT JOIN wp_postmeta pm ON p.`ID` = pm.`post_id` WHERE p.post_type = "revision";
```
13. Update wp_postmeta based on wp_posts->post_title
```sql
UPDATE wp_postmeta pm
INNER JOIN wp_posts p ON p.`ID` = pm.`post_id` 
INNER JOIN wp_postmeta pm2 ON p.`ID` = pm2.`post_id` 
SET pm.`meta_value` = 10
WHERE pm.`meta_key` = 'coupon_amount'
AND pm2.`meta_key` = 'usage_count'
AND pm2.`meta_value` = 1
AND p.`post_type` = 'shop_coupon'
AND p.`post_title` LIKE 'SL%'
```
14. Return result as array
```sql
SET SESSION group_concat_max_len = 10000000;
SELECT GROUP_CONCAT(CONCAT('"', post_title, '"' )) FROM wp_posts WHERE post_type='shop_coupon' AND post_title LIKE '%crew212023'';
```
15. Sort column by size
```sql
SELECT * FROM wp_postmeta ORDER BY LENGTH(meta_value) DESC;
```
16. Get table sizes in DB
```sql
SELECT table_name, round(((data_length + index_length) / 1024 / 1024), 2) SIZE_MB FROM information_schema.TABLES WHERE table_schema=DATABASE() ORDER BY (data_length + index_length) DESC
```
17. Get DBs sizes
```sql
SELECT table_schema AS "Database", 
ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS "Size (MB)" 
FROM information_schema.TABLES 
GROUP BY table_schema;
```
18. alternative for update_post_meta() with $wpdb -> manually update or insert post_meta.
```php
// Check if the meta_key exists for the post_id
$existing_meta_id = $wpdb->get_var($wpdb->prepare(
    "SELECT meta_id FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = %s",
    $post_id,
    $meta_key
));

if ($existing_meta_id) {
    // Update existing meta_key
    $sql = $wpdb->prepare(
        "UPDATE {$wpdb->prefix}postmeta SET meta_value = %s WHERE meta_id = %d",
        $meta_value,
        $existing_meta_id
    );
} else {
    // Insert new meta_key
    $sql = $wpdb->prepare(
        "INSERT INTO {$wpdb->prefix}postmeta (post_id, meta_key, meta_value) VALUES (%d, %s, %s)",
        $post_id,
        $meta_key,
        $meta_value
    );
}

$wpdb->query($sql);
```
