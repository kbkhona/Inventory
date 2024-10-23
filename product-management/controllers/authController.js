const User = require('../models/User');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const dotenv = require('dotenv');

dotenv.config({ path: '../.env' });

const register = async (req, res) => {
  const { username, password, role } = req.body;

  if(!role) return res.status(400).json({ message: 'Bad Request: Role needed'});
  if(!username) return res.status(400).json({ message: 'Bad Request: Username needed'});
  if(!password) return res.status(400).json({ message: 'Bad Request: Password needed'});
  // Check if user already exists
  const userExists = await User.findOne({ username });
  if (userExists) return res.status(400).json({ message: 'User already exists' });

  // Hash password and create user
  const hashedPassword = await bcrypt.hash(password, 10);
  const user = new User({ username, password: hashedPassword, role });
  await user.save();

  res.status(201).json({ message: 'User registered successfully', username: username, role: role });
};

const login = async (req, res) => {
  const { username, password } = req.body;
  
  const user = await User.findOne({ username });``
  if (!user) return res.status(400).json({ message: 'Invalid credentials' });

  const isPasswordValid = await bcrypt.compare(password, user.password);
  if (!isPasswordValid) return res.status(400).json({ message: 'Invalid credentials' });

  const token = jwt.sign({ username: user.username, role: user.role }, process.env.JWT_SECRET, {
    expiresIn: '20h'
  });

  res.json({ token });
};

const authenticate = (role) => {
  return (req, res, next) => {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) return res.status(401).json({ message: 'Access denied' });

    try {
      const decoded = jwt.verify(token, process.env.JWT_SECRET);
      if (role && decoded.role !== role) {
        return res.status(403).json({ message: 'Forbidden' });
      }
      req.user = decoded;
      next();
    } catch (err) {
      res.status(400).json({ message: 'Invalid token' });
    }
  };
};

module.exports = { register, login, authenticate };
