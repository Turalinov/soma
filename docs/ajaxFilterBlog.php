<?php

add_action('wp_ajax_filter_blog', 'ajax_filter_blog');
add_action('wp_ajax_nopriv_filter_blog', 'ajax_filter_blog');

function ajax_filter_blog() {
  $paged = isset($_POST['paged']) ? (int)$_POST['paged'] : 1;
  $categories = isset($_POST['categories']) ? explode(',', sanitize_text_field($_POST['categories'])) : [];

  $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'date';



  $args = [
    'post_type'      => 'post',
    'posts_per_page' => wp_is_mobile() ? 3 : 6,
    'paged'          => $paged,
  ];

  if ($sort === 'reading_time') {
    // Сортировка по ACF-полю "reading_time" как числовому значению
    $args['meta_key'] = 'reading_time';
    $args['orderby'] = 'meta_value_num';
    $args['order'] = 'DESC';
  } else {
    // По умолчанию — сортировка по дате
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';
  }

  if (!empty($categories) && $categories[0] !== '') {
    $args['tax_query'] = [
      [
        'taxonomy' => 'category',
        'field'    => 'slug',
        'terms'    => $categories,
        'operator' => 'IN',
      ]
    ];
  }

  $query = new WP_Query($args);

  ob_start();

  if ($query->have_posts()) {
    echo '<div class="articles__list">';
    while ($query->have_posts()) {
      $query->the_post();
      ?>

             <!-- item  -->
            <div class="articles__item article">
              <div class="article__content">
                <!-- top  -->
                <div class="article__content-top">
                  <!-- preview  -->
                  <div class="article__preview">


                  <?php if ($img = get_field('article_image')) : ?>
                    <img src="<?php echo esc_url($img); ?>" class="article__preview-img" alt="<?php the_title_attribute(); ?>">
                  <?php endif; ?>




                    <!-- meta  -->
                    <div class="article__meta">
                      <!-- reading time -->
                      <?php
                        $reading_time = get_field('reading_time');
                        if ($reading_time) {
                          echo '<span class="article__meta-item article__reading-time">' . esc_html($reading_time) . ' min</span>';
                        }
                      ?>
                      <!-- categories -->

                      <?php
                        $categories = get_the_category();
                        if (!empty($categories)) {
                          foreach ($categories as $category) {
                            echo '<span class="article__meta-item article__tag">' . esc_html($category->name) . '</span>';
                          }
                        }

                      ?>

                    </div>
                  </div>
                  <a href="<?php the_permalink(); ?>" class="article__title"><?php the_title(); ?></a>
                  <div class="article__date">
                    <?php echo get_the_date('d m Y'); ?>
                  </div>
                </div>
                <!-- bottom  -->
                <div class="article__content-bottom">
                  <a href="<?php the_permalink(); ?>" class="article__link">Voir plus</a>
                </div>
              </div>
            </div>
      <?php
    }
    echo '</div>';

    // Пагинация
    $pagination = paginate_links([
      'total'     => $query->max_num_pages,
      'current'   => $paged,
      'end_size'  => 3,
      'mid_size'  => 0,
      'prev_next' => false,
      'type'      => 'array',
      'format'    => '?paged=%#%',
    ]);

    echo '<ul class="pagination__list">';

    foreach ((array)$pagination as $item) {
      $isActive = strpos($item, 'current') !== false ? 'current' : '';
      echo '<li class="pagination__item ' . $isActive . '">' . wp_kses_post($item) . '</li>';
    }
    echo '</ul>';

  } else {
    echo '<div class="articles__list">Aucun article trouvé.</div>';
  }

  wp_reset_postdata();
  echo ob_get_clean();
  wp_die();
}
