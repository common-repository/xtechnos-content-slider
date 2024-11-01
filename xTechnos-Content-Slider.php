<?php
/*
Plugin Name: xTechnos Content Slider
Plugin URI: http://www.xtechnos.com
Description: xTechnos Content Slider is jFlow base slider. You can add both images and content in the slider, to make it easy a seprate post type is created. Use following code in any php file (of the theme) to install xTechnos Content Slider: $xtn_custom_content = new xtncustom_content(); $xtn_custom_content->my_content_slider();
Version: 1.0.0
Author: zagham.naseem
Author URI: http://xtechnos.com
License: GPL2
*/

/*  Copyright 2012 Syed Zagham Naseem (email : zagham.naseem@xtechnos.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class xtncustom_content {
var $meta_fields = array('x_title2','x_btn','x_link', 'xtn_btnText');
var $xtnop_db_version = "1.0.1";
function xtncustom_content() {

	register_post_type( 'xtn_slide_conten',
		$args= array(
			'labels' => array(
			'name' => __( 'Slider Content' ),
			'add_new_item' => __("Add New Content"),
			'edit_item'=> __("Edit Content"),
			'singular_name' => __( 'Content' )
			),
	
		'public' => true,
		'capability_type' 	=> 'post',
		'hierarchical' 		=> false,
		'rewrite' 			=> true,
		'has_archive' => true,
		'show_ui'=>true,
		'show_in_menu'=>true,
		'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt','cats'  ),
		'can_export'		=> true
		)
	);
	add_action("manage_posts_custom_column", array(&$this,"my_custom_columns"));
    add_filter("manage_edit-xtn_slide_conten_columns", array(&$this,"my_custom_slider_columns"));   
	add_action('init', array(&$this,'xtncustom_content'));
	add_action("admin_init", array(&$this,"admin_init"));	
	add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
	add_theme_support('post-thumbnails'); 
	add_image_size('featured_preview', 150, 150, true);
	add_shortcode('xtechnos-content-slider', array(&$this, 'my_content_slider'));
	add_action('admin_menu', array(&$this, 'register_my_custom_submenu_page'));
}

function my_custom_slider_columns($columns){
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => "Title",
		"x_description" => "Description",
		"x_link" => "Website Link",	
		"featured_image" => "Featured Image",
		'date' 	=> 	__('Published')
	);
	return $columns;
}

function my_custom_columns($column){
		global $post;
		switch ($column)
		{
			case "x_description":
				the_excerpt();
				break;
			case "x_link":
				$custom = get_post_custom();
				echo $custom["x_link"][0];
			break;
			case "featured_image":
			if ( has_post_thumbnail()){
				the_post_thumbnail( 'featured_preview' ); 
			}else{
		echo '<img src="'.get_bloginfo('url') .'/wp-content/plugins/xtechnos-content-slider/no_thumb.png" />';
        
			}
			break;
						
		}		
}
// When a post is inserted or updated
function wp_insert_post($post_id, $post = null)
{
	if ($post->post_type == "xtn_slide_conten")
	{
		// Loop through the POST data
		foreach ($this->meta_fields as $key)
		{
			$value = @$_POST[$key];
			if (empty($value))
			{
				delete_post_meta($post_id, $key);
				continue;
			}

			// If value is a string it should be unique
			if (!is_array($value))
			{
				// Update meta
				if (!update_post_meta($post_id, $key, $value))
				{
					// Or add the meta data
					add_post_meta($post_id, $key, $value);
				}
			}
			else
			{
				// If passed along is an array, we should remove all previous data
				delete_post_meta($post_id, $key);
				
				// Loop through the array adding new values to the post meta as different entries with the same name
				foreach ($value as $entry)
					add_post_meta($post_id, $key, $entry);
			}
		}
	}
}

function register_my_custom_submenu_page() {	
	add_submenu_page( 'edit.php?post_type=xtn_slide_conten', __('Settings'), __('Settings'), 'manage_options',"xtn_content_slider_setting", array(&$this,"xtn_content_slider_setting")); 
}

function xtn_content_slider_setting() {?>
<div align="center" style="color:#066"> <h2>Please Enter Values to Set Your Slider </h2></div>

<form method="post">

<label style="text-align:left">Slider's Wiidth: </label>
<span style="margin-left:50px;" ><input type="text" name="width" size="3" value="<?php echo get_post_meta(999998, 'xtn_width', true);?>"/>&nbsp;px<br/></span>
<label style="text-align:left">Slider's Height: </label>
<span style="margin-left:47px" ><input type="text" name="height" size="3" value="<?php echo get_post_meta(999999, 'xtn_height', true);?>" />&nbsp;px<br/> </span>
<label style="text-align:left;">Time Duration: </label>
<span style="margin-left:50px" ><input type="text" name="duration" size="3" value="<?php echo get_post_meta(999997, 'xtn_duration', true);?>" />&nbsp;Miliseconds<br/><br/></span>
<span style="margin-left:50px" ><input type="submit" name="submit" value="Save"  /></span>

</form>


	
<?php
if(isset($_POST['width']) && $_POST['width'] !=""){
	add_post_meta('999998', 'xtn_width', $_POST['width'] , true) or update_post_meta ('999998', 'xtn_width', $_POST['width']);
	add_post_meta('999999', 'xtn_height', $_POST['height'] , true) or update_post_meta ('999999', 'xtn_height', $_POST['height']);
	add_post_meta('999997', 'xtn_duration', $_POST['duration'] , true) or update_post_meta ('999997', 'xtn_duration', $_POST['duration']);
	}
 }

function my_content_slider(){?>
 <?php
	 $path= get_bloginfo('url');
 ?> 
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<link rel="stylesheet" href="<?php echo $path ;?>/wp-content/plugins/xtechnos-content-slider/css/slidestyle.css" type="text/css" />
    <?php 	// Width & Height from DB	
				$width = get_post_meta(999998, 'xtn_width', true);
				$height = get_post_meta(999999, 'xtn_height', true);
				$duration = get_post_meta(999997, 'xtn_duration', true);?>	
	<script type="text/javascript">

jQuery(document).ready(function($) {

(function($) {

	$.fn.jFlow = function(options) {
		var opts = $.extend({}, $.fn.jFlow.defaults, options);
		var randNum = Math.floor(Math.random()*11);
		var jFC = opts.controller;
		var jFS =  opts.slideWrapper;
		var jSel = opts.selectedWrapper;

		var cur = 0;
		var timer;
		var maxi = $(jFC).length;
		// sliding function
		var slide = function (dur, i) {
			$(opts.slides).children().css({
				overflow:"hidden"
			});
			$(opts.slides + " iframe").hide().addClass("temp_hide");
			$(opts.slides).animate({
				marginLeft: "-" + (i * $(opts.slides).find(":first-child").width() + "px")
				},
				opts.duration*(dur),
				opts.easing,
				function(){
					$(opts.slides).children().css({
						overflow:"hidden"
					});
					$(".temp_hide").show();
				}
			);
			
		}
		$(this).find(jFC).each(function(i){
			$(this).click(function(){
				dotimer();
				if ($(opts.slides).is(":not(:animated)")) {
					$(jFC).removeClass(jSel);
					$(this).addClass(jSel);
					var dur = Math.abs(cur-i);
					slide(dur,i);
					cur = i;
				}
			});
		});	
		
		$(opts.slides).before('<div id="'+jFS.substring(1, jFS.length)+'"></div>').appendTo(jFS);
		
		$(opts.slides).find("div").each(function(){
			$(this).before('<div class="jFlowSlideContainer"></div>').appendTo($(this).prev());
		});
		
		//initialize the controller
		$(jFC).eq(cur).addClass(jSel);
		
		var resize = function (x){
			$(jFS).css({
				position:"relative",
				width: opts.width,
				height: opts.height,
				overflow: "hidden"
			});
			//opts.slides or #mySlides container
			$(opts.slides).css({
				position:"relative",
				width: $(jFS).width()*$(jFC).length+"px",
				height: $(jFS).height()+"px",
				overflow: "hidden"
			});
			// jFlowSlideContainer
			$(opts.slides).children().css({
				position:"relative",
				width: $(jFS).width()+"px",
				height: $(jFS).height()+"px",
				"float":"left",
				overflow:"hidden"
			});
			
			$(opts.slides).css({
				marginLeft: "-" + (cur * $(opts.slides).find(":eq(0)").width() + "px")
			});
		}
		
		// sets initial size
		resize();

		// resets size
		$(window).resize(function(){
			resize();						  
		});
		
		$(opts.prev).click(function(){
			dotimer();
			doprev();
			
		});
		
		$(opts.next).click(function(){
			dotimer();
			donext();
			
		});
		
		var doprev = function (x){
			if ($(opts.slides).is(":not(:animated)")) {
				var dur = 1;
				if (cur > 0)
					cur--;
				else {
					cur = maxi -1;
					dur = cur;
				}
				$(jFC).removeClass(jSel);
				slide(dur,cur);
				$(jFC).eq(cur).addClass(jSel);
			}
		}
		
		var donext = function (x){
			if ($(opts.slides).is(":not(:animated)")) {
				var dur = 1;
				if (cur < maxi - 1)
					cur++;
				else {
					cur = 0;
					dur = maxi -1;
				}
				$(jFC).removeClass(jSel);
				//$(jFS).fadeOut("fast");
				slide(dur, cur);
				//$(jFS).fadeIn("fast");
				$(jFC).eq(cur).addClass(jSel);
			}
		}
		
		var dotimer = function (x){
			if((opts.auto) == true) {
				if(timer != null) 
					clearInterval(timer);
			    
        		timer = setInterval(function() {
	                	$(opts.next).click();
						}, <?php echo $duration; ?>);
			}
		}

		dotimer();
	};
	
	$.fn.jFlow.defaults = {
		controller: ".jFlowControl", // must be class, use . sign
		slideWrapper : "#jFlowSlide", // must be id, use # sign
		selectedWrapper: "jFlowSelected",  // just pure text, no sign
		auto: false,
		easing: "swing",
		duration: 400,
		width: "100%",
		prev: ".jFlowPrev", // must be class, use . sign
		next: ".jFlowNext" // must be class, use . sign
	};
	
})(jQuery);

$(document).ready(function(){
	$("#myController").jFlow({
		slides: "#slides",
		controller: ".jFlowControl", // must be class, use . sign
		slideWrapper : "#jFlowSlide", // must be id, use # sign
		selectedWrapper: "jFlowSelected",  // just pure text, no sign		
		width: "<?php echo $width.'px';?>",
		height: "<?php echo $height.'px';?>",
		duration: 600,
		prev: ".jFlowPrev", // must be class, use . sign
		next: ".jFlowNext", // must be class, use . sign
		auto: true // auto change slide, default true
	});
});
});
</script>
<style>
.description { width:<?php echo ($width-320) . 'px';?>}
</style>
	 <div id="nav_left" class="jFlowPrev"></div>
     <div id="nav_right" class="jFlowNext"></div>
     <div class="jflow-content-slider"><!--Slider Starts-->
      <div id="slides">
        <?php  
	  		global $number;
       		$number = wp_count_posts('xtn_slide_conten');
			$total= $number->publish;
			$args = array(	'post_type'  => 'xtn_slide_conten'	,'numberposts' => 5);
			$myposts = get_posts($args);		
	 		foreach( $myposts as $post ) :	setup_postdata($post);{
				$xPostID =  $post->ID;
			$post_title	 =  get_the_title($xPostID);
			$content	 = 	get_the_content($xPostID);
			$custom		 =  get_post_custom($xPostID); ?>
<div class="slide-wrapper">
  <div class="slide-details">
    <span class="title">
     	<h2> <?php echo $post_title ; ?> </h2>
     </span>
     <div class="title2">             
    	<?php echo $custom["x_title2"][0]; ?> 
     </div>
    <div class="description">
        <?php echo $content ;  ?>
    </div>         
  	<div class="button_container"> 
 		<a href="<?php echo $custom["x_link"][0]; ?>"> <img src="<?php echo $custom["x_btn"][0]; ?>" /> </a>
        <span class="btm_text"><?php echo $custom["xtn_btnText"][0]; ?> </span>
 	 </div>
	 </div>
     
	<div class="slide-thumbnail-container">
     <div class="slide-thumbnail">
    	<?php  echo get_the_post_thumbnail($xPostID, 'medium');?>
      </div>
    </div>
 	<div class="clear"></div>
</div>
	<?php       
        }
    endforeach; ?>
  </div>
<div id="myController"> <span class="jFlowPrev">Prev</span>
	<?php $i=1;		
    for ($i=1; $i<=$total; $i++){	?>
    <span class="jFlowControl"><?php echo $i; ?> </span>
    <?php }?>
    <span class="jFlowNext">Next</span> </div>
    <div class="clear"></div>
</div>     
    
				
<?php }


function admin_init() {
	// Custom meta boxes for the edit podcast screen
	add_meta_box("testimonial-meta", "Custom Website Link",array(&$this, "meta_options"), "xtn_slide_conten");
}

	// Admin post meta contents
function meta_options(){
		global $post;
		$custom = get_post_custom($post->ID);
		$xtn_title = $custom["x_title2"][0];
		$xtn_button = $custom["x_btn"][0];
		$xtn_link = $custom["x_link"][0];
		$xtn_text = $custom["xtn_btnText"][0]; ?>

<label>Title:</label>&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;<input name="x_title2" value="<?php echo $xtn_title; ?>" /> <br /><br />

<label>Button Link:</label>&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;<input name="x_btn" value="<?php echo $xtn_button; ?>" /> <br/> <br />

<label>Website Link:</label>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; <input name="x_link" value="<?php echo $xtn_link; ?>" /> <br/> <br />

<label>Button Text:</label>&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;<input name="xtn_btnText" value="<?php echo $xtn_text; ?>" /> <br /><br />

<?php    
   
  }
     
}
// Initiate the plugin
add_action("init", "xtn_custoInit");
function xtn_custoInit() { global $xtn_custom_content; $xtn_custom_content = new xtncustom_content(); }
?>