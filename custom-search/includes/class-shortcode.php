<?php
namespace CustomSearch;

class Shortcode {

	public function __construct() {
		// Add AJAX hooks for saving the display mode (list/grid)
		add_action('wp_ajax_custom_search_save_display_mode', array($this, 'ajax_save_display_mode'));
		add_action('wp_ajax_nopriv_custom_search_save_display_mode', array($this, 'ajax_save_display_mode'));

		// Enqueue JavaScript and CSS for front-end
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

		add_action('wp_ajax_custom_search_load_more', array($this, 'ajax_load_more_posts'));
		add_action('wp_ajax_nopriv_custom_search_load_more', array($this, 'ajax_load_more_posts'));
	}

	public function enqueue_scripts() {
		wp_enqueue_script('custom-search-js', plugin_dir_url(__FILE__) . '../js/custom-search.js', array('jquery'), '1.0', true);
		wp_localize_script('custom-search-js', 'customSearchData', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('custom_search_nonce'),
		));

		wp_enqueue_style('custom-search-css', plugin_dir_url(__FILE__) . '../css/style.css');
	}

	public function ajax_load_more_posts() {
		check_ajax_referer('custom_search_nonce', 'nonce');

		// Get current page and total pages from AJAX request
		$current_page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$total_pages = isset($_POST['total_pages']) ? intval($_POST['total_pages']) : 1;

		// Only proceed if there are more pages to load
		if ($current_page < $total_pages) {
			$new_page = $current_page + 1;
			echo $this->perform_search($_POST['post_types'], $_POST['element_count'], $_POST['search_query'], $new_page);
		}

		wp_die(); // This is required to terminate immediately and return a proper response
	}


	public function ajax_save_display_mode() {
		// Check AJAX nonce for security
		check_ajax_referer('custom_search_nonce', 'nonce');

		// Your AJAX logic goes here...
		// ...
	}

	public function handle_shortcode($atts) {

		// Set default attributes for the shortcode
		$atts = shortcode_atts(array(
			'post-types' => 'post',
			'element-count' => 6, // Default value
		), $atts, 'custom_search');

		// Start output buffering
		ob_start();

		// Display the search form
		?>
        <form class="custom-search-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
            <input type="text" name="custom_search_query" />
            <input type="submit" value="Search" />
        </form>
		<?php

		if (isset($_POST['custom_search_query']) && !empty($_POST['custom_search_query'])) {
			$search_query = sanitize_text_field($_POST['custom_search_query']);
			echo $this->perform_search($atts['post-types'], $atts['element-count'], $search_query);
		}

		return ob_get_clean();
}

	private function perform_search($post_types, $element_count, $search_query, $page = 1) {
		// Get the display mode option (list or grid)
		$display_mode = get_option('custom_search_display_mode', 'list');

		// Prepare arguments for WP_Query
		$args = array(
			'post_type' => explode(',', $post_types),
			'posts_per_page' => $element_count,
            'paged' => $page,
			's' => $search_query,
		);

		// Execute the query
		$query = new \WP_Query($args);
		ob_start();


		// Check if we have posts and display them
		if ($query->have_posts()) {
			echo '<div class="custom-search-results ' . esc_attr($display_mode) . '">';

			while ($query->have_posts()) {
				$query->the_post();

				// Calculate total pages
				$total_pages = $query->max_num_pages;

				// Output for each post
				echo '<div class="custom-search-item">';
				echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
                echo '<p>' . get_the_excerpt() . '</p>';
				echo '</div>';
			}

			echo '</div>';
		} else {
			// No posts found message
			echo '<p>No posts found.</p>';
		}
		if ($page < $total_pages) {
			echo '<button id="load-more-button" data-page="' . $page . '" data-total-pages="' . $total_pages . '" data-post-types="' . esc_attr($post_types) . '" data-element-count="' . esc_attr($element_count) . '" data-search-query="' . esc_attr($search_query) . '">Load More</button>';
		}

		// Reset post data and return the output
		wp_reset_postdata();
		return ob_get_clean();
	}
}
