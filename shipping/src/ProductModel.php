<?php
require_once __DIR__ . '/../config/database.php';

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = getDb();
    }

    public function getProductBySKU($sku) {
        return $this->db->products->findOne(['sku' => $sku]);
    }

    public function updateProductQuantity($sku, $newQuantity) {
        return $this->db->products->updateOne(
            ['sku' => $sku],
            ['$set' => ['quantity' => $newQuantity]]
        );
    }
}
