<?php
$args = array(
  'redirect' => get_permalink(24)
); ?>

<main>
  <div class="container">
    <h1>Login</h1>
    <?php wp_login_form($args); ?>
    <p>Or <a href="register">Register</a> if you don't have an account</p>
  </div>
</main>
