# Foodiclone Project

## Overview
FoodieClone is a comprehensive online food ordering and delivery platform that connects customers with a wide range of restaurants, allowing them to order food from anywhere and make payments using various methods such as Razor pay, Stripe, Braintree, or opt for Cash on Delivery.

## Prerequisites
Before you begin, ensure you have the following installed:
- PHP (8.2)
- MySQL (10.6.18-MariaDB-cll-lve)
- Apache or Nginx web server (2.4.62)
- Composer (2.6.6)

## Database Setup

1. **Create the Database:**
   - Run the following SQL command to create the database:
     ```sql
     CREATE DATABASE foodiclone;
     ```

2. **Import the Database File:**    
   - Import the provided `foodieclone.sql` file into the `foodiclone` database using a tool like phpMyAdmin or the MySQL command line:
     ```bash
     mysql -u username -p foodiclone < path/to/database.sql
     ```

## Project Setup

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/vidhi-katrodiya/foodiclone.git
