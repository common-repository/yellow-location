<?php
/**
 * Plugin Name: Yellow Location
 * Description: Create a woocommerce custom shipping method plugin
 * Version: 1.0
 * Author: Codeoasis
 * Tested up to: 5.0.3
 * Author URI: https://www.codeoasis.com/
 * Text Domain: yellow_shipping
 */
if ( ! defined( 'WPINC' ) ){
 die('security by preventing any direct access to your plugin file');
}
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
 

function YS_yellow_scripts() {
    if( function_exists( 'is_checkout' ) && is_checkout()){
        wp_enqueue_style( 'yellow-css', plugin_dir_url( __FILE__ ) . '/css/style.css', array(), '' );
        wp_enqueue_script( 'yellow-js', plugin_dir_url( __FILE__ ) . '/js/script-babel.js', array(), '1.0.0', true );
        wp_localize_script('yellow-js', 'yellowObject', array(
            'pluginsUrl' => plugin_dir_url(__FILE__),
        ));
    }
}


add_action( 'wp_enqueue_scripts', 'YS_yellow_scripts', 200 );    

 
add_action( 'woocommerce_after_checkout_form', 'YS_yellow_checkout');
 
function YS_yellow_checkout() { 

    include( plugin_dir_path( __FILE__ ) . '/yellow-page.php');
}





add_action( 'woocommerce_after_shipping_rate', 'YS_checkout_shipping_additional_field', 20, 2 );
function YS_checkout_shipping_additional_field( $method, $index )
{

    // print_r($method->get_id());

    if( $method->get_id() == 'yellow_shipping' && is_checkout() ){ ?>
        <br>
        <button type="button" onClick="modalServiceButtonClick(this)" data-modal-trigger="form">
        
        <?php
        $all_options = wp_load_alloptions();
        $my_options  = array();
         
        foreach ( $all_options as $name => $value ) {
            if ( stristr( $name, 'woocommerce_yellow_shipping' ) ) {
                $my_options[ $name ] = $value;
            }
        }
         
        foreach( $my_options as $key => $value ){
            
            $options = get_option($key);

           $my_button = $options['button'];

           echo $my_button;
        }
        ?>

        </button>

        

        



        <div id="locationInput" class="location--input"></div>
        
   <?php }
}


// Embedded jQuery script
add_action( 'wp_footer', 'YS_checkout_delivery_date_script' );
function YS_checkout_delivery_date_script() {
    // Only checkout page
    if( ! ( is_checkout() && ! is_wc_endpoint_url() ) ) return;
    ?>
    <script type="text/javascript">
    jQuery( document.body ).on( 'updated_checkout', function(){
        var a = 'input[name^="shipping_method"]', b = a+':checked',
            c = 'yellow_shipping',
            d = '#place_order';

        // Utility function that show or hide the delivery date
        function showHideDeliveryDate() {
            if( jQuery(b).val() == c && jQuery('#locationInput').text().trim().length <= 0) {

                jQuery(d).prop('disabled', true);
                
            }
            
            else{
                
                jQuery(d).prop('disabled', false);
                console.log('Chosen shipping method: '+jQuery(b).val()); // <== Just for testing (to be removed)
            }
        }

        // 1. On start
        showHideDeliveryDate();

        
    });
    </script>
    <?php
}









