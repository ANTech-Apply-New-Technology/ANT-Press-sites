<?php get_header(); ?>

<main class="site-main">
  <div class="container">
    <div class="page-content error-404" style="text-align: center; padding: var(--space-5xl) 0;">
      <h1 class="entry-title" style="font-size: clamp(4rem, 10vw, 8rem); color: var(--color-accent);">404</h1>
      <p style="font-size: 1.25rem; color: var(--color-text-secondary); margin-bottom: var(--space-2xl);">Sidan kunde inte hittas.</p>
      <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">Tillbaka till startsidan</a>
    </div>
  </div>
</main>

<?php get_footer(); ?>
