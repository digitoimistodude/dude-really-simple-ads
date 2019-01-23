# Really simple ads
![version_1.0.0-beta](https://img.shields.io/badge/Version-1.0.0--beta-orange.svg) ![Tested_up_to WordPress_4.6](https://img.shields.io/badge/Tested_up_to-WordPress_4.6-blue.svg?style=flat-square) ![Compatible_with PHP_7.0](https://img.shields.io/badge/Compatible_with-PHP_7.0-green.svg?style=flat-square)

[Digitoimisto Dude Oy](https://www.dude.fi) is a Finnish boutique digital agency in the center of Jyväskylä.

## Table of contents

- [Please note before using](#please-note-before-using)
- [Features](#features)
- [Usage](#usage)
    - [Register ad places](#register-ad-places)
    - [Get active ad](#get-active-ad)
    - [Adding a shortcode to embed ads into content](#adding-a-shortcode-to-embed-ads-into-content)
    - [Hooks](#hooks)
    - [Set default ad and link](#set-default-ad-and-link)
    - [Disable UTM tags in ad target url](#disable-utm-tags-in-ad-target-url)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [TODO](#todo)

## Please note before using
By using this code bases, you agree that the anything can change to a different direction without a warning.

## Features
Basic feature list includes

- Multiple ad places
- Campaigns containing multiple ads
- Sheculed ads with start and end time
- Sheculed campaigns with start and end time
- Simple view and click counter per ad _(JavaScript)_
- Ad view throttle prevention, so F5 will not bump up the display count _(JavaScript)_
- Private notes for ads and campaigns

## Usage

### Register ad places
Ad places are registered with `drsa_ad_placement_sizes` hook. You should pass nested array containing one array element per each ad place. See basic exmaple below.

```php
add_filter( 'drsa_ad_placement_sizes', 'myprefix_register_ad_places' );
function myprefix_register_ad_places() {
    $spots = array(
        array(
            // title whichs shows when adding ads
            'name'      => __( 'Frontpage default' ),

            // unique identified for this placed, used to get ads
            'id'        => 'frontpage-default',

            // add size, uploaded image will be tested against these values
            'width'     => 480,
            'height'    => 480,
        ),
    );

    return $spots;
}
```

### Get active ad
Getting the active ad is fairly simple, just use `get_the_active_ad` function and pass the used ad place as a paremeter.

If there is active campaign for that ad place, a random active ad assigned to selected place will be returned. When there is no active campaign, active single ad for place is returned. Default ad and link will be returned if no active campaign or ad are found, if there is no default then return is false.

When there is ad, return is array containing the ad place name, image src, target address and click counter class. Simple usage example is below, but you can modify it according to your needs. Click counter class needs to be in the same element with the target href.

```php
$ad = false;
if ( function_exists( 'get_the_active_ad' ) ) {
    $ad = get_the_active_ad( 'frontpage-default' );
}

if ( $ad ) {
    echo '<a href="' . $ad['target'] . '" target="_blank" class="' . $ad['click_counter_class'] . '"><img src="' . $ad['src'] . '" class="ad ad-place-' . $ad['place'] . '"/></a>';
}
```

_Always check the existance of function._

### Adding a shortcode to embed ads into content
You can make your own shortcode to get ads everywhere you want, for example into the content of blog post. Below is simple example of shortcode usage.

```php
add_shortcode( 'ad', 'myprefix_shortcode_show_ad' );
function myprefix_shortcode_show_ad( $atts ) {
    if ( ! function_exists( 'get_the_active_ad' ) ) {
        return; // plugin not active, bail
    }

    if ( empty( $atts ) ) {
        return; // no attributes to shortcode, bail
    }

    // no ad place defined, show error to user and bail if visitor
    if ( ! isset( $atts['place'] ) ) {
        if ( is_user_logged_in() && current_user_can( 'edit_others_posts' ) ) {
            return __( 'No ad place defined', 'textdomain' );
        } else {
            return;
        }
    }

    // get the ad
    $ad = get_the_active_ad( $atts['place'] );

    // no active ad, show error to user and bail if visitor
    if ( ! $ad ) {
        if ( is_user_logged_in() && current_user_can( 'edit_others_posts' ) ) {
            return __( 'No active ads or campaigns', 'textdomain' );
        } else {
            return;
        }
    }

    // return ad html
    return '<a href="' . $ad['target'] . '" target="_blank" class="' . $ad['click_counter_class'] . '"><img src="' . $ad['src'] . '" class="ad ad-place-' . $ad['place'] . '"/></a>';
}
```

## Hooks
Plugin contains a set of hooks for you to use and modify behavior of plugin.

### Set default ad and link
If there is no active ads for the place, you can set default image and link for the ad place in question with two different hooks.

Use filter `drsa_default_ad/{place-id}` to set default ad image src.
Use filter `drsa_default_ad_target/{place-id}` to set default ad address.

### Disable UTM tags in ad target url
By default [UTM](https://support.google.com/analytics/answer/1033863#parameters) tags are inserted automatically to the ad target address, use filter `drsa_use_utm` to disable it.

To disable globally, use `add_filter( 'drsa_use_utm', '__return_false' );`
To disable by ad place, use `add_filter( 'drsa_use_utm\{place-id}', '__return_false' );`
To disable by single ad, use `add_filter( 'drsa_use_utm\ad\{ad-id}', '__return_false' );`

### Disable or change ad view throttle time
If visitor reloads the page or visits in the same page again within 30 seconds after first visit, the new visit is not counted to ad views. If same ad is shown in multiple pages, each page has it's own throttle prevention. For exmaple if ad is in sidebar, reload within 30 seconds in frontpage does not count new view but visit in another page will.

This is simple prevention to ensure that view count is somewhat accurate.

Change the throttle time with filter `drsa_counter_cookie_timeout`, return thtrottle time in milliseconds. To disable the feature, return zero.

## Changelog

Changelog can be found from [releases page](https://github.com/digitoimistodude/dude-really-simple-ads/releases).

## Contributing

If you have ideas about the plugin or spot an issue, please let us know. Before contributing ideas or reporting an issue about "missing" features or things regarding to the nature of that matter, please read [Please note](#please-note-before-using) section. Thank you very much.

## TODO

[ ] better inline commenting
[ ] fix old files for phpcs
