<?php

class LogisticsService {
    private $apiUrl;

    public function __construct() {
        $this->apiUrl = $_ENV['LOGISTICS_API_URL'];
    }

    public function getShippingRate($weight, $destination) {
        $data = [
            'weight' => $weight,
            'destination' => $destination
        ];

        $response = $this->makeRequest('/ext/shipping-rate', $data);
        return $response ?? null;
    }

    public function submitOrderForShipment($product, $quantity) {
        $data = [
            'product' => $product,
            'quantity' => $quantity
        ];

        $response = $this->makeRequest('ext/submit-order', $data);
        return $response;
    }

    private function makeRequest($endpoint, $data) {
        $url = $this->apiUrl . $endpoint;
    
        $ch = curl_init($url);
        
        // Set curl options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true);           // Use POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Send JSON data
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Set content type to JSON
            'Accept: application/json'         // Expect JSON response
        ]);
    
        // Execute the request
        $response = curl_exec($ch);
    
        // Check for curl errors
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => true, 'message' => $error];
        }
    
        curl_close($ch);
    
        // Decode and return the response
        return json_decode($response, true);
    }    
}
