const express = require('express');
const mongoose = require('mongoose');
const productRoutes = require('./routes/productRoutes');
const authRoutes = require('./routes/authRoutes');
const dotenv = require('dotenv');

dotenv.config({ path: '../.env' });

const app = express();
app.use(express.json());

mongoose.connect(process.env.MONGO_URI).then(()=>console.log('DB connected'));

app.use('/api/products', productRoutes);
app.use('/api/auth', authRoutes);

app.listen(3000, () => {
  console.log('Server running on port 3000');
});
