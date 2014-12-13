<?php
/**
 * Plugin Name: ON Lab Subscription Box
 * Plugin URI: http://github.com/nicolasalliaume/ONSubscriptionBox
 * Description: Simple email subscription box
 * Version: 1.0.0
 * Author: Nicolás Alliaume @ ON Lab
 * Author URI: http://on-lab.com
 * License: MIT
 */

defined('ABSPATH') or die("No script kiddies please!");

if (!class_exists("OnSubscrptionBoxIdGenerator")) {
	class OnSubscrptionBoxIdGenerator {
		public static $currId = 1;
		public function generate_id() {
			$id = self::$currId;
			self::$currId = self::$currId + 1;
			return $id;
		}
	}
}

if (!function_exists('on_get_val')) {
	function on_get_val($key, $arr, $default) {
		if (array_key_exists($key, $arr))
			return $arr[$key];
		return $default;
	}
}

function on_subscription_box_ajax() 
{	
	// send email
	$subscriber = $_POST['subscriber'];
	$admin_email = get_settings('admin_email');
	$subject = $_POST['subject'];
	
	if (wp_mail( $admin_email, $subject, "New subscription from: " . $subscriber )) {
		die (json_encode(array('success' => true)));
	}
	die (json_encode(array('success' => false)));
}
add_action( 'wp_ajax_on_subscription_box', 'on_subscription_box_ajax' );
add_action( 'wp_ajax_nopriv_on_subscription_box', 'on_subscription_box_ajax' );


if (!function_exists('on_subscription_box_get')) 
{
	function on_subscription_box_get($attrs) 
	{
		// subject: the subject of the email
		//			default: "New subscription"
		// label: the label to use in the subscribe button
		//			default: "Subscribe"
		// placeholder: the placeholder to use in the subscribe input
		//			default: "Email"
		// class: a string containing a class or classes to add to
		// 			the subscription box
		//			default: ""
		
		if (!$attrs) $attrs = array();
		
		$id = OnSubscrptionBoxIdGenerator::generate_id();
		$class = on_get_val('class', $attrs, "");
		$placeholder = on_get_val('placeholder', $attrs, "Email");
		$label = on_get_val('label', $attrs, "Subscribe");
		$subject = on_get_val('subject', $attrs, "New subscription");
		
		return '
		<style type="text/css">
			.on_subscription_box,
			.on_subscription_box .inner,
			.on_subscription_box input,
			.on_subscription_box button {
				display: inline-block;
				font-size: 0px;
				font-weight: normal;
			}
			.on_subscription_box button {
				cursor: pointer;
				font-size: 14px;
				height: 30px;
				box-sizing: border-box;
				padding: 0 8px;
				margin: 0;
				background-color: #ddd;
				border: none;
				color: #666;
				-webkit-transition: all 200ms;
			    -moz-transition: all 200ms;
			    -o-transition: all 200ms;
			    transition: all 200ms;
			}
			.on_subscription_box button:hover {
				background-color: #aaa;
				color: #fff;
			}
			.on_subscription_box input {
				font-size: 14px;
				padding: 4px 8px;
				width: 200px;
				height: 30px;
				box-sizing: border-box;
				border: 1px solid #ddd;
			}
			.on_subscription_box .success,
			.on_subscription_box .error {
				font-size: 14px;
				display: none;
			}
		</style>
		<div class="on_subscription_box ' . $class . '" data-on-subscription-id="' . $id . '">
			<div class="inner">
				<input type="email" placeholder="' . $placeholder . '" />
				<button type="button">' . $label . '</button>
				<div class="success"><span>Gracias por suscribirte!</span></div>
				<div class="error"><span>Vuelve a intentarlo</span></div>
			</div>
		</div>
		<script>
			jQuery(document).ready(function(){
				
				var onSubscriptionSubmitAction = function() {
					var $button = jQuery(this);
					var $input = $button.siblings("input"),
						$success = $button.siblings(".success"),
						$error = $button.siblings(".error");
					
					var subscriber = jQuery.trim($input.val());
					
					$button.parent().css({
						opacity: 0.7
					});
					$button.unbind();
					
					if (subscriber != "") {
						jQuery.ajax({
							type: "POST",
							url: ajaxurl, 
							data: {
								action		: "on_subscription_box",
								subscriber	: subscriber,
								subject		: "' . $subject . '"
							},
							dataType: "json",
							success: function(data) {
								console.log(data);
								if (data.success) {
									console.log("Subscription success");
									$success.fadeIn();
								}
								else {
									$error.fadeIn(null, function(){
									setTimeout(function(){
										$error.fadeOut();
									}, 2000);
								});
								}
							},
							error: function(a,b,c) {
								$error.fadeIn(null, function(){
									setTimeout(function(){
										$error.fadeOut();
									}, 2000);
								});
								console.log("Subscription error");
								console.log(a);
								console.log(b);
								console.log(c);
								$button.click(onSubscriptionSubmitAction);
							}
						});
					}
				}
				
				jQuery(".on_subscription_box[data-on-subscription-id=\'' . $id . '\'] button")
					.click(onSubscriptionSubmitAction);
			});
		</script>
		';
	}
}
add_shortcode( 'on_subscription_box', 'on_subscription_box_get' );

?>