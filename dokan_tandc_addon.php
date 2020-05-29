<?php
/**
 * Plugin Name: Dokan T&C addon
 * Plugin URI: https://simplify-everything.com
 * Description: A simple plugin for to add the suppliers t&c checkboxes
 * Author: Max Page
 * Author URI: http://simplify-everything.com
 * Version: 2.0.0
 * Text Domain: theme-text-domain
 *
 * Copyright: (c) Simplify-Everything OG (info@simplify-everything.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   Simplify_settings_plugin
 * @author    Max Page
 * @category  Admin
 * @copyright (c) Simplify-Everything OG (info@simplify-everything.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 */

//checkout terms and conditions checkboxes
add_action( 'woocommerce_review_order_before_submit','show_tandc_checkbox' );
function show_tandc_checkbox(){

  ?><h5 id="Supplier_checkbox" style="font-size:1rem">
  <?php printf( __( 'Terms and Conditions:<span style="color:#FF0000"> *</span>', 'theme-text-domain' )); ?>
  </h5>
      <?php
          //create empty array to use later
          $authors = array ();
          //populate the array
          global $WC_checkout;
          $cart_object = WC()->cart;
          // Checking each cart item for author
          foreach($cart_object->get_cart() as $cart_item_key => $cart_item){
              // get the product ID
              $product = $cart_item['data'];
              $product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
              // Get the product post object to get the post author
              $post_obj = get_post( $product_id );
              $post_author = $post_obj->post_author;
              //add the post_author to the array we created above
              $authors [] = $post_author;
          }
              //remove duplicates in the authors array
              $unique_authors = array_unique($authors);
              //for each author print a checkbox
              foreach($unique_authors as $suppliers){
              $author  = get_user_by( 'id', $suppliers );
                ?>
                      <p class="form-row terms" style="font-size:0.7rem">
                      <input type="checkbox" class="input-checkbox" name="resellercheck2" id="resellercheck">
                      <?php printf( __( 'I&#39;ve read and accept <a href="%stoc" target="_blank">%ss terms & conditions</a> <span style="color:#FF0000"> *</span>', 'theme-text-domain' ), dokan_get_store_url( $author->ID ), $author->display_name ); ?>
                      </p>
                <?php
              }

              //add a checkbox for our t&c's
              $text = __('indicates a required checkbox','theme-text-domain');
              ?>
              <p class="form-row terms" style="font-size:0.7rem">
              <input type="checkbox" class="input-checkbox" name="resellercheck1" id="resellercheck">
              <?php printf( __( 'I&#39;ve read and accept the <a href="https://www.YOURSITEHERE.com/en/terms-and-conditions" target="_blank">terms & conditions</a> and I understand that this product is sold by the Suppliers and not directly by us. <span style="color:#FF0000"> *</span>', 'theme-text-domain' )); ?>
              </p><br>
              <p class="required-checkout" style="font-size:0.7rem;text-align:center;">
              <?php echo '<span style="color:#FF0000">* </span>'.$text.'<br />';?>
              </p>
              <?php
}

// Show notice if customer does not tick the checkout boxes
add_action( 'woocommerce_before_checkout_process', 'not_approved_delivery' );

function not_approved_delivery() {
    if ( ! (int) isset( $_POST['resellercheck2'] ) ) {
        wc_add_notice( __( 'You must accept the Suppliers terms and conditions. As such, your payment has not been accepted' , 'theme-text-domain' ), 'error' );
    }
    if ( ! (int) isset( $_POST['resellercheck1'] ) ) {
        wc_add_notice( __( 'You must accept the terms and conditions and understand who sells this product. As such, your payment has not been accepted' , 'theme-text-domain' ), 'error' );
    }
}

?>
