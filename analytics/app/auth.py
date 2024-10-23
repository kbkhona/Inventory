from fastapi import Depends, HTTPException
from fastapi.security import HTTPAuthorizationCredentials, HTTPBearer
from jose import JWTError, jwt
from dotenv import load_dotenv
from pydantic import BaseModel
import os

load_dotenv('../.env')

JWT_SECRET = os.getenv("JWT_SECRET")
ALGORITHM = os.getenv("ALGORITHM")
VALID_ROLE = os.getenv("VALID_ROLE_ANALYTICS")


# User roles and JWT authentication
class TokenData(BaseModel):
    username: str = None
    role: str = None

security = HTTPBearer()

def authenticate_jwt(token: HTTPAuthorizationCredentials = Depends(security)):
    credentials_exception = HTTPException(status_code=403, detail="Could not validate credentials")
    try:
        payload = jwt.decode(token.credentials, JWT_SECRET , algorithms=[ALGORITHM])
        print('payload', payload)
        username: str = payload.get("username")
        role: str = payload.get("role")
        if username is None or role is None:
            raise credentials_exception
        return TokenData(username=username, role=role)
    except JWTError:
        raise credentials_exception


def authenticate_reporter(token:TokenData = Depends(authenticate_jwt)):
    print(f"role here is{token.role}" )
    print(f"VALID_ROLE is {VALID_ROLE}" )
    if token.role != VALID_ROLE:
        raise HTTPException(status_code=403, detail="Not authorized")
    return token