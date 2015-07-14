=== LK Favorite Posts ===
Contributors: eduardo-leoni
Tags: wordpress, favorite, bookmark, post
Requires at least: 4.1
Tested up to: 4.2.2

Wordpress plugin that allows the user to bookmark their favorite posts.

== Description ==
This plugin allows the user to bookmark posts as favourite. You can place this \"Add/Remove\" button on any single post page. When the button pressed, it\'s saves the post id with the user id to the database. You can get the favourite posts as an array, if you want to show them (e.g. user profile page).

== Installation ==
1. Upload \'favorite-posts\' to the \'/wp-content/plugins/\' directory,
2. Activate the plugin through the \'Plugins\' menu in WordPress.
3. Place \"echo get_favorites_add_link();\" on a single template to show the button.
4. Use \"get_favorite_posts(USERID)\" to get the user favorite posts. USERID can be \"get_current_user_id()\" to get the current users posts.