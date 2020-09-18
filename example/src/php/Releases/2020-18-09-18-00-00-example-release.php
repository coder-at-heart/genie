<?php

global $wpdb;

$wpdb->query("delete from $wpdb->postmeta where post_id not in (select ID from $wpdb->posts)");

echo "completed";