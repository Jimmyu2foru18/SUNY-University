<?php
// src/controllers/FacultyController.php
require_once __DIR__ . '/BaseController.php';

class FacultyController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Faculty']);
    }
}
?>
