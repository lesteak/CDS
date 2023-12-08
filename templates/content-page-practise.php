<main>
  <div class="container">
    <?php while (have_posts()) : the_post(); ?>
      <h1><?php echo \Tofino\Helpers\title(); ?></h1>
      <?php $events = xinc_events_get_events('https://pretix.eu/api/v1/organizers/transition-network/events'); ?>
      <?php get_template_part('templates/partials/events/table-events', null, array('events' => $events)); ?>
    <?php endwhile; ?>
  </div>
</main>