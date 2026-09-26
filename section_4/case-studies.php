<?php

/**
 * Plugin Name: Case Studies
 * Description: Custom Post Type for Case Study functionality
 * Version: 1.0.0
 * Author: Candidate
 */

// register custom post type - case studies
function register_case_study_post_type(): void
{
    register_post_type('case_study', [
        'labels' => [
            'name'          => 'Case Studies',
            'singular_name' => 'Case Study',
            'add_new_item'  => 'Add New Case Study',
            'edit_item'     => 'Edit Case Study',
            'new_item'      => 'New Case Study',
            'view_item'     => 'View Case Study',
            'search_items'  => 'Search Case Studies',
        ],

        'public'       => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-portfolio',

        'supports' => [
            'title',
            'editor',
            'thumbnail',
        ],

        'has_archive' => true,
        'rewrite'     => [
            'slug' => 'case-studies',
        ],
    ]);
}

add_action(
    'init',
    'register_case_study_post_type'
);

// add a shortcode
function featured_case_studies_shortcode(): string
{
    $query = new WP_Query([
        'post_type'      => 'case_study',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    if (!$query->have_posts()) {
        return '<p>No case studies found.</p>';
    }

    $output = '<div class="featured-case-studies">';

    while ($query->have_posts()) {
        $query->the_post();
        $output .= '<article class="case-study">';
        $output .= '<h3>' . esc_html(get_the_title()) . '</h3>';
        $output .= '<p>' . esc_html(get_the_excerpt()) . '</p>';
        $output .= '</article>';
    }

    $output .= '</div>';

    wp_reset_postdata();

    return $output;
}

add_shortcode(
    'featured_case_studies',
    'featured_case_studies_shortcode'
);


// add a filter to modify title for case studies on archive page
function modify_case_study_archive_title(string $title): string
{
    if (is_post_type_archive('case_study')) {
        $title = 'Case Study: ' . $title;
    }

    return $title;
}

add_filter(
    'the_title',
    'modify_case_study_archive_title'
);
