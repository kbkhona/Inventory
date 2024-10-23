# app/scheduler.py

import schedule
import time
from threading import Thread
from app.reports import generate_report

def daily_job():
    print("Generating daily report...")
    generate_report()

def start_scheduler():
    # schedule.every().day.at("00:00").do(daily_job)
    schedule.every(4).minutes.do(daily_job)
    
    def run_scheduler():
        while True:
            schedule.run_pending()
            time.sleep(1)
    
    # Run scheduler in a background thread
    scheduler_thread = Thread(target=run_scheduler)
    scheduler_thread.daemon = True
    scheduler_thread.start()
