<?php
/**
 * Generate OPcache statistics in admin area
 *
 * @phpcs:disable Squiz.PHP.EmbeddedPhp.ContentAfterOpen
 * @phpcs:disable Squiz.PHP.EmbeddedPhp.ContentBeforeOpen
 *
 * @package flush-opcache
 */

use Amnuts\Opcache\Service;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( dirname( __FILE__ ) ) . '/vendor/autoload.php';
$options = array(
	'allow_filelist'   => false,
	'allow_invalidate' => false,
	'allow_reset'      => false,
	'allow_realtime'   => false,
	'size_precision'   => 2,
	'size_space'       => false,
);

$opcache_data = ( new Service( $options ) )->handle();

$opcache_stroke_dasharray       = pi() * 200;
$opcache_used_memory_percentage = $opcache_data->getData( 'overview', 'used_memory_percentage' );
$opcache_used_memory_stroke     = $opcache_stroke_dasharray * ( 1 - $opcache_used_memory_percentage / 100 );
$opcache_hit_rate               = round( $opcache_data->getData( 'overview', 'opcache_hit_rate' ) );
$opcache_hit_rate_stroke        = $opcache_stroke_dasharray * ( 1 - $opcache_hit_rate / 100 );
$opcache_num_cached_keys        = $opcache_data->getData( 'overview', 'num_cached_keys' );
$opcache_max_cached_keys        = $opcache_data->getData( 'overview', 'max_cached_keys' );
$opcache_used_keys_percentage   = round( ( $opcache_num_cached_keys / $opcache_max_cached_keys ) * 100 );
$opcache_used_keys_stroke       = $opcache_stroke_dasharray * ( 1 - $opcache_used_keys_percentage / 100 );
$opcache_data_readable          = $opcache_data->getData( 'overview', 'readable' );
$opcache_data_directives        = $opcache_data->getData( 'directives' );

?>

