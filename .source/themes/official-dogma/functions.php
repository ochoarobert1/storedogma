<?php

/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0');

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles()
{

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[],
		HELLO_ELEMENTOR_CHILD_VERSION
	);
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);

function hello_elementor_register_sidebars() {
     register_sidebar( array(
         'name' => 'Shop Sidebar',
         'id' => 'shop_sidebar',
         'before_widget' => '<div>',
         'after_widget' => '</div>',
         'before_title' => '<h2 class="rounded">',
         'after_title' => '</h2>',
	 ) );
}

add_action( 'widgets_init', 'hello_elementor_register_sidebars' );

add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
   add_theme_support( 'woocommerce' );
}  

add_filter( 'body_class', 'add_slug_body_class' );
function add_slug_body_class( $classes ) {
	global $post;
	if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
}


add_filter( 'manage_woocommerce_page_wc-orders_columns', 'add_wc_order_list_custom_column' );
function add_wc_order_list_custom_column( $columns ) {
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;

        if( $key ===  'order_status' ){
            // Inserting after "Status" column
            $reordered_columns['order_product'] = __( 'Products','theme_domain');
			$reordered_columns['shipping_country'] = __( 'Shipping Country','theme_domain');
			$reordered_columns['shipping_city'] = __( 'Shipping City','theme_domain');
			$reordered_columns['pickup_venue'] = __( 'Pickup Local Show','theme_domain');
        }
    }
    return $reordered_columns;
}

add_action('manage_woocommerce_page_wc-orders_custom_column', 'display_wc_order_list_custom_column_content', 10, 2);
function display_wc_order_list_custom_column_content( $column, $order ){
    switch ( $column )
    {
        case 'order_product' :
            $order_items = $order->get_items();
			if ( !is_wp_error( $order_items ) ) {
				foreach( $order_items as $order_item ) {
					echo $order_item['quantity'] .' × <a href="' . admin_url('post.php?post=' . $order_item['product_id'] . '&action=edit' ) . '">'. $order_item['name'] .'</a><br />';
				}
			}
            break;
		 case 'shipping_country' :
			$country = WC()->countries->countries[ $order->get_shipping_country() ];
            echo $country;
            break;
		 case 'shipping_city' :
            $city = $order->get_shipping_city();
			echo $city;
            break;
		 case 'pickup_venue' :
			global $wpdb;

			$order_id = $order->get_id();
			
			$pickup_selected = $wpdb->get_var( $wpdb->prepare(
				"SELECT order_item_name
				FROM {$wpdb->prefix}woocommerce_order_items
				WHERE order_id = %d
				AND order_item_type = 'shipping'",
				$order_id
			));
			echo $pickup_selected;
			break;
    }
}

function get_all_country_orders_from_wc() {
	$country = [
		'' => __('Filter By Country', 'woocommerce')
	];
	$orders = wc_get_orders( array(
		'limit' => -1,
		'return' => 'ids',
		'orderby' => 'date',
		'order' => 'ASC'
	));
	
	foreach ($orders as $order) :
		$current_order = wc_get_order( $order );
		$order_data = $current_order->get_data();
		if ($order_data['shipping']['country'] !== '') {
			$country[$order_data['shipping']['country']] = WC()->countries->countries[ $order_data['shipping']['country'] ];
		}
	endforeach;
	
	return $country;
}

add_action( 'woocommerce_order_list_table_restrict_manage_orders', 'show_is_first_order_checkbox', 5 );
function show_is_first_order_checkbox() {
	$options = get_all_country_orders_from_wc();
    $selected = isset($_GET['country']) ? esc_attr($_GET['country']) : '';
    
    echo '<select name="country" id="dropdown_shop_order_metadata">';
    foreach( $options as $value => $label_name ) {
		if ($label_name == null) {
			printf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), 'Filter By Country');	
		} else {
			printf('<option value="%s" %s>%s</option>', $value, selected($selected, $value, false), $label_name);	
		}
        
    }
    echo '</select>';
}   

add_filter('woocommerce_order_query_args', 'filter_woocommerce_orders_in_the_table');
function filter_woocommerce_orders_in_the_table( $query_args ) {
		if ( isset($_GET['country'])) {
			$query_args['shipping_country'] = $_GET['country'];
		}
    return $query_args;
}

/**
 * Add Custom Fields to Edit Order Page
 *
 * @author Misha Rudrastyh
 * @link https://rudrastyh.com/woocommerce/customize-order-details.html
 */
add_action( 'woocommerce_admin_order_data_after_order_details', 'misha_editable_order_meta_general' );

function misha_editable_order_meta_general( $order ){

	?>
		<br class="clear" />
		<h3>Meet and Greet <a href="#" class="edit_address">Edit</a></h3>
		<?php
			/*
			 * get all the meta data values we need
			 */
			$mg_country = $order->get_meta( 'mg_country' );
			$mg_date = $order->get_meta( 'mg_date' );
		?>
		<div class="address">
			<p><strong>Country: </strong><?php echo $mg_country; ?></p>
			<p><strong>Date: </strong><?php echo $mg_date; ?></p>
		</div>
		<div class="edit_address">
			<?php
				woocommerce_wp_text_input( array(
					'id' => 'mg_country',
					'label' => 'Country:',
					'value' => $mg_country,
					'wrapper_class' => 'form-field-wide'
				) );
	
				woocommerce_wp_text_input( array(
					'id' => 'mg_date',
					'label' => 'Date:',
					'value' => $mg_date,
					'wrapper_class' => 'form-field-wide'
				) );
			?>
		</div>
	<?php 
}

add_action( 'woocommerce_process_shop_order_meta', 'misha_save_general_details' );

function misha_save_general_details( $order_id ){
	
	$order = wc_get_order( $order_id );
	$order->update_meta_data( 'mg_country', wc_clean( $_POST[ 'mg_country' ] ) );
	$order->update_meta_data( 'mg_date', wc_clean( $_POST[ 'mg_date' ] ) );
	// wc_clean() and wc_sanitize_textarea() are WooCommerce sanitization functions
	$order->save();
	
}

add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );
add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );
 
 // Apply custom args to main query
function custom_woocommerce_get_catalog_ordering_args( $args ) {

	$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
 
	if ( 'oldest_to_recent' == $orderby_value ) {
		$args['orderby'] = 'date';
		$args['order'] = 'ASC';
	}
 
	return $args;
}
 
// Create new sorting method
function custom_woocommerce_catalog_orderby( $sortby ) {
	
	$sortby['oldest_to_recent'] = __( 'Oldest to most recent', 'woocommerce' );
	
	return $sortby;
}