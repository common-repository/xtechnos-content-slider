<!-- Thumbnail from Custom Field, Post first image or default thumbnail -->
<div class="goleft picpad">
<?php
$PostContent = $post->post_content;
$ImgSearch = '|<img.*?src=[\'"](.*?)[\'"].*?>|i';
preg_match_all( $ImgSearch, $PostContent, $PostImg );
$ImgNumber = count($PostImg[0]);

    if ( get_post_meta($post->ID, "Thumbnail", true) )
  { ?>
        <img src="<?php echo get_post_meta($post->ID, "Thumbnail", true); ?>" alt="<?php the_title(); ?>" />
<?php }
    elseif ( get_post_meta($post->ID, "thumbnail", true) )
  { ?>
        <img src="<?php echo get_post_meta($post->ID, "thumbnail", true); ?>" alt="<?php the_title(); ?>" />
<?php }
    elseif ( $ImgNumber > 0 )
  {
    for ( $i=0; $i < $ImgNumber ; $i++ )
     {
        echo $PostImg[0][$i];
     };
}
    else
    { ?>
<?php } ?></div>