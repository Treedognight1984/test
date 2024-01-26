<?php
namespace CustomSearch;

class DisplaySettings {

	public function __construct() {
		add_action('admin_init', array($this, 'admin_init'));
	}

	public function admin_init() {
		register_setting('general', 'custom_search_display_mode');
		add_settings_field(
			'custom_search_display_mode_field',
			'Display Mode for Custom Search',
			array($this, 'display_mode_field_callback'),
			'general'
		);
	}

	public function display_mode_field_callback() {
		$current_value = get_option('custom_search_display_mode', 'list');
		echo '
            <select name="custom_search_display_mode">
                <option value="list" ' . selected($current_value, 'list', false) . '>List</option>
                <option value="grid" ' . selected($current_value, 'grid', false) . '>Grid</option>
            </select>
        ';
	}

	/**
	 * Store display settings in the database.
	 *
	 * @param int $user_id User ID.
	 * @param array $settings Display settings to store.
	 */
	public function store_settings($user_id, $settings) {
		// Validate and sanitize settings
		$settings = $this->sanitize_settings($settings);

		// Update user meta with settings
		update_user_meta($user_id, 'custom_search_display_settings', $settings);
	}

	/**
	 * Retrieve display settings from the database.
	 *
	 * @param int $user_id User ID.
	 * @return array Stored settings.
	 */
	public function get_settings($user_id) {
		// Retrieve settings from user meta
		$settings = get_user_meta($user_id, 'custom_search_display_settings', true);

		// Return settings or default if not set
		return $settings ? $settings : $this->default_settings();
	}

	/**
	 * Provide default settings.
	 *
	 * @return array Default settings.
	 */
	private function default_settings() {
		return array(
			'view' => 'list', // Default to list view
		);
	}

	/**
	 * Sanitize and validate settings.
	 *
	 * @param array $settings Settings to sanitize.
	 * @return array Sanitized settings.
	 */
	private function sanitize_settings($settings) {
		// Sanitize each setting
		$sanitized = array();
		if (isset($settings['view'])) {
			$sanitized['view'] = in_array($settings['view'], ['list', 'grid']) ? $settings['view'] : 'list';
		}

		return $sanitized;
	}

	/**
	 * Save the display mode setting.
	 *
	 * @param int $user_id User ID.
	 * @param string $mode Display mode (list or grid).
	 */
	public function save_display_mode($user_id, $mode) {
		$valid_modes = ['list', 'grid'];
		if (in_array($mode, $valid_modes)) {
			update_user_meta($user_id, 'custom_search_display_mode', $mode);
		}
	}

}