add_action('woocommerce_shipping_init', 'YS_yellow_shipping_method');
function YS_yellow_shipping_method() {

    if ( ! class_exists( 'WC_Yellow_Shipping_Method' ) ) {
        class WC_Yellow_Shipping_Method extends WC_Shipping_Method {

            public function __construct( $instance_id = 0) {
                $this->id = 'yellow_shipping';
                $this->instance_id = absint( $instance_id );
                $this->domain = 'yellow_shipping';
                $this->method_title = __( 'Yellow Shipping', $this->domain );
                $this->method_description = __( 'Shipping method to be used where the exact shipping amount needs to be quoted', $this->domain );
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );
                $this->init();
            }

            ## Load the settings API
            function init() {
                $this->init_form_fields();
                $this->init_settings();
                
                $this->enabled = $this->get_option( 'enabled', $this->domain );
                $this->title   = $this->get_option( 'title', $this->domain );
                $this->button  = $this->get_option( 'button', $this->domain );
                $this->cost    = $this->get_option( 'cost', $this->domain );
                $this->info    = $this->get_option( 'info', $this->domain );
                add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
             }

            function init_form_fields() {
                $this->instance_form_fields = array(
                    'title' => array(
                        'type'          => 'text',
                        'title'         => __('Title', $this->domain),
                        'description'   => __( 'Title to be displayed on site.', $this->domain ),
                        'default'       => __( 'YELLOWBOX ', $this->domain ),
                    ),

                    'button' => array(
                        'type'          => 'text',
                        'title'         => __('Button', $this->domain),
                        'description'   => __( 'Button text to be displayed on site.', $this->domain ),
                        'default'       => __( 'בחירת נקודת איסוף', $this->domain ),
                    ),

                    'cost' => array(
                        'type'          => 'number',
                        'title'         => __('Coast', $this->domain),
                        'description'   => __( 'Enter a cost', $this->domain ),
                        'default'       => '',
                    ),
                );
            }

            public function calculate_shipping( $packages = array() ) {
                $rate = array(
                    'id'       => $this->id,
                    'label'    => $this->title,
                    'cost'     => $this->cost,
                    'calc_tax' => 'per_item'
                );
                $this->add_rate( $rate );
            }
        }
    }
}

add_filter('woocommerce_shipping_methods', 'YS_add_yellow_shipping');
function YS_add_yellow_shipping( $methods ) {
    $methods['yellow_shipping'] = 'WC_Yellow_Shipping_Method';
    return $methods;
}





/**
 * Add the field to the checkout
 **/
add_action('woocommerce_after_order_notes', 'YS_yellow_my_custom_checkout_field');

function YS_yellow_my_custom_checkout_field( $checkout ) {
	
	
				
	/**
	 * Output the field. This is for 1.4.
	 *
	 * To make it compatible with 1.3 use $checkout->checkout_form_field instead:
	 
	 $checkout->checkout_form_field( 'my_field_name', array( 
	 	'type' 			=> 'text', 
	 	'class' 		=> array('my-field-class orm-row-wide'), 
	 	'label' 		=> __('Fill in this field'), 
	 	'placeholder' 	=> __('Enter a number'),
	 	));
	 **/
	woocommerce_form_field( 'yellow_Address_Id', array( 
        'type' 			=> 'text',
        'id'            =>  'yellowAddressId',
		'class' 		=> array('location--form_hide orm-row-wide'), 
		'label' 		=> __(''), 
		'placeholder' 	=> __('מזהה'),
        ), $checkout->get_value( 'yellow_Address_Id' ));


        woocommerce_form_field( 'selected_station_city', array( 
            'type' 			=> 'text', 
            'id'            =>  'selected-station-city',
            'class' 		=> array('location--form_hide orm-row-wide'), 
            'label' 		=> __(''), 
            'placeholder' 	=> __('עיר'),
            ), $checkout->get_value( 'selected_station_city' ));

            woocommerce_form_field( 'selected_station_address', array( 
                'type' 			=> 'text',
                'id'            =>  'selected-station-address', 
                'class' 		=> array('location--form_hide orm-row-wide'), 
                'label' 		=> __(''), 
                'placeholder' 	=> __('כתובת'),
                ), $checkout->get_value( 'selected_station_address' ));

                woocommerce_form_field( 'selected_station_name', array( 
                    'type' 			=> 'text',
                    'id'            =>  'selected-station-name', 
                    'class' 		=> array('location--form_hide orm-row-wide'), 
                    'label' 		=> __(''), 
                    'placeholder' 	=> __('שם'),
                    ), $checkout->get_value( 'selected_station_name' ));
        
	
}
/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'YS_yellow_my_custom_checkout_field_process');
function YS_yellow_my_custom_checkout_field_process() {
	global $woocommerce;
	
	// Check if set, if its not set add an error. This one is only requite for companies
	if ($_POST['billing_company'])
		if (!$_POST['yellow_Address_Id'] || !$_POST['selected_station_city'] || !$_POST['selected_station_address'] || !$_POST['selected_station_name']) 
			$woocommerce->add_error( __('Please enter your XXX.') );
}

