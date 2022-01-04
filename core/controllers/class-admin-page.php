<?php
/**
 * Controller for admin pages.
 *
 * @link    https://gist.github.com/panoslyrakis/
 * @since   1.0.0
 *
 * @author  Panos Lyrakis
 * @package Gist_Sync\Core\Controllers
 */

namespace Gist_Sync\Core\Controllers;

// Abort if called directly.
defined( 'WPINC' ) || die;

use Gist_Sync\Core\Utils\Abstracts\Base;
use Gist_Sync\Core\Traits\Enqueue;

/**
 * Class Admin_Page
 *
 * @package Gist_Sync\Core\Controllers
 */
abstract class Admin_Page extends Base {
	/**
	 * Use the Enqueue Trait.
	 *
	 * @since 1.0.0
	 */
	use Enqueue;

	/**
	 * The Admin Page's Menu Type.
	 *
	 * @var bool $is_submenu Set to true if page uses submenu.
	 *
	 * @since 1.0.0
	 */
	protected $is_submenu = false;

	/**
	 * The Admin SubPage's Parent Slug.
	 *
	 * @var string $parent_slug The slug of the parent admin menu.
	 *
	 * @since 1.0.0
	 */
	protected $parent_slug;

	/**
	 * The Admin Page's Title.
	 *
	 * @var string $page_title The text to be displayed in the title tags of the page when the menu is selected.
	 *
	 * @since 1.0.0
	 */
	protected $page_title;

	/**
	 * The Admin Menu's Title.
	 *
	 * @var string $menu_title The text to be used for the menu.
	 *
	 * @since 1.0.0
	 */
	protected $menu_title;

	/**
	 * The Admin Menu's capability.
	 *
	 * @var string $capability The capability required for this menu to be displayed to the user.
	 *
	 * @since 1.0.0
	 */
	protected $capability = 'manage_options';

	/**
	 * The Admin Menu's Slug.
	 *
	 * @var string $menu_slug The slug name to refer to this menu by. Should be unique.
	 *
	 * @since 1.0.0
	 */
	protected $menu_slug;

	/**
	 * The Admin Menu's Icon.
	 *
	 * @var string $icon_url The URL to the icon to be used for this menu.
	 *
	 * @since 1.0.0
	 */
	protected $icon_url = '';

	/**
	 * The Admin Menu's position.
	 *
	 * @var int $position The position in the menu order this item should appear.
	 *
	 * @since 1.0.0
	 */
	protected $position = null;

	/**
	 * Init Admin Page
	 *
	 * @since 1.0.0
	 *
	 * @return void Initialize the Admin_Page.
	 */
	public function init() {
		$this->prepare_props();
		$this->actions();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @return void Add the Actions.
	 */
	public function actions() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'admin_submenu' ), 99 );
		add_action( 'admin_init', array( $this, 'boot' ) );
	}

	/**
	 * Set the admin_menu for the Admin Page.
	 *
	 * @since 1.0.0
	 *
	 * @return void Creates the admin page menu(s). Should check if menu or submenu.
	 */
	public function admin_menu() {
		if ( ! $this->is_submenu() ) {
			add_menu_page(
				$this->page_prop( 'page_title' ),
				$this->page_prop( 'menu_title' ),
				$this->page_prop( 'capability' ),
				$this->page_prop( 'menu_slug' ),
				array( $this, 'callback' ),
				$this->page_prop( 'icon_url' ),
				$this->page_prop( 'position' )
			);
		}
	}

	/**
	 * Set the admin_submenu for the Admin Page.
	 *
	 * @since 1.0.0
	 *
	 * @return void Creates the admin sub menu(s). Should check if menu or submenu.
	 */
	public function admin_submenu() {
		if ( $this->is_submenu() ) {
			add_submenu_page(
				$this->page_prop( 'parent_slug' ),
				$this->page_prop( 'page_title' ),
				$this->page_prop( 'menu_title' ),
				$this->page_prop( 'capability' ),
				$this->page_prop( 'menu_slug' ),
				array( $this, 'callback' ),
				$this->page_prop( 'position' )
			);
		}
	}

	/**
	 * Admin init actions.
	 *
	 * @since 1.0.0
	 *
	 * @return void Admin init actions.
	 */
	public function boot() {
		add_action( 'current_screen', array( $this, 'current_screen_actions' ) );
	}

	/**
	 * Current screen actions.
	 *
	 * @since 1.0.0
	 *
	 * @return void Current screen actions.
	 */
	public function current_screen_actions() {
		if ( $this->can_boot() ) {
			$this->prepare_scripts();
		}
	}

	/**
	 * To boot or not to boot?
	 *
	 * @since 1.0.0
	 *
	 * @return boolean Checks if admin page actions/scripts should load. Useful for enqueing scripts.
	 */
	public function can_boot() {
		// Using strpos of the menu_slug so it can be checked dynamically for toplevel or not.
		// Should also take care of translated menu_slugs.
		return (
			is_admin() &&
			is_callable( '\get_current_screen' ) &&
			isset( \get_current_screen()->id ) &&
			strpos( \get_current_screen()->id, $this->menu_slug )
		);
	}

	/**
	 * Get Admin Page's properties.
	 *
	 * @since 1.0.0
	 *
	 * @return string|int|null Returns true if page uses submenu.
	 */
	private function page_prop( $prop ) {
		$properties = $this->get_properties();
		return isset( $properties[ $prop ] ) ? $properties[ $prop ] : null;
	}

	/**
	 * Admin Page's Properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array with all Page's Properties.
	 */
	public function get_properties() {
		return apply_filters(
			'pluginbase/admin_page_props',
			array(
				'parent_slug' => $this->parent_slug,
				'page_title'  => $this->page_title,
				'menu_title'  => $this->menu_title,
				'capability'  => $this->capability,
				'menu_slug'   => $this->menu_slug,
				'icon_url'    => $this->icon_url,
				'position'    => $this->position,
			),
			$this->menu_slug,
			$this
		);
	}


	/**
	 * Returns true if page uses submenu.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Returns true if page uses submenu..
	 */
	private function is_submenu() {
		return $this->is_submenu;
	}

	/**
	 * Register scripts for the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return array Register scripts for the admin page.
	 */
	public function set_scripts() {
		return array();
	}

	/**
	 * Admin Menu Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void The callback function of the Admin Menu Page.
	 */
	abstract public function callback();

	/**
	 * Prepares the properties of the Admin Page.
	 *
	 * @since 1.0.0
	 *
	 * @return void Prepares of the admin page.
	 */
	abstract public function prepare_props();

}
