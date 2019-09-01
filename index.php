<?php
/*
* Plugin Name: Authors Posts List ShortCode
* Description: Create shortcode to present authors posts list.
* Version: 1.0.1
* Author: Zeyad Habbab
* Author URI: https://www.conversion.ps/
*/

/*	Authors Posts List ShortCode Plugin Doc
*	Example
*	[author-post author_username="admin" exclude_posts="19,12,45" exclude_cats_slug="tech, animals" hide_title="true" limit="1"]
*	
*	Attributes:
*	author_username for user name like admin
*	exclude_posts to exclude some post from result
*	exclude_cats_slug to exclude some cats from result using cats slug
*	hide_title to hide author name
*	limit to limit the post result , use -1 to show all
*
*	CSS Class to cutmize view
*	.author_name_title{
*		
*	}
*	
*	.author_post_list{
*		
*	}
*
*	.author_post_list li{
*		
*	}
*/

function wp_authors_posts_list_shortcode($atts, $content=null){
	$data = "";
	/*Initilized Shorcode Attributes*/
	$a = shortcode_atts( array(
		'author_username' => '',
		'limit' => '-1',
		'exclude_posts' => '',
		'hide_title' => 'false',
		'exclude_cats_slug' => ''
	), $atts );
	
	/*Co-Authors Plus Plugin Required*/
	if ( function_exists( 'coauthors_posts_links' ) ) {
		if($a['author_username'] !=""){
			$data = $data . "<div>";
			$login = $a['author_username'];
			$user = get_user_by( 'login', $login );
			
			if($a['hide_title'] != "true"){
				$data = $data . '<h2 class="author_name_title">' . $user->display_name . '</h2>';
			}
			
			/*Get All Post For the user as teams (this is how Co-Authors Plus Plugin worked)*/
			$args = array(
				'post_type' => 'post',
				'order' => 'ASC',
				'orderby' => 'title',
				'posts_per_page' => $a['limit'],
				'tax_query' => array(
					array(
						'taxonomy' => 'author',
						'field'    => 'name',
						'terms'    => $a['author_username'],
					),
				),
			);
				
			if($a['exclude_cats_slug'] !="" ){
				$ex_cats = [];
				$exclude_cats = explode(",",$a['exclude_cats_slug']);
				foreach($exclude_cats as $cat){
					$id_obj = get_category_by_slug($cat); 
					$id = $id_obj->term_id;
					$ex_cats[] = $id;
				}
				$args['category__not_in'] = $ex_cats;
			}
			
			if($a['exclude_posts'] !="" ){
				$ex_posts = [];
				$exclude_posts = explode(",",$a['exclude_posts']);
				foreach($exclude_posts as $post){
					$ex_posts[] = $post;
				}
				$args['post__not_in'] = $ex_posts;
			}
				
			$the_query = new WP_Query( $args );
			
			if ( $the_query->have_posts() ) {
				$data = $data . '<ul class="author_post_list">';
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$data = $data . '<li><a href="'.get_permalink().'">' . get_the_title() . '</a></li>';
				}
				$data = $data . '</ul>';
			}
			$data = $data . "</div>";
		
		}
	}else{
		$data = $data . "This plugin work only with <a href='https://wordpress.org/plugins/co-authors-plus/#description'>Co-Authors Plus</a>.<br>";
	}

	return $data;
	
	
}
add_shortcode('author-post', 'wp_authors_posts_list_shortcode');



/*	Authors Posts List ShortCode Plugin Doc
*	Example
*	[authors-posts exclude_authors="admin" exclude_posts="19,12,45" exclude_cats_slug="tech, animals" hide_title="true" limit="1"]
*	
*	Attributes:
*	author_username for user name like admin
*	exclude_posts to exclude some post from result
*	exclude_cats_slug to exclude some cats from result using cats slug
*	hide_title to hide author name
*	limit to limit the post result , use -1 to show all
*
*	CSS Class to cutmize view
*	.author_name_title{
*		
*	}
*	
*	.author_post_list{
*		
*	}
*
*	.author_post_list li{
*		
*	}
*/

function wp_authors_posts_lists_shortcode($atts, $content=null){
	/*Initilized Shorcode Attributes*/
	$data = "";

	$a = shortcode_atts( array(
		'exclude_authors' => '',
		
		'limit' => '-1',
		'exclude_posts' => '',
		'hide_title' => 'false',
		'exclude_cats_slug' => ''
	), $atts );
	
	/*Co-Authors Plus Plugin Required*/
	if ( function_exists( 'coauthors_posts_links' ) ) {
			$ex_users = [];
			
			if($a['exclude_authors'] !=""){
				$user_list = explode(",",$a['exclude_authors']);
				foreach($user_list as $login){
					$user = get_user_by( 'login', $login );
					$ex_users[] = $user->ID;
				}	
			}
			
			
			
			$wp_user_query = new WP_User_Query( array( 'exclude' => $ex_users 'order' => 'ASC', 'orderby' => 'display_name') );
			$authors = $wp_user_query->get_results();


			 
			foreach ( $authors as $author ) {
				$user =  get_userdata( $author->ID );
				$data = $data . "<div>";
				
				
				/*Get All Post For the user as teams (this is how Co-Authors Plus Plugin worked)*/
				$args = array(
					'post_type' => 'post',
					'order' => 'ASC',
					'orderby' => 'title',
					'posts_per_page' => $a['limit'],
					'tax_query' => array(
						array(
							'taxonomy' => 'author',
							'field'    => 'name',
							'terms'    => $user->user_login,
						),
					),
				);
					
				if($a['exclude_cats_slug'] !="" ){
					$ex_cats = [];
					$exclude_cats = explode(",",$a['exclude_cats_slug']);
					foreach($exclude_cats as $cat){
						$id_obj = get_category_by_slug($cat);
						if($id_obj){
							$id = $id_obj->term_id;
							$ex_cats[] = $id;
						}
						
					}
					$args['category__not_in'] = $ex_cats;
				}
				
				if($a['exclude_posts'] !="" ){
					$ex_posts = [];
					$exclude_posts = explode(",",$a['exclude_posts']);
					foreach($exclude_posts as $post){
						$ex_posts[] = $post;
					}
					$args['post__not_in'] = $ex_posts;
				}
					
				$the_query = new WP_Query( $args );
				
				if ( $the_query->have_posts() ) {
					if($a['hide_title'] != "true"){
						$data = $data . '<h2 class="author_name_title">' . $user->display_name . '</h2>';
					}
					$data = $data . '<ul class="author_post_list">';
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$data = $data . '<li><a href="'.get_permalink().'">' . get_the_title() . '</a></li>';
					}
					$data = $data . '</ul>';
				}
				$data = $data . "</div>";
			}
		
		
	}else{
		$data = $data . "This plugin work only with <a href='https://wordpress.org/plugins/co-authors-plus/#description'>Co-Authors Plus</a>.<br>";
	}

	return  $data;
	
	
}
add_shortcode('authors-posts', 'wp_authors_posts_lists_shortcode');