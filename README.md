# Billing System - Database Setup Instructions

## Step 1: Create the Database

1. Open **phpMyAdmin** at: `http://localhost/phpmyadmin`
2. Click on the **SQL** tab
3. Copy the entire content from `database.sql` file
4. Paste it in the SQL textarea
5. Click **Go** to create the database and tables

**OR use command line:**
```
mysql -u root -p < database.sql
```

## Step 2: Start XAMPP

1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**

## Step 3: Access the Billing System

Open your browser and go to:
```
http://localhost/BillingSystem/index.html
```

## Features

✅ **Add Items** - Add products with quantity and price
✅ **Calculate Tax** - Automatic tax calculation
✅ **Save Bills** - Click "💾 Save Bill" to store in database with date
✅ **View Saved Bills** - Click "📋 View Saved Bills" to see all saved invoices
✅ **View Details** - Click "View" to see complete bill details
✅ **Print Bills** - Print invoice directly to PDF
✅ **Customer Database** - Automatically stores customer info

## Database Structure

### customers table
- id (Primary Key)
- name (Customer Name)
- phone (Phone Number)
- created_at (Timestamp)

### bills table
- id (Primary Key)
- customer_id (Foreign Key)
- subtotal (Total before tax)
- tax_percent (Tax percentage)
- tax_amount (Calculated tax)
- grand_total (Final amount)
- created_at (Bill Date & Time)

### bill_items table
- id (Primary Key)
- bill_id (Foreign Key)
- item_name (Product name)
- quantity (Qty purchased)
- price (Price per unit)
- total (Quantity × Price)

## Troubleshooting

**Error: "Connection failed"**
- Make sure MySQL is running in XAMPP
- Check username/password in api.php (default: root, no password)
- Make sure `billing_system` database exists

**Error: "Database does not exist"**
- Run the database.sql file again in phpMyAdmin
- Or use command: `mysql -u root -p < database.sql`

**Cannot save bills**
- Check browser console for errors (Press F12)
- Make sure api.php is in the same folder as index.html
- Verify MySQL is running

## How to Use

1. **Enter Customer Info** → Name & Phone (required for save)
2. **Add Items** → Product name, Quantity, Price → Click "Add Item to Bill"
3. **Set Tax** → Enter tax percentage (optional)
4. **Save Bill** → Click "💾 Save Bill" to store in database
5. **View Saved Bills** → Click "📋 View Saved Bills" to see history
6. **Print Bill** → Click "🖨️ Print Bill" to get PDF

All bills are automatically saved with:
- Date & Time
- Customer Name & Phone
- All items with prices
- Tax calculation
- Grand total
