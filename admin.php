<?php


function create_decoratable_products_cpt() {
    $labels = array(
        'name'                  => _x('Decoratable Products', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Decoratable Product', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Decoratable Products', 'text_domain'),
        'name_admin_bar'        => __('Decoratable Product', 'text_domain'),
        'archives'              => __('Decoratable Product Archives', 'text_domain'),
        'attributes'            => __('Product Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent Product:', 'text_domain'),
        'all_items'             => __('All Decoratable Products', 'text_domain'),
        'add_new_item'          => __('Add New Decoratable Product', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Decoratable Product', 'text_domain'),
        'edit_item'             => __('Edit Decoratable Product', 'text_domain'),
        'update_item'           => __('Update Decoratable Product', 'text_domain'),
        'view_item'             => __('View Decoratable Product', 'text_domain'),
        'view_items'            => __('View Decoratable Products', 'text_domain'),
        'search_items'          => __('Search Decoratable Product', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into product', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this product', 'text_domain'),
        'items_list'            => __('Decoratable Products list', 'text_domain'),
        'items_list_navigation' => __('Decoratable Products list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter products list', 'text_domain'),
    );

    $args = array(
        'label'                 => __('Decoratable Product', 'text_domain'),
        'description'           => __('Post Type Description', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'description'),
        'taxonomies'            => array(),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-art',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );

    register_post_type('decoratable_product', $args);
}

add_action('init', 'create_decoratable_products_cpt');