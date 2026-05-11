<?php require 'includes/db.php'; echo \->query('SELECT COUNT(*) FROM restaurants')->fetchColumn(); ?>
