<?php
// namespace Drupal\custom_module;
session_start();

session_unset();
session_destroy();
header('Location: /');
exit;
