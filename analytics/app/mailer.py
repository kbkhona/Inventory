# app/mailer.py

import os
import requests
from dotenv import load_dotenv

load_dotenv('../.env')

MAILGUN_API_KEY = os.getenv("MAILGUN_API_KEY")
MAILGUN_DOMAIN = os.getenv("MAILGUN_DOMAIN")
MAILGUN_API_URL = os.getenv("MAILGUN_API_URL")
SENDER_EMAIL = os.getenv("SENDER_EMAIL")

def send_email(receiver_emails, report_file):
    url = MAILGUN_API_URL
    sender = SENDER_EMAIL
    
    # Read the attachment file
    with open(report_file, "rb") as attachment:
        files = {
            "attachment": (report_file, attachment)
        }
        print(receiver_emails)
        for receiver in receiver_emails:
            data = {
                "from": sender,
                "to": receiver,
                "subject": "Daily Inventory Report",
                "text": "Please find attached the daily inventory report."
            }
            print(f"Sending mail to  {receiver}")
            # Send the request to the Mailgun API
            response = requests.post(
                url,
                auth=("api", MAILGUN_API_KEY),
                data=data,
                files=files
            )

            # Print response for debugging
            print(f"Email sent to {receiver}: {response.status_code}, {response.text}")


