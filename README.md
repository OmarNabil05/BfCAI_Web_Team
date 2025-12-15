# My Store - College Web Project

A full-featured food delivery e-commerce web application built with HTML, CSS, Bootstrap, PHP, and MySQL.

## Project Structure

```
Web_project/
├── index.php                    # Landing page
├── image.php                    # Image serving endpoint (BLOB storage)
├── config/
│   └── db.php                   # Database connection
├── database/
│   └── my_store.sql            # Complete database schema with migrations
├── modules/
│   ├── auth/                    # ✅ Authentication System
│   │   ├── login.php            # User login
│   │   ├── register.php         # User registration
│   │   ├── logout.php           # Session logout
│   │   ├── process_login.php    # Login handler
│   │   └── process_register.php # Registration handler
│   ├── admin/                   # ✅ Admin Panel (Complete)
│   │   ├── index.php            # Dashboard
│   │   ├── users.php            # User management
│   │   ├── products.php         # Product management with category filter
│   │   ├── process_product.php  # Product CRUD handler
│   │   ├── categories.php       # Category management
│   │   ├── process_category.php # Category CRUD handler
│   │   ├── orders.php           # Order management with date filter
│   │   ├── process_order_status.php # Order status handler
│   │   ├── get_order_details.php    # Order details modal
│   │   └── components/          # Shared admin components
│   │       ├── sidebar.php      # Admin navigation
│   │       ├── styles.css       # Admin styling
│   │       └── scripts.js       # Admin JavaScript
│   └── restaurant/              # ✅ Customer Frontend
│       ├── home.php             # Homepage with categories & popular items
│       ├── menu.php             # Browse products
│       └── cart.php             # Shopping cart
└── .gitignore
```

## Key Features

### Admin Panel
- **Dashboard** - Overview of system statistics
- **User Management** - View and manage customer accounts
- **Product Management**
  - Add/Edit/Delete products with image upload
  - Filter products by category
  - BLOB-based image storage system
- **Category Management**
  - Manage product categories with images
  - Database-stored category images
