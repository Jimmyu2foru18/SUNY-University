<?php
// src/controllers/StatStaffController.php
require_once __DIR__ . '/BaseController.php';

class StatStaffController extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['StatStaff']);
    }
}
?>
