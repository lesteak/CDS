<?php use \Tofino\Helpers as h;

$template = h\get_page_name();

get_header(); ?>
<?php get_hub_filter(); ?>

<ul id="dom-target" style="display:none;">
  <?php foreach ($posts as $post) :
    $map = get_field('map', get_the_ID(), false); ?>
    <li class="point" data-lat="<?php echo htmlspecialchars($map['center_lat']); ?>" data-lng="<?php echo htmlspecialchars($map['center_lng']); ?>" data-title="<?php echo get_the_title(); ?>" data-link="<?php the_permalink(); ?>"></li>
  <?php endforeach; ?>
</ul>
<div id="iframe_map"></div>
<main>
  <div class="container">
    <h1><?php echo $page_title ?></h1>
    <?php list_initiatives(); ?>
    <ul class="button-group">
      <li><a class="btn btn-primary" href="<?php echo get_permalink(13); ?>">Add new initiative</a></li>
    </ul>
  </div>
</main>

<?php get_footer(); ?>
