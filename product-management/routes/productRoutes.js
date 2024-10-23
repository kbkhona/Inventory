const express = require('express');
const { createProduct, updateProduct, deleteProduct, getProducts, sendProducts } = require('../controllers/productController');
const { authenticate } = require('../controllers/authController');
const router = express.Router();

// Product management routes, restricted to inventoryAdmin
router.delete('/:sku', authenticate('inventoryAdmin'), deleteProduct);
router.put('/:sku', authenticate('inventoryAdmin'), updateProduct);
router.post('/', authenticate('inventoryAdmin'), createProduct);
// router.get('/', authenticate('inventoryAdmin'), sendProducts);

module.exports = router;
