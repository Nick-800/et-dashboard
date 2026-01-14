#!/bin/bash

# Quick Test Script for ET Dashboard API
# Make this file executable: chmod +x test_api.sh

echo "🚀 ET Dashboard API Test Script"
echo "================================"
echo ""

# Configuration
BASE_URL="http://127.0.0.1:8000/api"
EMAIL="admin@mail.com"
PASSWORD="password"  # Change this to your actual password

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}📝 Step 1: Testing Login${NC}"
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}")

echo "$LOGIN_RESPONSE" | jq '.'

# Extract token
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data.token')

if [ "$TOKEN" != "null" ] && [ -n "$TOKEN" ]; then
    echo -e "${GREEN}✅ Login successful!${NC}"
    echo "Token: $TOKEN"
else
    echo -e "${RED}❌ Login failed. Please check your credentials.${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}📝 Step 2: Testing GET /images (Public)${NC}"
IMAGES_RESPONSE=$(curl -s -X GET "$BASE_URL/images")
echo "$IMAGES_RESPONSE" | jq '.'

if [ "$(echo "$IMAGES_RESPONSE" | jq -r '.success')" == "true" ]; then
    echo -e "${GREEN}✅ Successfully retrieved images${NC}"
else
    echo -e "${RED}❌ Failed to retrieve images${NC}"
fi

echo ""
echo -e "${YELLOW}📝 Step 3: Testing GET /projects (Public)${NC}"
PROJECTS_RESPONSE=$(curl -s -X GET "$BASE_URL/projects")
echo "$PROJECTS_RESPONSE" | jq '.'

if [ "$(echo "$PROJECTS_RESPONSE" | jq -r '.success')" == "true" ]; then
    echo -e "${GREEN}✅ Successfully retrieved projects${NC}"
else
    echo -e "${RED}❌ Failed to retrieve projects${NC}"
fi

echo ""
echo -e "${YELLOW}📝 Step 4: Testing Create Project (Protected)${NC}"
PROJECT_RESPONSE=$(curl -s -X POST "$BASE_URL/projects" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "Web Development",
    "name": "Test Project",
    "description": "This is a test project created via API",
    "year": 2026,
    "services": ["Frontend", "Backend", "API Development"],
    "images": []
  }')

echo "$PROJECT_RESPONSE" | jq '.'

if [ "$(echo "$PROJECT_RESPONSE" | jq -r '.success')" == "true" ]; then
    PROJECT_ID=$(echo "$PROJECT_RESPONSE" | jq -r '.data.id')
    echo -e "${GREEN}✅ Successfully created project (ID: $PROJECT_ID)${NC}"

    # Cleanup - Delete the test project
    echo ""
    echo -e "${YELLOW}📝 Step 5: Cleaning up - Deleting test project${NC}"
    DELETE_RESPONSE=$(curl -s -X DELETE "$BASE_URL/projects/$PROJECT_ID" \
      -H "Authorization: Bearer $TOKEN")

    echo "$DELETE_RESPONSE" | jq '.'

    if [ "$(echo "$DELETE_RESPONSE" | jq -r '.success')" == "true" ]; then
        echo -e "${GREEN}✅ Successfully deleted test project${NC}"
    else
        echo -e "${RED}❌ Failed to delete test project${NC}"
    fi
else
    echo -e "${RED}❌ Failed to create project${NC}"
fi

echo ""
echo -e "${YELLOW}📝 Step 6: Testing Logout${NC}"
LOGOUT_RESPONSE=$(curl -s -X POST "$BASE_URL/logout" \
  -H "Authorization: Bearer $TOKEN")

echo "$LOGOUT_RESPONSE" | jq '.'

if [ "$(echo "$LOGOUT_RESPONSE" | jq -r '.success')" == "true" ]; then
    echo -e "${GREEN}✅ Successfully logged out${NC}"
else
    echo -e "${RED}❌ Failed to logout${NC}"
fi

echo ""
echo "================================"
echo -e "${GREEN}🎉 API Tests Complete!${NC}"
echo ""
echo "Next steps:"
echo "1. Upload an image: Use the dashboard at http://127.0.0.1:8000/admin"
echo "2. Create projects with image URLs"
echo "3. Integrate with your website using the public GET endpoints"
echo ""
echo "For more details, see:"
echo "  - API_DOCUMENTATION.md"
echo "  - SETUP_GUIDE.md"
echo "  - SYSTEM_OVERVIEW.md"
