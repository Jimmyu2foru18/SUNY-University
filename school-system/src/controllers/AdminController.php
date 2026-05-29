<?php
// src/controllers/AdminController.php
require_once __DIR__ . '/BaseController.php';

class AdminController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Admin']);
    }
}
?>
