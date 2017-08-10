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
	$content = [];
	$current = 'default';
	$complement = '';
	$level = 0;
	while ( ! feof( $source ) ) {
		$line = trim( fgets( $source ) );
		if ( '' === $line ) {
			continue;
		}
		if ( strpos( $line, '#' ) === 0 ) {
			$tmp = strpos( $line, ' ' );
			if ( $level > 1 && $tmp > $level ) {
				$complement = $current . '{}';
			} elseif ( $tmp < $level ) {
				$complement = '';
			}
			$current = $complement . trim( substr( $line, $tmp ) );
			$content[ $current ] = '';
			$level = $tmp;
		} else {
			$content[ $current ] .= $line . PHP_EOL;
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

%upgrade%
== Changelog ==

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
$notices = implode( PHP_EOL, $notices );

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
		$changelog .= "* {$tmp[1]}\n" . str_replace( '- ', '  * ', $notice ) . PHP_EOL;
	}
}

// -----------------------------------------------------------------------------
// Export template
echo str_replace([
    '%tags%',
    '%changelog%',
    '%upgrade%',
], [
    $tags,
    $changelog,
    $notices
], $template);
