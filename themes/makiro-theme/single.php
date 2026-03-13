<?php get_header(); ?>

<main class="site-main">
  <div class="container">
    <div class="page-content">
      <?php
      while ( have_posts() ) :
          the_post();
      ?>
      <article class="entry">
        <div class="entry-meta">
          <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
          <?php if ( has_category() ) : ?>
            <span class="entry-categories"><?php the_category(', '); ?></span>
          <?php endif; ?>
        </div>
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php if ( has_post_thumbnail() ) : ?>
          <div class="entry-thumbnail">
            <?php the_post_thumbnail('large'); ?>
          </div>
        <?php endif; ?>
        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </article>
      <?php endwhile; ?>
    </div>
  </div>
</main>

<?php get_footer(); ?>
