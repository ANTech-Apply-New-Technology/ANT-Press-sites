<?php get_header(); ?>

<main class="site-main">
  <div class="container">
    <div class="page-content">
      <?php
      while ( have_posts() ) :
          the_post();
      ?>
      <article class="entry">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </article>
      <?php endwhile; ?>
    </div>
  </div>
</main>

<?php get_footer(); ?>
