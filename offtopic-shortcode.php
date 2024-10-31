<?php
/*
Plugin Name: Offtopic Shortcode
Plugin URI: http://w3prodigy.com/wordpress-plugins/offtopic-shortcode/
Description: Allows authors to use the [offtopic] shortcode. [offtopic tag="div" autop="false" prepend="Offtopic:"]Did you know that this is how you use the offtopic shortcode?[/offtopic] Designers can style shortcode output using the <tt>offtopic</tt> and <tt>offtopic-prepend</tt> CSS classes.
Author: Jay Fortner
Author URI: http://w3prodigy.com
Version: 0.1
Tags: shortcode, offtopic
License: GPL2
*/

new Offtopic_Shortcode;

class Offtopic_Shortcode {
	
	function Offtopic_Shortcode()
	{
		new Offtopic_Shortcode_Options;
		add_shortcode( 'offtopic', array( &$this, 'offtopic' ) );
	} // function
	
	function offtopic( $atts = null, $content = null, $code = null )
	{
		$shortcode_atts = array(
			'tag' => get_site_option( 'offtopic_shortcode_tag' ) ? get_site_option( 'offtopic_shortcode_tag' ) : 'div',
			'autop' => get_site_option( 'offtopic_shortcode_autop' ) ? get_site_option( 'offtopic_shortcode_autop' ) : 'false',
			'prepend' => get_site_option( 'offtopic_shortcode_prepend' ) ? get_site_option( 'offtopic_shortcode_prepend' ) : ''
			);
			
		extract( shortcode_atts( $shortcode_atts, $atts ) );
		
		if( !empty( $prepend ) )
			$content = "<span class='offtopic-prepend'>$prepend</span>&nbsp;" . $content;
		
		$allowed_tags = array( "div" );
		
		if( "true" == $autop ):
			$tag = "div";
			$content = wpautop( $pee = $content, $br = 1 );
		else:
			$allowed_tags[] = "p";
		endif;
		
		if( !in_array( $tag, $allowed_tags ) )
			$tag = $shortcode_atts['tag'];
		
		return "<$tag class='offtopic'>$content</$tag>";
	} // function
	
} // class

class Offtopic_Shortcode_Options {
	
	function Offtopic_Shortcode_Options()
	{
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
	} // function
	
	function admin_init()
	{
		add_settings_section(
			$id = 'offtopic_shortcode',
			$title = 'Offtopic Shortcode',
			$callback = array( &$this, 'offtopic_shortcode_section' ),
			$page = 'writing'
			);
		
		# 2. Default Autop
		add_settings_field( 
			$id = 'offtopic_shortcode_autop',
			$title = 'Default Autop',
			$callback = array( &$this, 'offtopic_shortcode_autop' ),
			$page = 'writing',
			$section = 'offtopic_shortcode'
			);
		register_setting( $option_group = 'writing', $option_name = 'offtopic_shortcode_autop' );
		
		# 1. Default Tag
		add_settings_field( 
			$id = 'offtopic_shortcode_tag',
			$title = 'Default Tag',
			$callback = array( &$this, 'offtopic_shortcode_tag' ),
			$page = 'writing',
			$section = 'offtopic_shortcode'
			);
		register_setting( $option_group = 'writing', $option_name = 'offtopic_shortcode_tag' );
		
		# 3. Default Prepend
		add_settings_field( 
			$id = 'offtopic_shortcode_prepend',
			$title = 'Default Prepend',
			$callback = array( &$this, 'offtopic_shortcode_prepend' ),
			$page = 'writing',
			$section = 'offtopic_shortcode'
			);
		register_setting( $option_group = 'writing', $option_name = 'offtopic_shortcode_prepend' );
	} // function
	
	function offtopic_shortcode_section()
	{
		echo "<p>The Offtopic Shortcode has multiple options you can use. Set the default options for all Offtopic Shortcodes here so you don't have to write it every time.</p>";
		echo "<p>If Autop is enabled the shortcode will switch to using <tt>div</tt> as your default tag.</p>\n";
	} // function
	
	function offtopic_shortcode_autop()
	{
		$value = get_site_option( 'offtopic_shortcode_autop' );
		
		$checked = "";
		if( !empty( $value ) )
			$checked = "checked='checked'";
		
		echo "<input type='checkbox' name='offtopic_shortcode_autop' value='true' $checked/> <label for='offtopic_shortcode_autop'>Enable Autop on Offtopic content</label>";
	} // function
	
	function offtopic_shortcode_tag()
	{
		$value = get_site_option( 'offtopic_shortcode_tag' );
		
		$options = array( "div" );
		
		$autop = get_site_option( 'offtopic_shortcode_autop' );
		if( "true" != $autop )
			$options[] = "p";
		
		echo "<select name='offtopic_shortcode_tag'>\n";
		foreach( $options as $key => $tag ):
			$selected = "";
			if( $tag == $value )
				$selected = "selected='selected'";
			echo "<option value='$tag' $selected>$tag</option>\n";
		endforeach;
		echo "</select>\n";
		
		if( "true" == $autop )
			echo "<br/><small><strong>Warning</strong> You currently have Autop enabled by default which removes some Default Tag options.</small>";
	} // function
	
	function offtopic_shortcode_prepend()
	{
		$value = get_site_option( 'offtopic_shortcode_prepend' );
		
		echo "Prepend Offtopic Content with <input name='offtopic_shortcode_prepend' type='text' class='regular-text code' value='$value'/>";
		echo "<br/><small><strong>Attention</strong> Offtopic Content will be prepended with the above text before the Autop is applied.</small>";
	} // function
	
} // class