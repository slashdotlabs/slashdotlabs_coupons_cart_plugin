## Coupons Cart Plugin
WordPress plugin that integrates iPay payment gateway to facilitate coupon purchasing on [PROMO.KE](https://promo.co.ke)

### Features

- Adds meta details onto the normal blog post: (coupon name, price and expiry date)
- Appends a redeem coupon form at the end of every post
- Redirects users to iPay payment gateway
- Displays records of paid coupons from customers
- Sends an email to the customers after coupon purchase

### Installation

- Go to the [releases](https://github.com/steekam/slashdotlabs_coupons_cart_plugin/releases) page and download the latest release, or download the repo which might not be stable.
- In your WordPress install, go to ``plugins`` then the ``Add New`` option.
- Upload the zipped folder to install
- Go to the ``plugins`` page and activate the plugin.

### Setup

There are a few settings you need to configure before you can use the plugin features.

__Coupon Details__

The plugin adds a meta box after every post entry for the admin to add the required coupon detials.
<br>[``Coupon Name``, ``Coupon Price``, ``Coupon Expiry Date``]

> For the redeem form to appear, these details need to be set and the coupon hasn't expired.

__iPay Settings__

To link the iPay integration to your merchant account, the plugin required your ``VENDOR ID`` and ``HASHKEY``.
<br> This information is provided to you after you register for your merchant account.

__Mail Settings__

``MAIL FROM NAME`` and ``MAIL FROM EMAIL ADDRESS`` used in the mail headers while sending the coupon emails to customers.

__SMTP Settings__

This is optional in case your WordPress install hasn't configured mail sending properly. The plugin uses the ``wp_mail`` function, so 
you can use your existing mail configuration and ignore this section.

### Testing

At the moment, the iPay integration is still in _demo_ mode. If you don't have an iPay merchant account yet 
you can use ``demo`` as ``VENDOR ID`` and ``demoCHANGED`` as ``HASHKEY`` to test the integration.

In demo mode, iPay provides test Credit Card details to test the complete process.
You can use ``4444444444444444`` as your credit card, the rest of the details can be filled as you please.
The process will complete with success.

For the email sending, you can use your hosting account SMTP credentials or use tools like [mailtrap.io](https://mailtrap.io)
 for a mock inbox.


### Contributors
[Stephen Wanyee](https://github.com/steekam) <br>
[Allan Vikiru](https://github.com/AllanVikiru) <br>
[Sianwa Atemi](https://github.com/sianwa11) <br>
