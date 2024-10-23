<?php
require_once __DIR__ . '/ProductModel.php';
require_once __DIR__ . '/LogisticsService.php';

class ProductController {
    private $productModel;
    private $logisticsService;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->logisticsService = new LogisticsService();
    }

    // Fetch shipping rate based on product SKU and destination
    public function getShippingRate($sku, $destination) {
        $product = $this->productModel->getProductBySKU($sku);
        if (!$product) {
            return json_encode(['error' => 'Product not found']);
        }
        $this->logisticsService->getShippingRate($product['weight'], $destination);
        $rate = 30;
        return json_encode(['shipping_rate' => $product['weight']*$destination*$rate]);
    }

    // Submit an order for shipment and update the product quantity
    public function submitShipmentOrder($sku, $quantity) {
        $product = $this->productModel->getProductBySKU($sku);
        if (!$product || $product['quantity'] < $quantity) {
            return json_encode(['error' => 'Insufficient stock']);
        }

        // Submit order to logistics provider
        $response = $this->logisticsService->submitOrderForShipment($product, $quantity);

        
        // Update product quantity in the database
        $newQuantity = $product['quantity'] - $quantity;
        $this->productModel->updateProductQuantity($sku, $newQuantity);
        

        return json_encode(["new_quantity" => $newQuantity]);
    }
}
