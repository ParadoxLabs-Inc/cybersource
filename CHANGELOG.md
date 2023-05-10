# ParadoxLabs_CyberSource Changelog

## 1.3.1 - March 10, 2023
- Added compatibility for Magento 2.4.6.
- Changed GraphQL data assignment to allow order placement in a separate mutation. (Thanks Alfredo)
- Fixed GraphQL tokenbase_id handling during order placement. (Thanks Damien, Tony)
- Fixed incomplete billing address causing a payment error on admin order.
- Fixed multi-website config scope issues with Secure Acceptance.
- Fixed PHP 8.1 compatibility error on admin order.
- Fixed possible duplicate checkout submission by keyboard input.
- Fixed transaction being voided in error if 'quote failure' event runs despite the order saving successfully. (Thanks Michael)
- Fixed zero-total checkout handling.

## 1.3.0 - April 6, 2022
- Removed compatibility for Magento 2.2 and below. For anyone updating from Magento 2.2 or below, update this extension to the previous version before updating Magento, then update Magento and the latest extension version.
- Added form key validation to GetParams requests (thanks Ian).
- Changed card pruning delay from 120 to 180 days to reflect new Authorize.net policy.
- Fixed 3D Secure 2.x capability.
- Fixed addresses to send full region name to SOAP API for intl support.
- Fixed decision manager running and possibly requiring CVV on subscription rebilling.
- Fixed fraud updates checking wrong transaction ID field, causing PHP errors.
- Fixed handling of payment methods on free orders.
- Fixed possible session/card loading issues when adding a new card, due to a field conflict.
- Fixed response code 478 step-up for Payer Authentication.

## 1.2.0 - February 16, 2022
- Added compatibility for Magento 2.4.4 + PHP 8.1.
- Added auto voiding of transactions at checkout when third party code throws an order processing exception.
- Added configuration to change the delay for inactive card pruning.
- Added GraphQL support for 3D Secure/Payer Authentication.
- Added security-related settings to admin checkout configuration.
- Added setting to enable/disable fraud check on card storage.
- Improved error logging.
- Improved state/region handling and compatibility.
- Fixed 'get payment update' button under multi-website configuration.
- Fixed ability to use TokenBase methods for free orders.
- Fixed address input issues possibly resulting in infinite load/error loop.
- Fixed card save error handling when receiving a 'call card issuer' response.
- Fixed config not loading from proper scope on admin/API requests.
- Fixed error parameter replacement on checkout for complex error messages. (Thanks Navarr)
- Fixed fingerprint ID including merchant ID in API calls.
- Fixed missing CVV failure on partial capture by disabling decision manager for follow-up transactions.
- Fixed possible 'Please enter your credit card CVV' error at invoicing/refunding.
- Fixed possible PHP notice in address input processing.
- Fixed scheduled tasks not supporting multi-account configuration.
- Fixed void to instead run auth reversal if transaction is uncaptured.

## 1.1.3 - August 23, 2021
- Fixed 'please enter CVV' validation error when capturing a card modified since order placement, with require CVV enabled.
- Fixed card info not displaying in My Payment Data on `Magento/blank` and derived themes.
- Fixed expired cards not showing any indicator.
- Fixed post-checkout registration also catching normal customer registration, causing 'unable to load card' errors.
- Fixed transaction info not showing on admin order view on Magento 2.4.2+.
- Replaced deprecated escapeQuote calls to fix Magento 2.4.3 compatibility.

## 1.1.2 - April 21, 2021
- Fixed validation error after invoice.

## 1.1.1 - March 31, 2021
- Changed 'Payment Data'/'My Payment Data' to 'Payment Options'/'My Payment Options'.
- Fixed checkout validation errors on Magento 2.3.3-2.4 resulting from core bug #28161.
- Fixed errors on void/cancel if card no longer exists.
- Fixed payment failed emails.

## 1.1.0 - January 6, 2021
- Added Account Updater support.
- Added selected-card data to GraphQL cart SelectedPaymentMethod.
- Fixed 'please enter valid email' popup when creating a new-customer order in admin.
- Fixed card association and authorization issues when changing the email on admin checkout.
- Fixed IE11 compatibility issue on checkout form.
- Fixed Magento 2.2 compatibility issue since 4.3.2 (GraphQL reference).
- Fixed payment failed emails by changing checkout exceptions from PaymentException to LocalizedException, to follow core behavior.

## 1.0.3 - October 20, 2020
- Fixed compatibility issue with Magento 2.4.1 and Klarna 7.1.0 that broke cart and checkout.
- Fixed CVV type validation for stored cards.
- Fixed exceptions on void preventing order cancellation.
- Fixed GraphQL not being considered a frontend area, for client IP handling.
- Fixed incomplete phone number causing error loop on checkout.
- Fixed sporadic 'unable to load card' errors when adding a new credit card.

## 1.0.2 - August 5, 2020
- Added Magento 2.4 compatibility.
- Added CSP allowed hosts.
- Fixed PHP notice with company field disabled.
- Fixed transaction failures with CVV requirement disabled.

## 1.0.1 - May 20, 2020
- Fixed "Email already exists" error after placing an admin order for a new customer and getting a payment failure.
- Fixed admin order payment form not initializing correctly on new orders.
- Fixed API test responses getting logged as errors.
- Fixed possible error with missing card instrument ID response data.
- Fixed potential false positives in address change detection.

## 1.0.0 - March 20, 2020
- Initial release for Magento 2.
