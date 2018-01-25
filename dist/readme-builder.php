<?php
/**
 * This file is a readme.txt builder used to publish updates on WordPress repository.
 * It avoid duplicating README.md files and ensure simplicity on Github.
 *
 * @package wp-mautic
 */

// -----------------------------------------------------------------------------
/**
 * Markdown parser helper
 *
 * @param  string $file
 * @return array
 */
function parse_markdown( string $file ) :array {
	$source = fopen( $file, 'r' );
	$content = ['default' => ''];
	$current = 'default';
	$complement = '';
	$level = 0;
	$sourceCode = false;
	while ( ! feof( $source ) ) {
		$line = rtrim(fgets( $source ));
		if ( '' === $line ) {
			$content[ $current ] .= PHP_EOL;
			continue;
		}
		if ( strpos( $line, '#' ) === 0 ) {
			$tmp = strpos( $line, ' ' );
			if ( $level > 1 ) {
				if( $tmp > $level ) {
					$complement = $current . '{}';
				} elseif($tmp < $level) {
					$exploded = explode('{}', $current);
					$complement = count($exploded) <= 2
						? ''
						: implode('{}', array_slice($exploded, 0, count($exploded)-2)).'{}';

				}
			} else {
				$complement = '';
			}
			$current = $complement . trim( substr( $line, $tmp ) );
			$content[ $current ] = '';
			$level = $tmp;
		} else {
			if (0 === strpos($line, '`')) {
				$sourceCode = !$sourceCode;
				continue;
			}

			$content[ $current ] .= (true === $sourceCode?'    ':'').$line . PHP_EOL;
		}
	}

	return $content;
}

// -----------------------------------------------------------------------------
// readme.txt template
$template = <<<TXT
=== WP Mautic ===
Author: mautic
Tags: marketing, automation
%tags%
Donate link: http://mautic.org/

== Description ==

%heading%

## Key features
%key-features%

## Configuration

%configuration%

## Usage

%documentation%

== Installation ==

%installation%

== Upgrade Notice ==

%upgrade%== Changelog ==

%changelog%
TXT;

// -----------------------------------------------------------------------------
// Extract package tag
$source = fopen( __DIR__ . '/../wpmautic.php', 'r' );
$tags = [];
while ( ! feof( $source ) ) {
	$line = fgets( $source );
	if ( " * @package wp-mautic\n" === $line ) {
		break;
	}
	if (
		0 === strpos( $line, ' * ' ) &&
		false === strpos( $line, 'Author:' ) &&
		false === strpos( $line, 'Plugin ' )
	) {
		$tags[] = str_replace( 'Version:', 'Stable tag:', trim( substr( $line, 3 ) ) );
	}
}
$tags = implode( PHP_EOL, $tags );

// -----------------------------------------------------------------------------
// Extract upgrade notices
$notices = [];
foreach ( parse_markdown( __DIR__ . '/../UPGRADE.md' ) as $key => $notice ) {
	if ( ! preg_match( '/^v.*/', $key ) ) {
		continue;
	}

	$notices[] = "= $key =\n$notice";
}
$notices = implode( $notices );

// -----------------------------------------------------------------------------
// Extract changelog
$changelog = '';
$title = '';
foreach ( parse_markdown( __DIR__ . '/../CHANGELOG.md' ) as $key => $notice ) {
	if ( ! preg_match( '/^\[v.*/', $key ) ) {
		continue;
	}
	if ( false === strpos( $key, '{}' ) ) {
		preg_match( '/\[(.*)\] - (.*)/', $key, $matches );
		$changelog .= "= {$matches[1]} =\n\nRelease date: {$matches[2]}\n\n";
	} else {
		$tmp = explode( '{}', $key );
		$changelog .= "* {$tmp[1]}\n" . str_replace( '- ', '  * ', $notice );
	}
}

// -----------------------------------------------------------------------------
// Extract README details
$sections = [];

$readme = parse_markdown( __DIR__ . '/../README.md' );

$header = explode('=======================', $readme['default'] ?? '');
$heading = end($header);
$keyFeature = $readme['Key features'] ?? '';
$configuration = $readme['Configuration'] ?? '';
$documentation = '';
$installation = '';
foreach ( $readme as $key => $bloc ) {
	$tmp = [];
	if (false !== strpos( $key, '{}' )) {
		$tmp = explode( '{}', $key );
	}
	if (0 === strpos($key, 'Usage')) {
		$documentation .= (count($tmp) > 1 ? str_repeat('#', count($tmp)+1).' '.end($tmp).PHP_EOL : '').$bloc;
	}
	if (0 === strpos($key, 'Installation')) {
		$installation .= (count($tmp) > 1 ? str_repeat('#', count($tmp)+1).' '.end($tmp).PHP_EOL : '').$bloc;
	}
}

// -----------------------------------------------------------------------------
// Export template
echo str_replace([
    '%tags%',
    '%heading%',
    '%key-features%',
    '%configuration%',
    '%documentation%',
    '%installation%',
    '%changelog%',
    '%upgrade%',
    '%upgrade%',
], [
    $tags,
    trim($heading),
    trim($keyFeature),
    trim($configuration),
    trim($documentation),
    trim($installation),
    $changelog,
    $notices
], $template);