- **Order Management**
  - 5-stage order workflow: Received → Preparing → In Delivery → Delivered/Cancelled
  - Date filtering (Today's Orders / All Orders)
  - Order status tracking
  - Detailed order view with items
  - Payment status filtering

### Customer Frontend
- **Home Page** - Featured categories and popular items
- **Browse Menu** - View all available food items
- **Shopping Cart** - Add/remove items, adjust quantities
- **Image System** - All images served from database via `image.php` endpoint

### Technical Features
- **BLOB Image Storage** - Images stored in database, not filesystem
- **Secure Authentication** - Session-based login system
- **Responsive Design** - Bootstrap 5 for mobile-first UI
- **Dark Theme** - Modern dark UI with gold accents
- **Modal Dialogs** - Bootstrap modals for forms and details
- **Prepared Statements** - SQL injection prevention

## Getting Started

### 1. Setup Local Environment

1. Install XAMPP (includes Apache + MySQL + PHP)
2. Clone this repository to `C:\xampp\htdocs\Web_project\` (or your htdocs folder)

### 2. Setup Database

1. Start XAMPP (Apache and MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Create database: `my_store`
4. Import `database/my_store.sql`

### 3. Configure Database Connection

1. Use `config/db.php` 
2. Update credentials if needed (default works for XAMPP):
   ```php
   $db_host = '127.0.0.1';
   $db_user = 'root';
   $db_pass = '';
   $db_name = 'my_store';
   ```

### 4. Access the Application

**Customer Frontend:** `http://localhost/Web_project/modules/restaurant/home.php`
**Admin Panel:** `http://localhost/Web_project/modules/admin/`

## Image Storage System

This project uses a **database BLOB storage** approach for images instead of filesystem storage.

### How It Works
1. Images are uploaded and stored as binary data (LONGBLOB) in the `images` table
2. Each product/category has an `image_id` foreign key referencing the images table
3. Images are served via `image.php?id=123` endpoint with proper MIME types and caching headers

### Benefits
- No broken image paths
- Easy backup with database dumps
- Centralized image management
- Automatic cleanup when items are deleted

## Order Management Workflow

### Order Status Flow
```
Received (0) → Preparing (1) → In Delivery (2) → Delivered (3)
                                              ↘ Cancelled (4)
```

### Features
- **Active Orders** - Received, Preparing, In Delivery shown by default
- **Collapsible Sections** - Delivered and Cancelled orders hidden in collapsible tables
- **Date Filter** - Toggle between Today's Orders and All Orders
- **Status Update** - Dropdown selector updates order status instantly
- **Payment Filter** - Only shows orders with confirmed payment (payment_status = 1)

## Working on Features (Team Members)

### Feature-Based Development

Each team member works on ONE complete feature in isolation to minimize merge conflicts.

### Step-by-Step Guide

1. **Pick a feature** from the list:
   - Products (browse, view details, search)
   - Shopping Cart (add/remove items, update quantities)
   - Orders (place order, view history, track payment_status)
   - Categories (browse by category, manage categories)
   - Admin Panel (dashboard, user management)

2. **Create your feature folder:**
   ```
   modules/your-feature-name/
   ```

3. **Create your files** (see `modules/auth/` for example):
   - HTML pages with Bootstrap
   - PHP processing files

4. **Include database connection** at the top of PHP files:
   ```php
   require_once '../../config/db.php';
   ```

5. **Test locally:**
   ```
   http://localhost/Web_project/modules/your-feature/your-page.php
   ```

### Git Workflow

1. **Create feature branch:**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Work only in your feature folder:**
   ```
   modules/your-feature-name/
   ```

3. **Commit regularly:**
   ```bash
   git add modules/your-feature-name/
   git commit -m "Add your-feature: describe what you did"
   ```

4. **Push your branch:**
   ```bash
   git push origin feature/your-feature-name
   ```

5. **Create Pull Request** on GitHub when ready

### Database Migrations

If you need to add/modify database tables:

1. Create `migration.sql` in your feature folder
2. Document changes in your feature's README.md
3. Notify team to run the SQL after pulling your code

Example `modules/products/migration.sql`:
```sql
-- Add stock column to items table
ALTER TABLE items ADD COLUMN stock INT DEFAULT 0;
```

## Example Feature Structure

See `modules/auth/` for a complete working example with:
- Login/Register pages
- Form processing
- Session management
- Database queries
- Bootstrap styling


## Database Schema

### Tables
- `users` - User accounts (customers and admins)
  - Roles: 0 = Customer, 1 = Admin
- `categories` - Product categories
  - `image_id` - Foreign key to images table
- `items` - Products/food items for sale
  - `image_id` - Foreign key to images table
  - `category_id` - Foreign key to categories
- `orders` - Customer orders
  - `order_status` - 0: Received, 1: Preparing, 2: In Delivery, 3: Delivered, 4: Cancelled
  - `payment_status` - 0: Pending, 1: Paid
- `order_items` - Items in each order
- `images` - BLOB storage for all images (NEW)
  - `mime_type` - image/jpeg, image/png, image/gif
  - `data` - LONGBLOB binary data
  - `original_name` - Original filename
  - `created_at` - Upload timestamp

### Recent Migrations
- Added `images` table for BLOB storage
- Added `image_id` column to `items` table
- Added `image_id` column to `categories` table
- Added `order_status` column to `orders` table

See `database/my_store.sql` for complete schema.

## Test Users

### Customer Account
```
Email: alice@example.com
Password: pass123
```

### Admin Account
```
Email: admin@example.com
Password: adminpass
```

**Note:** Admin users have `role = 1` in the database.

## Team Best Practices

1. ✅ Work in your own feature folder only
2. ✅ Use feature branches (not main)
3. ✅ Test before committing
4. ✅ Use Bootstrap CDN (no local files)
5. ✅ Include `../../config/db.php` for database
6. ✅ Use prepared statements for SQL queries
7. ✅ Add comments in your code
8. ❌ Don't edit other people's feature folders
9. ❌ Don't commit `config/db.php` (already in .gitignore)
10. ❌ Don't work directly on main branch

## Technologies Used

- **Frontend:** HTML5, CSS3, Bootstrap 5.3.2, Bootstrap Icons
- **Backend:** PHP 8.0
- **Database:** MySQL (MariaDB 10.4)
- **Server:** Apache (XAMPP)
- **Image Storage:** Database BLOB (LONGBLOB)
- **Version Control:** Git & GitHub
- **Architecture:** MVC-inspired modular structure

## Design Patterns

- **Separation of Concerns** - Processing files separate from display
- **Prepared Statements** - All SQL queries use parameterized queries
- **Session Management** - Secure authentication with session validation
- **Responsive Design** - Mobile-first Bootstrap components
- **Component Reusability** - Shared admin sidebar, styles, and scripts
- **RESTful Endpoints** - Image serving via dedicated endpoint

## Need Help?

1. Look at existing `auth` feature code as reference
2. Ask team members in your group chat
3. Test frequently: `http://localhost/Web_project/`

## Project Timeline

- ✅ Database schema defined and migrated
- ✅ Project structure created
- ✅ Authentication system completed
- ✅ Admin panel fully implemented
  - ✅ Dashboard
  - ✅ User management
  - ✅ Product management with category filtering
  - ✅ Category management
  - ✅ Order management with workflow and date filtering
- ✅ BLOB image storage system implemented
- ✅ Customer frontend (Home, Menu, Cart)
- ✅ Image endpoint for database-served images
- ✅ Responsive design with dark theme
- 🔄 Order tracking for customers (future)

## Current Branch

**Main Development:** `refactor/handle-image` - Image storage system migration

---

**Project Status:** Core features complete. Admin panel fully functional with order workflow management.
