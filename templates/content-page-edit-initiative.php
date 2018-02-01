<?php 
acf_form_head();
?>

<?php
$edit_post_id = get_query_var('edit_post');
$post_author = (get_post_field ('post_author', $edit_post_id));
$current_author = ((string)get_current_user_id()); ?>

<?php if($edit_post_id) : ?>
	<main>
		<div class="container">
			<h1><?php echo \Tofino\Helpers\title(); ?></h1>
			<?php if(($post_author == $current_author) || (current_user_can( 'manage_options' ))) : 
				acf_form(array(
					'post_id'		=> $edit_post_id,
					'post_title'	=> true,
					'post_content'	=> false,
					'return' => 'thank-you-for-your-submission',
					'submit_value' => 'Save changes',
					'new_post'		=> array(
						'post_type'		=> 'initiatives',
						'post_status'	=> 'publish'
					)
				));
			else :
				echo 'You don\'t have the permission to edit this post';
			endif; ?>
		</div>
	</main>
<?php endif; ?>
