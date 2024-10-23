
from fastapi import FastAPI, Depends
from app.auth import TokenData
from dotenv import load_dotenv
from contextlib import asynccontextmanager

from app.auth import authenticate_reporter
from app.reports import generate_report as report_generator
from app.scheduler import start_scheduler

load_dotenv('../.env')


@asynccontextmanager
async def lifespan(app: FastAPI):
    start_scheduler()  # Start daily report generation on app startup
    yield

app = FastAPI(lifespan=lifespan)

# Generate inventory report
@app.get("/api/generate_report")
def generate_report(token: TokenData = Depends(authenticate_reporter)):
    print('authentication succesful');
    return report_generator()


# @app.get("/")
# def generate_report(token: TokenData = Depends(authenticate_reporter)):
#     print('hi')
#     return
