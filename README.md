# My Store - College Web Project

A feature-based e-commerce web application built with HTML, CSS, Bootstrap, PHP, and MySQL.

## Project Structure

```
Web_project/
├── index.php                    # Homepage with feature navigation
├── config/
│   ├── db.php                   # Database connection (gitignored - create locally)
│   └── db.example.php           # Database config template
├── database/
│   └── my_store.sql            # Main database schema
├── modules/
│   ├── auth/                    # ✅ Authentication feature (EXAMPLE)
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── logout.php
│   │   ├── process_login.php
│   │   ├── process_register.php
│   │   └── README.md           # Feature development guide
│   ├── products/                # 🔄 To Do
│   ├── cart/                    # 🔄 To Do
│   ├── orders/                  # 🔄 To Do
│   ├── categories/              # 🔄 To Do
│   └── admin/                   # 🔄 To Do
└── .gitignore
```

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

Open in browser: `http://localhost/bfcai_web_team/`

## Working on Features (Team Members)

### Feature-Based Development

Each team member works on ONE complete feature in isolation to minimize merge conflicts.

### Step-by-Step Guide

1. **Pick a feature** from the list:
   - Products (browse, view details, search)
   - Shopping Cart (add/remove items, update quantities)
   - Orders (place order, view history, track status)
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
- `categories` - Product categories
- `items` - Products/items for sale
- `orders` - Customer orders
- `order_items` - Items in each order

See `database/my_store.sql` for complete schema.

## Test Users

```
Email: alice@example.com
Password: pass123

Email: admin@example.com
Password: adminpass (role: admin)
```

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

- **Frontend:** HTML5, CSS3, Bootstrap 5.3
- **Backend:** PHP 8.0
- **Database:** MySQL (MariaDB 10.4)
- **Server:** Apache (XAMPP)
- **Version Control:** Git & GitHub

## Need Help?

1. Look at existing `auth` feature code as reference
2. Ask team members in your group chat
3. Test frequently: `http://localhost/Web_project/`

## Project Timeline

- ✅ Database schema defined
- ✅ Project structure created
- ✅ Example feature (auth) completed
- 🔄 Team members implement remaining features
- 🔄 Integration and testing
- 🔄 Final presentation

---

**Remember:** Each person builds ONE complete feature (frontend + backend). This keeps work independent and reduces conflicts!
