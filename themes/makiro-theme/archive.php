<?php get_header(); ?>

<main class="site-main">
  <div class="container">
    <div class="page-content">
      <h1 class="entry-title"><?php the_archive_title(); ?></h1>
      <?php if ( the_archive_description() ) : ?>
        <div class="archive-description"><?php the_archive_description(); ?></div>
      <?php endif; ?>

      <div class="posts-grid">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article class="post-card">
          <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>" class="post-card-image">
              <?php the_post_thumbnail('medium_large'); ?>
            </a>
          <?php endif; ?>
          <div class="post-card-content">
            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
          </div>
        </article>
        <?php endwhile; else : ?>
        <p>Inga inlägg hittades.</p>
        <?php endif; ?>
      </div>

      <?php the_posts_pagination(['mid_size' => 2]); ?>
    </div>
  </div>
</main>

<?php get_footer(); ?>
