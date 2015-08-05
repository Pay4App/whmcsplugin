Pay4App WHMCS Plugin
===================

## Install

Install the module as you would any other WHMCS plugin. Then in the Pay4App module settings enter your Pay4App Merchant details as they are provided you in your Pay4App merchant dashboard.

## Configure callback

You must configure a callback URL in your Pay4App merchant dashboard. You can do this from the *API Setting* menu to the left under the *Settings* menu.

If your clients access your WHMCS from http://example.com/whmcs, then the callback is at http://example.com/whmcs/modules/gateways/callback/pay4app.php

Don't forget to check *Confirm change* before clicking Save, and make sure Callback Maintenance Mode is *off*