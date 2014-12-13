ONSubscriptionBox
=================

ONSubscriptionBox is a very simple subscription box for Wordpress. When a person submits the subscription, the admin of the site receives an email.

How to use it
-----------------

Simply add the shortcode '[on_subscription_box]' to any post or page content and the box will appear like magic.

Customization
-----------------

The shortcode allows some parameters to customize it:
  - subject : the subject of the email sent to the admin user
  - label : the label (text) that is used on the subscribe button
  - placeholder : the placeholder to use on the email text input
  - class : one or more classes (separated by spaces, for example, 'box_1 fun_box') to add to the subscription box
  
For example:

  - [on_subscription_box subject="New follower"] : sends an email with subject 'New follower'
  
  - [on_subscription_box label="Follow"] : The button of the subscription box says 'Follow'
  
  
Further customization
-----------------

You're welcome to do any further customization on the php file directly.