/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'YS_yellow_my_custom_checkout_field_update_order_meta');
function YS_yellow_my_custom_checkout_field_update_order_meta( $order_id ) {
    if (!empty( $_POST['yellow_Address_Id'])) {
        update_post_meta($order_id, 'yellow_Id', sanitize_text_field($_POST['yellow_Address_Id']));
    }
    if (!empty( $_POST['selected_station_city'])) {
        update_post_meta($order_id, 'yellow_city', sanitize_text_field($_POST['selected_station_city']));
    }
    if(!empty($_POST['selected_station_address'])) {
        update_post_meta($order_id, 'yellow_address', sanitize_text_field($_POST['selected_station_address']));
    }
    if (!empty( $_POST['selected_station_name'])) {
        update_post_meta($order_id, 'yellow_name', sanitize_text_field($_POST['selected_station_name']));
    }
}



/**
 * Display field value on the order edit page
 */



add_action( 'woocommerce_admin_order_data_after_order_details', 'YS_editable_order_meta_general' );
 
function YS_editable_order_meta_general( $order ){  ?>
 
		<br class="clear" />
		<h4>Yellow Shipping <a href="#" class="edit_address">Edit</a></h4>
		<?php 
			/*
			 * get all the meta data values we need
			 */ 
			
			$yellow_id = get_post_meta( $order->id, 'yellow_Id', true );
			$yellow_city = get_post_meta( $order->id, 'yellow_city', true );
            $yellow_address = get_post_meta( $order->id, 'yellow_address', true );
            $yellow_name = get_post_meta( $order->id, 'yellow_name', true );
		?>
		<div class="address">
			
				
					<p><strong>Yellow Id:</strong> <?php echo $yellow_id ?></p>
					<p><strong>Yellow City:</strong> <?php echo $yellow_city ?></p>
                    <p><strong>Yellow Address:</strong> <?php echo $yellow_address ?></p>
                    <p><strong>Yellow Name:</strong> <?php echo $yellow_name ?></p>
				
		</div>
		<div class="edit_address"><?php
 
			
            woocommerce_wp_text_input( array(
				'id' => 'yellow_id',
				'label' => 'Yellow Id',
				'value' => $yellow_id,
				'wrapper_class' => 'form-field-wide'
            ) );
            
            woocommerce_wp_text_input( array(
				'id' => 'yellow_city',
				'label' => 'Yellow City',
				'value' => $yellow_city,
				'wrapper_class' => 'form-field-wide'
			) );

 
			woocommerce_wp_text_input( array(
				'id' => 'yellow_address',
				'label' => 'Yellow Address',
				'value' => $yellow_address,
				'wrapper_class' => 'form-field-wide'
			) );
 
			woocommerce_wp_text_input( array(
				'id' => 'yellow_name',
				'label' => 'Yellow Name',
				'value' => $yellow_name,
				'wrapper_class' => 'form-field-wide'
			) );
 
		?></div>
 
 
<?php }
 
    add_action( 'woocommerce_process_shop_order_meta', 'YS_save_general_details' );
    
    function YS_save_general_details( $ord_id ){
        
        update_post_meta( $ord_id, 'yellow_id', wc_clean( $_POST[ 'yellow_id' ] ) );
        update_post_meta( $ord_id, 'yellow_city', wc_clean( $_POST[ 'yellow_city' ] ) );
        update_post_meta( $ord_id, 'yellow_address', wc_clean( $_POST[ 'yellow_address' ] ) );
        update_post_meta( $ord_id, 'yellow_name', wc_clean( $_POST[ 'yellow_name' ] ) );
        
    }

    function YS_register_settings() {
        add_option( 'YS_google_map', '');
        register_setting( 'YS_options_group', 'YS_google_map', 'YS_callback' );
    }
    add_action( 'admin_init', 'YS_register_settings' );

    function YS_register_options_page() {
        add_options_page('Yellow Location', 'Yellow Location', 'manage_options', 'YS', 'YS_options_page');
    }
    add_action('admin_menu', 'YS_register_options_page');

    function YS_options_page() { ?>
        <div>
            <?php screen_icon(); ?>
            <h2>Yellow Location Settings</h2>
            <form method="post" action="options.php">
                <?php settings_fields( 'YS_options_group' ); ?>
                <h3>Google Map</h3>
                <table>
                    <tr valign="top">
                        <th scope="row"><label for="YS_google_map">Use my Google Map</label></th>
                        <td><input type="checkbox" id="YS_google_map" name="YS_google_map" value="1" <?php checked(1, get_option('YS_google_map'), true); ?> /></td>
                    </tr>
                </table>
                <?php  submit_button(); ?>
            </form>
        </div>
    <?php }
}