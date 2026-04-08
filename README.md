# Draad Adreszoeker

Author: Draad Internet & Media B.V.\
Tags: block\
Tested up to: 6.8\
Stable tag: 2.0.8\
Plugin for adding the 'Adreszoeker' functionality to denhaag.nl

## Description

Plugin containing 2 native blocks and 1 legacy block using ACF Blocks for adding the 'Adreszoeker' to any page.\
The 'Adreszoeker' is a tool to lookup what citizens of The Hague can do to save on the energy bill.

The blocks contain a search field where citizens of The Hague can lookup their address and then retrieve the accompanying advice

## Changelog

### 2.0.8

* Added PostCSS and webpack plugins to automatically scope all component CSS to `.draad-adreszoeker`
* Fixed JS-inlined styles from @gemeente-denhaag and @utrecht packages leaking globally

### 2.0.7

* Fixed Utrecht component styles leaking globally by scoping them to the `.draad-adreszoeker` wrapper

### 2.0.6

* Fixed button and component styles leaking globally by scoping design system tokens to the `.draad-adreszoeker` block wrapper

### 2.0.5

* Fixed 403 errors on AJAX endpoints by adding nonce verification and including `_wpnonce` in frontend requests
* Reverted minimum WordPress requirement to 6.8

### 2.0.4

* Fixed mixed content error on sites behind a reverse proxy by forcing HTTPS on admin-ajax URL
* Fixed CSS layout: grid columns only applied when image is present in formulier block
* Fixed CSS layout: negative margin only applied to non-first output block
* Fixed background color on first output block surface

### 2.0.3

* Fixed mixed content error

### 2.0.2

* Fixed deprecation warning: Deprecated: Creation of dynamic property Draad_Adreszoeker::$import_handler is deprecated

### 2.0.1

* Improved compliance with Wordpress coding best practices

### 2.0.0

* Added import page for the addresses database table under Tools

* Added new Gutenberg block for the 'Adreszoeker' form using the The hague design system

* Added new Gutenberg block for the 'Adreszoeker' output using the The hague design system

### 1.0.0

* Release
