<?php
$c = file_get_contents(__DIR__ . '/../resources/js/Pages/Admin/Dashboard.vue');
echo substr_count($c, '<div') . ' open, ' . substr_count($c, '</div') . ' close';
