from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.lib.units import inch
from reportlab.lib.pagesizes import A4
from reportlab.platypus import SimpleDocTemplate, Table, TableStyle, Paragraph
from datetime import datetime

from dotenv import load_dotenv
import os
from app.db import db
from app.mailer import send_email

load_dotenv('../.env')

RECEIVER_EMAILS = os.getenv("RECEIVER_EMAILS")

def generate_pdf_report(file_path):
    # Create the PDF file
    doc = SimpleDocTemplate(file_path, pagesize=A4)
    
    # Set up styles and container for elements
    styles = getSampleStyleSheet()
    elements = []
    
    # Title for the report
    title = Paragraph("Inventory Report", styles['Title'])
    elements.append(title)
    
    # Fetch all products from the database
    products = db.products.find({})
    
    # Initialize table data with headers
    table_data = [['Product Name', 'S`K`U', 'Quantity', 'Price (per unit)', 'Weight (per unit)', 'Total Value']]
    total_inventory_value = 0
    out_of_stock_products = []

    # Add products to the table
    for product in products:
        product_name = product.get('name')
        sku = product.get('sku')
        quantity = product.get('quantity')
        price = product.get('price')
        weight = product.get('weight')
        total_value = quantity * price if quantity and price else 0
        total_inventory_value += total_value
        
        # Check for out-of-stock products
        if quantity == 0:
            out_of_stock_products.append(product_name)

        # Add the row to the table
        table_data.append([product_name, sku, quantity, price, weight, total_value])

    # Create the table for inventory
    table = Table(table_data)
    table.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.lightblue),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
        ('FONTSIZE', (0, 0), (-1, 0), 12),
        ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
        ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
        ('GRID', (0, 0), (-1, -1), 1, colors.black),
    ]))
    elements.append(table)
    
    # Add out-of-stock section if there are any out-of-stock products
    if out_of_stock_products:
        elements.append(Paragraph("Out-of-Stock Products:", styles['Heading2']))
        for product in out_of_stock_products:
            elements.append(Paragraph(product, styles['Normal']))

    # Add overall inventory value at the bottom
    elements.append(Paragraph(f"Overall Inventory Value: ${total_inventory_value:.2f}", styles['Heading2']))

    # Build the PDF
    doc.build(elements)


def generate_report():
    # Get current date in desired format (e.g., YYYY-MM-DD)
    current_date = datetime.now().strftime("%d-%b_%H:%M")

    # Insert the date into the filename
    report_file = f"daily_inventory_report_{current_date}.pdf"
    
    
    # Generate the report and save it
    generate_pdf_report(report_file)
    
    # Send the email with the report attached
    print(f"receiver env {RECEIVER_EMAILS}")
    email_list = RECEIVER_EMAILS.split(",")
    send_email(email_list, report_file)

    return {"status": "Report generated and emailed successfully"}