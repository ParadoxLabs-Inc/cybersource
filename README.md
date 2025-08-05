[![Latest Stable Version](https://poser.pugx.org/paradoxlabs/cybersource/v/stable)](https://packagist.org/packages/paradoxlabs/cybersource)
[![License](https://poser.pugx.org/paradoxlabs/cybersource/license)](https://packagist.org/packages/paradoxlabs/cybersource)
[![Total Downloads](https://poser.pugx.org/paradoxlabs/cybersource/downloads)](https://packagist.org/packages/paradoxlabs/cybersource)

<p align="center">
    <a href="https://www.paradoxlabs.com"><img alt="ParadoxLabs" src="https://paradoxlabs.com/wp-content/uploads/2020/02/pl-logo-canva-2.png" width="250"></a>
</p>

[CyberSource](https://cybersource.com/), a subsidiary of Visa Inc., is a premier global payment gateway dedicated to servicing ecommerce businesses like yours. CyberSource specializes in enterprise payment services, with fraud and security solutions that connect merchants and payment processors worldwide. CyberSource offers payment acceptance in more than 190 countries, and processes over $400 billion in transactions per year. Their Decision Manager fraud protection suite supports over 300 metrics, including address and IP geolocation.

CyberSource is a paid service, and will charge monthly and per-transaction fees separate from this extension. CyberSource does not publish their fee structures; costs will vary by your contract, features, and payment processor. Please [contact CyberSource](https://www.cybersource.com/en-us/contact-us/sales.html) for more information.

This extension brings CyberSource’s enterprise payment services to Magento 2. This includes enhanced fraud services (Decision Manager), secure payment forms (Secure Acceptance Hosted Checkout), 3D Secure 2 card authentication (Payer Authentication), and extensive stored card functionality (Token Management Services). This gives you and your customers the convenience of stored credit cards, with all the security and protection of CyberSource services.

For full product details, please [visit our website](https://store.paradoxlabs.com/magento2-cybersource-payment-method.html).

Requirements
============

* Magento 2.3 or 2.4 including 2.4.8 (or equivalent version of Adobe Commerce, Adobe Commerce Cloud, or Mage-OS)
* PHP 7.3, 7.4, 8.0, 8.1, 8.2, 8.3, or 8.4
* composer 1 or 2

Features
========

* Pay by credit card
* Save credit cards (tokens) for reuse
* PCI SAQ A eligibility: CyberSource collects all credit card data for you
* Add, edit, and delete saved payment data
* Edit orders and reorder without having to ask the customer for CC info again
* Authorize, Capture, or Save CC Info (without charging) at time of checkout
* Capture funds even after the authorization expires
* Partially invoice orders (including reauthorization on partial invoice)
* Partially refund (online credit memo)
* Send billing address, shipping address, and line items with transactions
* Card Verification Number (CVN) Validation
* Address Verification (AVS)
* Account Updater card updates
* Advanced fraud protection (Decision Manager or Fraud Management Essentials)
* 3D Secure 2 card authentication on standard checkout
* Integrate your systems with Magento REST and GraphQL API support
* Use a different CyberSource account for each website (multi-website support)
* Supports ParadoxLabs [Adaptive Subscriptions](https://store.paradoxlabs.com/magento2-subscriptions-recurring-billing.html) extension

Installation and Usage
======================

In SSH at your Magento base directory, run:

    composer require paradoxlabs/cybersource
    php bin/magento module:enable ParadoxLabs_CyberSource ParadoxLabs_TokenBase
    php bin/magento setup:upgrade

**Before proceeding: [Contact CyberSource](https://www.cybersource.com/en-us/contact-us/sales.html) to sign up for merchant account if you don’t have one already. You will
need to go through the account setup and activation process before you can accept real payments.**

Open your Admin Panel and go to **Stores > Settings > Configuration > Sales > Payment Methods**. If the extension installed correctly, you will see a new setting section near the bottom titled **CyberSource**.

Configuring the CyberSource payment method is a bit of a process: There are four separate APIs involved that you need to generate or share the API credentials for. [Please see the User Manual for setup instructions.](https://store.paradoxlabs.com/media/wysiwyg/ParadoxLabs-CyberSource-M2-user-manual.pdf)

## Applying Updates

In SSH at your Magento base directory, run:

    composer update paradoxlabs/cybersource
    php bin/magento setup:upgrade

These commands will download and apply any available updates to the module.

If you have any integrations or custom functionality based on this extension, we strongly recommend testing to ensure they are not affected.

**If you have modified the template or JS files in any theme**, be sure to update them to match any changes in the extension. Failing to do this may result in errors during checkout or card management.

Changelog
=========

Please see [CHANGELOG.md](https://github.com/ParadoxLabs-Inc/cybersource/blob/master/CHANGELOG.md).

Support
=======

This module is provided free and without support of any kind. You may report issues you've found in the module, and we will address them as we are able, but **no support will be provided here.**

**DO NOT include any API keys, credentials, or customer-identifying in issues, pull requests, or comments. Any personally identifying information will be deleted on sight.**

If you need personal support services, please [buy an extension support plan from ParadoxLabs](https://store.paradoxlabs.com/support-renewal.html), then open a ticket at [support.paradoxlabs.com](https://support.paradoxlabs.com).

Contributing
============

Please feel free to submit pull requests with any contributions. We welcome and appreciate your support, and will acknowledge contributors.

This module is maintained by ParadoxLabs, a Magento solutions provider. We make no guarantee of accepting contributions, especially any that introduce architectural changes.

License
=======

This module is licensed under [APACHE LICENSE, VERSION 2.0](https://github.com/ParadoxLabs-Inc/cybersource/blob/master/LICENSE).
