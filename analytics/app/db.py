from pymongo import MongoClient
from dotenv import load_dotenv
import os

load_dotenv('../.env')


# MongoDB connection
MONGO_URI = os.getenv("MONGO_URI")
client = MongoClient(MONGO_URI)
db = client.inventory