<div class="wrap">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">

			<h1><?php esc_attr_e( 'OPcache statistics', 'flush-opcache' ); ?></h1>

			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables flush-opcache-widget-container">

					<div class="postbox flush-opcache-postbox">
						<h2 class="flush-opcache-widget-title"><span><?php esc_attr_e( 'Memory', 'flush-opcache' ); ?></span></h2>
						<div class="inside">
							<figure class="flush-opcache-widget">
								<svg width="260" height="260" viewBox="0 0 260 260" style="transform: rotate(-90deg);">
									<circle cx="130" cy="130" r="100" fill="none" stroke="#A0A5AA" stroke-width="30"></circle>
									<circle cx="130" cy="130" r="100" fill="none" stroke="#00A0D2" stroke-width="30" stroke-dasharray="<?php echo esc_attr( $opcache_stroke_dasharray ); ?>" stroke-dashoffset="<?php echo esc_attr( $opcache_used_memory_stroke ); ?>"></circle>
								</svg>
							<figcaption class="flush-opcache-widget-value"><?php echo esc_attr( $opcache_used_memory_percentage ); ?>%</figcaption>
							</figure>
						</div>
					</div>

					<div class="postbox flush-opcache-postbox">
						<h2 class="flush-opcache-widget-title"><span><?php esc_attr_e( 'Hit rate', 'flush-opcache' ); ?></span></h2>
						<div class="inside">
							<figure class="flush-opcache-widget">
								<svg width="260" height="260" viewBox="0 0 260 260" style="transform: rotate(-90deg);">
									<circle cx="130" cy="130" r="100" fill="none" stroke="#A0A5AA" stroke-width="30"></circle>
									<circle cx="130" cy="130" r="100" fill="none" stroke="#00A0D2" stroke-width="30" stroke-dasharray="<?php echo esc_attr( $opcache_stroke_dasharray ); ?>" stroke-dashoffset="<?php echo esc_attr( $opcache_hit_rate_stroke ); ?>"></circle>
								</svg>
							<figcaption class="flush-opcache-widget-value"><?php echo esc_attr( $opcache_hit_rate ); ?>%</figcaption>
							</figure>
						</div>
					</div>

					<div class="postbox flush-opcache-postbox">
						<h2 class="flush-opcache-widget-title"><span><?php esc_attr_e( 'Keys', 'flush-opcache' ); ?></span></h2>
						<div class="inside">
							<figure class="flush-opcache-widget">
								<svg width="260" height="260" viewBox="0 0 260 260" style="transform: rotate(-90deg);">
									<circle cx="130" cy="130" r="100" fill="none" stroke="#A0A5AA" stroke-width="30"></circle>
									<circle cx="130" cy="130" r="100" fill="none" stroke="#00A0D2" stroke-width="30" stroke-dasharray="<?php echo esc_attr( $opcache_stroke_dasharray ); ?>" stroke-dashoffset="<?php echo esc_attr( $opcache_used_keys_stroke ); ?>"></circle>
								</svg>
							<figcaption class="flush-opcache-widget-value"><?php echo esc_attr( round( $opcache_used_keys_percentage ) ); ?>%</figcaption>
							</figure>
						</div>
					</div>

					<div class="postbox flush-opcache-postbox">
					<table class="widefat">
						<thead>
						<tr>
							<th class="flush-opcache-widget-title"><b><?php esc_attr_e( 'General info', 'flush-opcache' ); ?></b></th>
							<th class="flush-opcache-widget-title"></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><b><?php esc_attr_e( 'OPcache version', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'version', 'version' ) ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'PHP version', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'version', 'php' ) ); ?></td>
						</tr>
						<tr>
							<td><b><?php esc_attr_e( 'host', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'version', 'host' ) ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'server software', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'version', 'server' ) ); ?></td>
						</tr>
						<tr>
							<td><b><?php esc_attr_e( 'start time', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( date_i18n( 'Y/m/d g:i:s A', $opcache_data->getData( 'overview', 'start_time' ) ) ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'last reset', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( date_i18n( 'Y/m/d g:i:s A', $opcache_data->getData( 'overview', 'last_restart_time' ) ) ); ?></td>
						</tr>
						</tbody>
					</table>
					</div>

				</div>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<div class="meta-box-sortables flush-opcache-widget-container">

					<div class="postbox flush-opcache-postbox">
					<table class="widefat">
						<thead>
						<tr>
							<th class="flush-opcache-widget-title"><b><?php esc_attr_e( 'Memory usage', 'flush-opcache' ); ?></b></th>
							<th class="flush-opcache-widget-title"></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><b><?php esc_attr_e( 'total memory', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data_readable['total_memory'] ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'used memory', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data_readable['used_memory'] ); ?></td>
						</tr>
						<tr>
							<td><b><?php esc_attr_e( 'free memory', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data_readable['free_memory'] ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'wasted memory', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data_readable['wasted_memory'] ); ?> (<?php echo esc_attr( $opcache_data->getData( 'overview', 'wasted_percentage' ) ); ?>%)</td>
						</tr>
						</tbody>
					</table>
					</div>

					<div class="postbox flush-opcache-postbox">
					<table class="widefat">
						<thead>
						<tr>
							<th class="flush-opcache-widget-title"><b><?php esc_attr_e( 'OPcache statistics', 'flush-opcache' ); ?></b></th>
							<th class="flush-opcache-widget-title"></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><b><?php esc_attr_e( 'number of cached files', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'overview', 'num_cached_scripts' ) ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'number of hits', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'overview', 'hits' ) ); ?></td>
						</tr>
						<tr>
							<td><b><?php esc_attr_e( 'number of misses', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'overview', 'misses' ) ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'blacklist misses', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'overview', 'blacklist_misses' ) ); ?></td>
						</tr>
						<tr>
							<td><b><?php esc_attr_e( 'number of cached keys', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'overview', 'num_cached_keys' ) ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'max cached keys', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data->getData( 'overview', 'max_cached_keys' ) ); ?></td>
						</tr>
						</tbody>
					</table>
					</div>

					<div class="postbox flush-opcache-postbox">
					<table class="widefat">
						<thead>
						<tr>
							<th class="flush-opcache-widget-title"><b><?php esc_attr_e( 'Interned strings usage', 'flush-opcache' ); ?></b></th>
							<th class="flush-opcache-widget-title"></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><b><?php esc_attr_e( 'buffer size', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data_readable['interned']['buffer_size'] ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'used memory', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data_readable['interned']['strings_used_memory'] ); ?></td>
						</tr>
						<tr>
							<td><b><?php esc_attr_e( 'free memory', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data_readable['interned']['strings_free_memory'] ); ?></td>
						</tr>
						<tr class="alternate">
							<td><b><?php esc_attr_e( 'number of strings', 'flush-opcache' ); ?>:</b></td>
							<td><?php echo esc_attr( $opcache_data_readable['interned']['number_of_strings'] ); ?></td>
						</tbody>
					</table>
					</div>

					<div class="postbox flush-opcache-postbox">
					<table class="widefat">
						<thead>
						<tr>
							<th class="flush-opcache-widget-title"><b><?php esc_attr_e( 'Available functions', 'flush-opcache' ); ?></b></th>
							<th class="flush-opcache-widget-title"></th>
						</tr>
						</thead>
						<tbody>
						<?php
						$count = 0;
						foreach ( $opcache_data->getData( 'functions' ) as $function ) {
							$count++;
							?>
							<tr<?php if ( 0 === $count % 2 ) { ?> class="alternate" <?php } // phpcs:ignore ?>>
							<td><a href="https://www.php.net/<?php echo esc_attr( $function ); // phpcs:ignore ?>" target="_blank"><?php echo $function; ?></a></td>
							<td></td>
						</tr>
						<?php } ?>
						</tbody>
					</table>
					</div>

				</div>
			</div>

			<div id="postbox-container-3" class="postbox-container">
				<div class="meta-box-sortables flush-opcache-widget-container">

					<table class="widefat">
						<thead>
						<tr>
							<th class="flush-opcache-widget-title"><b><?php esc_attr_e( 'Directives', 'flush-opcache' ); ?></b></th>
							<th class="flush-opcache-widget-title"></th>
						</tr>
						</thead>
						<tbody>
						<?php
						$count = 0;
						foreach ( $opcache_data_directives as $directive ) {
							$count++;
							if ( is_array( $directive['v'] ) ) {
								$directive_value = '<ul>';
								foreach ( $directive['v'] as $item ) {
									$directive_value .= '<li>' . $item . '</li>';
								}
								$directive_value .= '</ul>';
							} elseif ( '' === $directive['v'] ) {
								$directive_value = __( 'not defined', 'flush-opcache' );
							} elseif ( is_bool( $directive['v'] ) ) {
								$directive_value = var_export( $directive['v'], true ); // phpcs:ignore
							} else {
								$directive_value = $directive['v'];
							}
							?>
							<tr<?php if ( 0 === $count % 2 ) { ?> class="alternate" <?php } // phpcs:ignore ?>>
							<td><a href="https://www.php.net/manual/en/opcache.configuration.php#ini.<?php echo esc_attr( str_replace( '_', '-', $directive['k'] ) ); // phpcs:ignore ?>" target="_blank"><?php echo $directive['k']; ?></a></td>
							<td><?php echo $directive_value; // phpcs:ignore ?></td>
						</tr>
						<?php } ?>
						</tbody>
					</table>

				</div>
			</div>

		</div>
		<br class="clear">
	</div>
</div>
