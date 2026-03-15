<?php
/**
 * ANT-Press Theme Configuration API
 *
 * Exposes theme_mods via REST for classic themes,
 * logo upload endpoint, theme-type detection, and
 * reliable theme activation via switch_theme().
 *
 * Endpoints:
 *   GET/POST /wp-json/ant-press/v1/theme-mods
 *   POST     /wp-json/ant-press/v1/logo
 *   GET      /wp-json/ant-press/v1/theme-type
 *   POST     /wp-json/ant-press/v1/activate-theme
 *
 * Requires: edit_theme_options capability (admin).
 * Loaded automatically as a must-use plugin.
 *
 * @package ANT_Press
 */

add_action( 'rest_api_init', function () {

    // ── Theme Mods (GET / POST) ───────────────────────────
    register_rest_route( 'ant-press/v1', '/theme-mods', [
        [
            'methods'             => 'GET',
            'callback'            => 'antpress_get_theme_mods',
            'permission_callback' => function () {
                return current_user_can( 'edit_theme_options' );
            },
        ],
        [
            'methods'             => 'POST',
            'callback'            => 'antpress_set_theme_mods',
            'permission_callback' => function () {
                return current_user_can( 'edit_theme_options' );
            },
        ],
    ] );

    // ── Logo ──────────────────────────────────────────────
    register_rest_route( 'ant-press/v1', '/logo', [
        'methods'             => 'POST',
        'callback'            => 'antpress_set_logo',
        'permission_callback' => function () {
            return current_user_can( 'edit_theme_options' );
        },
    ] );

    // ── Theme Type Detection ──────────────────────────────
    register_rest_route( 'ant-press/v1', '/theme-type', [
        'methods'             => 'GET',
        'callback'            => 'antpress_get_theme_type',
        'permission_callback' => function () {
            return current_user_can( 'edit_theme_options' );
        },
    ] );

    // ── Theme Activation (ANT-206) ────────────────────────
    register_rest_route( 'ant-press/v1', '/activate-theme', [
        'methods'             => 'POST',
        'callback'            => 'antpress_activate_theme',
        'permission_callback' => function () {
            return current_user_can( 'switch_themes' );
        },
    ] );
} );


/**
 * GET /wp-json/ant-press/v1/theme-mods
 * Returns all current theme_mods (except internal WP key).
 */
function antpress_get_theme_mods() {
    $mods = get_theme_mods();
    unset( $mods[0] ); // Remove internal WP key
    return rest_ensure_response( $mods );
}


/**
 * POST /wp-json/ant-press/v1/theme-mods
 * Accepts a JSON object of key→value pairs and sets each as a theme_mod.
 */
function antpress_set_theme_mods( $request ) {
    $settings = $request->get_json_params();

    if ( empty( $settings ) || ! is_array( $settings ) ) {
        return new WP_Error(
            'invalid_body',
            'Request body must be a non-empty JSON object.',
            [ 'status' => 400 ]
        );
    }

    $updated = [];
    foreach ( $settings as $key => $value ) {
        set_theme_mod( sanitize_key( $key ), $value );
        $updated[ $key ] = $value;
    }

    return rest_ensure_response( [ 'updated' => $updated ] );
}


/**
 * POST /wp-json/ant-press/v1/logo
 * Sets the custom_logo theme_mod to the given media attachment ID.
 */
function antpress_set_logo( $request ) {
    $media_id = $request->get_param( 'media_id' );

    if ( ! $media_id || ! is_numeric( $media_id ) ) {
        return new WP_Error(
            'missing_media_id',
            'A numeric media_id is required.',
            [ 'status' => 400 ]
        );
    }

    $media_id = (int) $media_id;

    // Verify attachment exists
    if ( ! wp_attachment_is_image( $media_id ) ) {
        return new WP_Error(
            'invalid_media',
            'media_id must reference an existing image attachment.',
            [ 'status' => 400 ]
        );
    }

    set_theme_mod( 'custom_logo', $media_id );

    return rest_ensure_response( [
        'logo_media_id' => $media_id,
        'message'       => 'Logo updated successfully.',
    ] );
}


/**
 * GET /wp-json/ant-press/v1/theme-type
 * Detects whether the active theme is a classic (PHP) or block (FSE) theme.
 */
function antpress_get_theme_type() {
    $theme    = wp_get_theme();
    $is_block = $theme->is_block_theme();

    return rest_ensure_response( [
        'name'     => $theme->get( 'Name' ),
        'slug'     => $theme->get_stylesheet(),
        'is_block' => $is_block,
        'type'     => $is_block ? 'block' : 'classic',
    ] );
}


/**
 * POST /wp-json/ant-press/v1/activate-theme
 * Activates a theme by slug using switch_theme() — reliable across WP versions.
 *
 * Request body: {"theme_slug": "theme-slug-here"}
 *
 * @since ANT-206
 */
function antpress_activate_theme( $request ) {
    $theme_slug = $request->get_param( 'theme_slug' );

    if ( empty( $theme_slug ) ) {
        return new WP_Error(
            'missing_slug',
            'theme_slug is required.',
            [ 'status' => 400 ]
        );
    }

    $theme = wp_get_theme( $theme_slug );

    if ( ! $theme->exists() ) {
        return new WP_Error(
            'theme_not_found',
            "Theme '$theme_slug' not found.",
            [ 'status' => 404 ]
        );
    }

    switch_theme( $theme_slug );

    $active = wp_get_theme();

    return rest_ensure_response( [
        'stylesheet' => $active->get_stylesheet(),
        'name'       => $active->get( 'Name' ),
        'status'     => 'active',
    ] );
}
