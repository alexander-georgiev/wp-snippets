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
