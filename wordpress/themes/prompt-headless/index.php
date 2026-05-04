<?php
/**
 * Main template for Prompt Headless theme.
 *
 * This theme is purely headless. The frontend is served by the Laravel application.
 * Any direct access to WordPress will be redirected to the admin dashboard.
 */

declare(strict_types=1);

// Redirect frontend access to the admin panel.
wp_redirect(admin_url());
exit;
