const { WebSocketServer } = require('ws');
const Product = require('../models/Product');

// WebSocket for stock updates
const wss = new WebSocketServer({ port: 8080 });

wss.on('connection', async (ws) => {
    console.log('Client connected');
    const products = await Product.find();
    ws.send(JSON.stringify(products))
    ws.on('close', () => {
        console.log('Client disconnected');
    });
});


const createProduct = async (req, res) => {
  const { name, sku, quantity, price, weight, description } = req.body;
  try {
    const product = new Product({ name, sku, quantity, price, weight, description });
    await product.save();
    sendProducts()
    res.status(201).json(product);
  } catch (error) {
    res.status(400).json({ message: error});
  }
  
};

const updateProduct = async (req, res) => {
  const productsku = req.params.sku;
  const updates = req.body;
  try {
    const product = await Product.findOneAndUpdate({sku: productsku}, updates, { new: true });
    sendProducts()
    res.json(product);
  } catch (error) {
    res.status(400).json({ message: error});
  }
  
};

const deleteProduct = async (req, res) => {
  const productSku = req.params.sku;
  try {
    await Product.findOneAndDelete({sku: productSku});    
  } catch (error) {
    res.status(400).json({ errormessage: error});
  }
  res.json({ message: 'Product deleted' });
  sendProducts()
};

const sendProducts = async () => {
  wss.clients.forEach(async client => {
    if (client.readyState === client.OPEN) {
        const products = await Product.find();
        client.send(JSON.stringify(products));
    }
  });
};

module.exports = { createProduct, updateProduct, deleteProduct, sendProducts };
