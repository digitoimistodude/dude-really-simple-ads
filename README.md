# Really simple ads
![version_1.0.0-beta](https://img.shields.io/badge/Version-1.0.0--beta-orange.svg) ![Tested_up_to WordPress_4.6](https://img.shields.io/badge/Tested_up_to-WordPress_5.6-blue.svg?style=flat-square) ![Compatible_with PHP_7.0](https://img.shields.io/badge/Compatible_with-PHP_7.0-green.svg?style=flat-square)

[Digitoimisto Dude Oy](https://www.dude.fi) is a Finnish boutique digital agency in the center of Jyväskylä.

## Table of contents

1. [Please note before using](#please-note-before-using)
3. [Features](#features)
4. [Usage](#usage)
6. [Changelog](#hangelog)
7. [Contributing](#contributing)
8. [TODO](#TODO)

## Please note before using
By using this code bases, you agree that the anything can change to a different direction without a warning.

## Features
Basic feature list includes

- Multiple ad places
- Sheculed ads with start and end time
- Campaigns containing multiple ads
- Simple view and click counter per ad
- Private notes for ads and campaigns

## Usage

### Register ad places
Ad places are registered with `drsa_ad_placement_sizes` hook. You should pass nested array containing one array element per each ad place. See exmaple below.

```php
add_filter( 'drsa_ad_placement_sizes', 'myprefix_register_ad_spots' );
function myprefix_register_ad_spots() {
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

If there is active campaign for that ad place, a random image from that campaign will be returned. Note that campaign will bypass single ad schedule over the campaings schedule. When there is no active campaign, active single ad for place is returned. Default ad and link will be returned if no active campaign or ad are found, if there is no default then return is false.

Function returns array containing the ad place, image src and target. Simple usage example is below, but you should modify it according to your needs. Always check the function existance.

```php
$ad = false;
if ( function_exists( 'get_the_active_ad' ) ) {
    $ad = get_the_active_ad( 'frontpage-default' );
}

if ( $ad ) {
    echo '<a href="' . $ad['target'] . '"><img src="' . $ad['src'] . '" class="ad ad-place-' . $ad['place'] . '"/></a>';
}
```

### Campaigns

### Set default ad and link
If theres no active ads for the place, you can set default image and link for the ad place in question with two different hooks.

Use filter `drsa_default_ad/{place-id}` to set default ad image src.
Use filter `drsa_default_ad_target/{place-id}` to set default ad address.

### Disable UTM tags in ad target url
By default UTM tags are inserted automatically to the ad target address, use filter `drsa_use_utm` to disable it.

`add_filter( 'drsa_use_utm', '__return_false' );`

## Changelog

Changelog can be found from [releases page](https://github.com/digitoimistodude/air-helper/releases).

## Contributing

If you have ideas about the plugin or spot an issue, please let us know. Before contributing ideas or reporting an issue about "missing" features or things regarding to the nature of that matter, please read [Please note](#please-note-before-using) section. Thank you very much.

## TODO

[ ] better inline commenting
[ ] fix old files for phpcs
[ ] this documentation
