<?php
/*
Plugin Name: wrap-dhamma-org
Description: retrieves, re-formats, and emits HTML for selected pages from www.dhamma.org
Version: 3.01
Authors: Joshua Hartwell <JHartwell@gmail.com> & Jeremy Dunn <jeremy.j.dunn@gmail.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, version 3.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>
*/

function fetch_url( $url ) {
	$r = wp_remote_get( $url );
	if ( is_wp_error( $r ) ) {
		// TODO(vlotoshnikov@gmail.com): Retry once and then
		// return '<script>window.location.replace(substr($url, 0, -4))</script>';
		// or even
		// echo '<script>window.location.replace(substr($url, 0, -4))</script>'; return '';
		// if second attempt also fails?
	}
	return wp_remote_retrieve_body( $r );
}

function wrap_dhamma( $page, $lang = null ) {
	$lang = isset($lang) ? $lang : substr(get_bloginfo('language'), 0, 2);
	// validate page
	switch ( $page ) {
		case 'vipassana' :
		case 'code' :
		case 'goenka' :
		case 'art' :
		case 'qanda' :
		case 'dscode' :
		case 'osguide' :
		case 'privacy' :
			$url = 'https://www.dhamma.org/' . $lang . '/' . $page . "?raw";
			$text_to_output = pull_page( $url, $lang );
			break;
		default:
			die ( "invalid page '".$page."'" );
	}

	// emit the required comment
	echo '<!-- ' . $url . ' has been dynamically reformatted on ' . date("D M  j G:i s Y T") . '. -->';

	// emit the reformatted page
	echo $text_to_output;

	echo '<!-- end dynamically generated content.-->';
	// we're done
}

function prepare_html( $html, $lang ) {
	$raw = fixURLs ( $html, $lang );
	$raw = stripH1 ( $raw );
	$raw = stripHR ( $raw );
	$raw = changeTag ( $raw, "h3", "h2" );
	$raw = changeTag ( $raw, "h4", "h3" );
	$raw = fixGoenkaImages ( $raw );
	return $raw;
}

function pull_page ( $url, $lang ) {
	$raw = fetch_url ( $url );
	return prepare_html($raw, $lang);
}

const LOCAL_URLS = array(
	'art' => '/vipassana/art-of-living/',
	'goenka' => '/vipassana/teacher-goenka/',
	'vipassana' => '/vipassana/about/',
	'/' => '',
);

function fixURLs ( $raw, $lang ) {
	foreach ( LOCAL_URLS as $from => $to ) {
		$raw = str_replace('<a href="' . $from . '">', '<a href="' . get_option('home') . $to . '">', $raw);
		$raw = str_replace("<a href='" . $from . "'>", '<a href="' . get_option('home') . $to . '">', $raw);
	}

	$raw = preg_replace("#<a href=[\"']/?code/?[\"']>#", '<a href="' . get_option('home') . '/courses/code-of-discipline/">', $raw);
	$raw = str_replace("<a href='/bycountry/'>", '<a target="_blank" href="' . get_theme_mod( 'dhamma_schedule_link' ) . '">', $raw);
	$raw = str_replace("<a href='/docs/core/code-" . $lang . ".pdf'>here</a>",
		"<a href='https://www.dhamma.org/" . $lang . "/docs/core/code-" . $lang . ".pdf'>here</a>", $raw);
	$raw = str_replace('"/en/docs/forms/Dhamma.org_Privacy_Policy.pdf"',
		'"https://www.dhamma.org/en/docs/forms/Dhamma.org_Privacy_Policy.pdf"', $raw);
	return $raw;
}

function stripH1( $raw ) {
	return preg_replace('@<h1[^>]*?>.*?<\/h1>@si', '', $raw); //This isn't a great solution, not very dynamic, but it gets the job done.
}

function stripHR ( $raw ) {
	return preg_replace("@<hr.*?>@si", '', $raw);
}

function changeTag ( $source, $oldTag, $newTag ) {
	$source = preg_replace( "@<{$oldTag}>@si", "<{$newTag}>", $source );
	$source = preg_replace( "@</{$oldTag}>@si", "</{$newTag}>", $source );
	return $source;
}

function fixGoenkaImages ( $raw ) {
	//Make the Goenkaji images work - JDH 10/12/2014
	$raw = preg_replace( '#/images/sng/#si', 'https://www.dhamma.org/images/sng/', $raw );

	//Make the goenka images inline - JDH 10/12/2014
	$raw = str_replace('class="www-float-right-bottom"', "align='right'", $raw);
	$raw = str_replace('<img alt="S. N. Goenka at U.N."', '<img alt="S. N. Goenka at U.N." style="display: block; margin-left: auto; margin-right: auto;"', $raw);
	$raw = str_replace('Photo courtesy Beliefnet, Inc.', '<p style="text-align:center">Photo courtesy Beliefnet, Inc.</p>', $raw);

	return $raw;
}

?>
