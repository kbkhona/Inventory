const mongoose = require('mongoose');

const productSchema = new mongoose.Schema({
  name: { type: String, required: true },
  sku: { type: String, required: true, unique: true, minlength: 5, maxlength: 10},
  quantity: { type: Number, required: true },
  price: { type: Number, required: true },
  weight: { type: Number, required: true },
  description: String
});

module.exports = mongoose.model('Product', productSchema);
