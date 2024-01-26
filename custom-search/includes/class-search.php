<?php
namespace CustomSearch;

class Search {

	/**
	 * Perform a search query.
	 *
	 * @param array $args Search parameters.
	 * @return array Array of WP_Post objects and pagination data.
	 */
	public function perform_search($args) {
		// Default parameters for WP_Query
		$default_args = array(
			'post_status'    => 'publish',
			'posts_per_page' => 10, // Default number of posts per page
			'paged'          => 1,  // Default page number
		);

		// Merge with user supplied arguments
		$query_args = wp_parse_args($args, $default_args);

		// Execute the query
		$query = new \WP_Query($query_args);

		// Extract the necessary information from the query
		$posts = $query->posts;
		$max_num_pages = $query->max_num_pages;
		$current_page = max(1, get_query_var('paged', 1));

		// Return the posts and pagination data
		return array(
			'posts' => $posts,
			'pagination' => array(
				'max_num_pages' => $max_num_pages,
				'current_page'  => $current_page,
			),
		);
	}

	/**
	 * Output pagination links.
	 *
	 * @param array $pagination Pagination data.
	 */
	public function output_pagination($pagination) {
		if ($pagination['max_num_pages'] > 1) {
			$current_page = $pagination['current_page'];
			$total_pages = $pagination['max_num_pages'];

			// Display the pagination
			echo '<div class="pagination">';

			// Previous page link
			if ($current_page > 1) {
				echo '<a href="' . get_pagenum_link($current_page - 1) . '">&laquo; Previous</a>';
			}

			// Page number links
			for ($i = 1; $i <= $total_pages; $i++) {
				if ($i == $current_page) {
					echo '<span class="current">' . $i . '</span>';
				} else {
					echo '<a href="' . get_pagenum_link($i) . '">' . $i . '</a>';
				}
			}

			// Next page link
			if ($current_page < $total_pages) {
				echo '<a href="' . get_pagenum_link($current_page + 1) . '">Next &raquo;</a>';
			}

			echo '</div>';
		}
	}
}
