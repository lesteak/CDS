<?php $args = array(
  'post_type' => 'initiatives',
  'author' => wp_get_current_user()->ID,
  'posts_per_page' => -1,
  'post_status' => array('publish', 'pending')
);
$posts = get_posts($args); ?>

<section>
  <h2><?php _e('Initiatives created by me', 'tofino'); ?></h2>
  <?php if ($posts) :
    list_initiatives($posts);
  else : ?>
    <?php _e('You haven\'t added any initiatives yet', 'tofino'); ?>
  <?php endif; ?>

  <div class="button-block"><a href="/add-initiative" class="btn btn-primary btn-sm"><?php echo svg('plus'); ?><?php _e('Add New Initiative', 'tofino'); ?></a></div>
</section>
