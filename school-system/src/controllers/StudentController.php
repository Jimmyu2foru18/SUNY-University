<?php
// src/controllers/StudentController.php
require_once __DIR__ . '/BaseController.php';

class StudentController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Student']);
    }
}
?>
