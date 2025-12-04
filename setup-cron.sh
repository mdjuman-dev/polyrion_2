#!/bin/bash

# Laravel Scheduler Cron Job Setup Script
# This script helps you set up the cron job for Laravel scheduler

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}Laravel Scheduler Cron Job Setup${NC}"
echo "=================================="
echo ""

# Get current directory
PROJECT_DIR=$(pwd)
echo -e "Project directory: ${YELLOW}$PROJECT_DIR${NC}"

# Get PHP path
PHP_PATH=$(which php)
if [ -z "$PHP_PATH" ]; then
    echo -e "${RED}Error: PHP not found in PATH${NC}"
    echo "Please install PHP or add it to your PATH"
    exit 1
fi
echo -e "PHP path: ${YELLOW}$PHP_PATH${NC}"

# Check if artisan exists
if [ ! -f "$PROJECT_DIR/artisan" ]; then
    echo -e "${RED}Error: artisan file not found in $PROJECT_DIR${NC}"
    exit 1
fi

# Create cron entry
CRON_ENTRY="* * * * * cd $PROJECT_DIR && $PHP_PATH artisan schedule:run >> /dev/null 2>&1"

echo ""
echo -e "${GREEN}Proposed cron entry:${NC}"
echo -e "${YELLOW}$CRON_ENTRY${NC}"
echo ""

# Ask for confirmation
read -p "Do you want to add this to your crontab? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Check if cron entry already exists
    if crontab -l 2>/dev/null | grep -q "artisan schedule:run"; then
        echo -e "${YELLOW}Warning: A Laravel scheduler cron job already exists${NC}"
        read -p "Do you want to replace it? (y/n) " -n 1 -r
        echo ""
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            # Remove existing entry
            crontab -l 2>/dev/null | grep -v "artisan schedule:run" | crontab -
        else
            echo "Cancelled."
            exit 0
        fi
    fi
    
    # Add new cron entry
    (crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Cron job added successfully!${NC}"
        echo ""
        echo "Current crontab:"
        crontab -l | grep "artisan schedule:run"
        echo ""
        echo -e "${GREEN}The scheduler will run every minute.${NC}"
    else
        echo -e "${RED}Error: Failed to add cron job${NC}"
        exit 1
    fi
else
    echo "Cancelled."
    echo ""
    echo "To add manually, run:"
    echo "crontab -e"
    echo ""
    echo "Then add this line:"
    echo -e "${YELLOW}$CRON_ENTRY${NC}"
fi
