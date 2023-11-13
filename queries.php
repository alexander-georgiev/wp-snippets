<?php 
function searchfilter($query)
{

	if ($query->is_search && !is_admin()) {
		global $current_user;
		if ($current_user->display_name == 'admin') {
			$post_in = $query->get('post__in');
			$query->set('post__in', $post_in);
			//$query->set('tax_query', array());
			//echo tdebug($query);
		}
	}

	return $query;
}

add_filter('pre_get_posts', 'searchfilter');

function order_by_stock_status($posts_clauses)
{

	global $current_user;
	if ($current_user->display_name == 'admin') {
		global $wpdb, $wp_query;

		var_dump($wp_query->query_vars['wc_query']);
		//only change query on WooCommerce loops
		if (!empty($wp_query->query_vars['s'])) {
			echo tdebug($posts_clauses);
			$posts_clauses['join'] .= " INNER JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id) ";
			$posts_clauses['orderby'] = ' IFNULL(istockstatus.meta_value, 0) ASC, ' . $posts_clauses['orderby'];
			$posts_clauses['where'] = " AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '' " . $posts_clauses['where'];
		}
	}

	return $posts_clauses;
}
add_filter('posts_clauses', 'order_by_stock_status', 30);

function test_posts_pre_query($posts, \WP_Query $query)
{
	if ($query->is_search && !is_admin()) {
		global $current_user;
		if ($current_user->display_name == 'admin') {
			var_dump($query);
		}
	}

	return $posts;
}
add_filter('posts_pre_query', 'test_posts_pre_query', 10, 2);

function test_posts_results($posts, \WP_Query $query)
{

	if ($query->is_search && !is_admin()) {
		global $current_user;
		if ($current_user->display_name == 'admin') {
			echo tdebug($query);
		}
	}

	return $posts;
}
add_filter('posts_results', 'test_posts_results', 10, 2);

function redirect_b2b_to_custom_homepage($query) {
    if ($query->is_main_query() && is_user_logged_in()) {
        //work-around for using is_front_page() in pre_get_posts
        //known bug in WP tracked by https://core.trac.wordpress.org/ticket/21790
        $front_page_id = get_option('page_on_front');
        $current_page_id = $query->get('page_id');
        $is_static_front_page = 'page' == get_option('show_on_front');

        if ($is_static_front_page && $front_page_id == $current_page_id) {
            $current_user = wp_get_current_user();
            $user = new WP_User($current_user->ID);
            // Check if the user has a role containing the substring 'haendler'
            $haendler_roles = array_filter($current_user->roles, function($role) {
                return strpos($role, 'haendler') !== false;
            });

            if (!empty($haendler_roles)) {
                $query->set('page_id', '92112');
            }
        }
    }
}
add_action('pre_get_posts', 'redirect_b2b_to_custom_homepage');
