<?php
use \Tofino\ThemeOptions\Notifications as n;

if (get_theme_mod('footer_sticky') === 'enabled') : ?>
  </div>
<?php endif; ?>

<footer>
  <div class="container">
    &copy <?php echo date('Y'); ?> Transition Network
  </div>
</footer>

<?php wp_footer(); ?>

<?php n\notification('bottom'); ?>
</body>
</html>
