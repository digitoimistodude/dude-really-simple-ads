# dude-really-simple-ads
A simple way to manage, track and show ads.

Plugin counts ad views and clicks, supports timing, multiple places and campaigns.

## Registering Ad Places

Before you can start usign this plugin, you need to register places where ads can be inserted. registration is done via `drsa_ad_placement_sizes` hook. Hook should return multidimensional array, see the example below.

```php
add_filter( 'drsa_ad_placement_sizes', function() { return array(
	array(
		'name'		=> 'Frontpage',
		'id'			=> 'frontpage',
		'width'		=> 600,
		'height'	=> 600,
	),
); } );
```

## Getting ads

Displaying ads needs some custom coding. You can get current active ad or campaing with function `get_the_active_ad( 'frontpage' );` which returns empty response of there is no active ad or array containing the ad information.

```php
$ad = array(
  'target'              => null, // ad target url
  'src'                 => null, // url to ad image
)
```

## Activating click counting

Click counting needs `drsa-PLACEMENT` class in clickable html element (usually a a-tag) in order to work.

## Setting ad place defaults

Normally default ad is not returned or shown if the place does not have any active ad or campaing. If you wish to chnage this, use hooks.

```php
add_filter( 'drsa_default_ad/frontpage', function() { return 'IMAGE-SRC'; } );
add_filter( 'drsa_default_ad_target/frontpage', function() { return 'URL'; } );
```

## Disble automatic UTM tags in ad target url

By default UTM tags are automatically added to target url of ad. With UTM tags, advertisers can easily track how many have arrived to their site from the ad. Sometimes this may break the target system or there's no need for UTM tags, use hook to disable those.

```php
add_filter( 'drsa_use_utm', '__return_false' );
```

## TODO

- [ ] This README file
- [ ] Better inline commenting
