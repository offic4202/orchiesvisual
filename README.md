# Orchies Visual - Portfolio Website with Admin Panel

A complete portfolio website for a videographer/content creator with equipment rentals, sales, and e-books management system.

## Features

### Frontend
- Modern portfolio website with hero section
- Portfolio showcase with video lightbox
- About section with skills and stats
- Client logos slider
- Contact form
- Equipment & Resources section (Rentals, Sales, E-Books)

### Admin Panel
- Secure login with session management
- Dashboard with statistics
- Full CRUD operations for products
- Image upload support
- Soft delete functionality
- Search and filter products
- Pagination
- CSRF protection

---

## Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP 8.2
- **Database:** MySQL 8.0
- **Server:** Apache (Docker)
- **Deployment:** Coolify (Docker Compose)

---

## Project Structure

```
orchiesvisual/
├── docker-compose.yml          # Docker configuration
├── index.html                  # Frontend homepage
├── styles.css                  # Frontend styles
├── script.js                   # Frontend JavaScript
├── api/                        # API endpoints
│   ├── products.php            # Get all products (JSON)
│   └── product.php             # Get single product (JSON)
└── admin/                      # Admin panel
    ├── setup.php               # Initial setup (delete after use)
    ├── login.php               # Admin login
    ├── dashboard.php           # Admin dashboard
    ├── index.php               # Admin home
    ├── logout.php              # Logout handler
    ├── assets/
    │   └── css/style.css       # Admin styles
    ├── includes/
    │   ├── db.php              # Database connection
    │   ├── auth.php            # Authentication
    │   └── csrf.php            # CSRF protection
    ├── products/
    │   ├── index.php           # List products
    │   ├── add.php             # Add product
    │   ├── edit.php            # Edit product
    │   └── delete.php          # Delete product (soft)
    └── uploads/
        └── products/           # Uploaded product images
```

---

## Quick Deployment to Coolify

### Step 1: Upload Files

Upload the entire `orchiesvisual` folder to your server or push to Git and connect to Coolify.

### Step 2: Deploy via Coolify

1. Log in to your Coolify dashboard
2. Click **Create New Resource**
3. Select **Docker Compose**
4. Paste the contents of `docker-compose.yml`
5. Click **Deploy**

### Step 3: Run Initial Setup

1. Visit: `http://your-server-ip:8080/admin/setup.php`
2. The setup script will:
   - Create the database
   - Create all tables
   - Create the default admin account
3. **IMPORTANT: Delete `admin/setup.php` after setup!**

### Step 4: Access Admin Panel

| Page | URL |
|------|-----|
| Login | `http://your-server-ip:8080/admin/login.php` |
| Dashboard | `http://your-server-ip:8080/admin/dashboard.php` |
| Products | `http://your-server-ip:8080/admin/products/index.php` |

**Default Admin Credentials:**
- **Username:** `admin`
- **Password:** `admin123`

---

## Docker Compose Configuration

```yaml
version: '3.8'

services:
  app:
    image: php:8.2-apache
    container_name: orchiesvisual
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    environment:
      - DB_HOST=db
      - DB_NAME=orchiesvisual_db
      - DB_USER=root
      - DB_PASSWORD=orchies_secret_2024
    depends_on:
      - db
    command: >
      bash -c "
        docker-php-ext-install pdo pdo_mysql > /dev/null 2>&1 &&
        chown -R www-data:www-data /var/www/html/admin/uploads &&
        chmod -R 755 /var/www/html/admin/uploads &&
        apache2-foreground
      "
    restart: unless-stopped

  db:
    image: mysql:8.0
    container_name: orchiesvisual-db
    environment:
      - MYSQL_ROOT_PASSWORD=orchies_secret_2024
      - MYSQL_DATABASE=orchiesvisual_db
    volumes:
      - mysql_data:/var/lib/mysql
    restart: unless-stopped

volumes:
  mysql_data:
```

---

## Environment Variables

| Variable | Value | Description |
|----------|-------|-------------|
| `DB_HOST` | `db` | MySQL container hostname |
| `DB_NAME` | `orchiesvisual_db` | Database name |
| `DB_USER` | `root` | Database root user |
| `DB_PASSWORD` | `orchies_secret_2024` | Database password |

---

## API Endpoints

### Get All Products

```http
GET /api/products.php
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_name": "Sony A7S III",
      "description": "4K Video Camera",
      "price": "15000.00",
      "category": "rentals",
      "product_image": "abc123.jpg",
      "status": "active",
      "created_at": "2024-01-15 10:30:00"
    }
  ],
  "count": 5
}
```

### Get Single Product

```http
GET /api/product.php?id=1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "product_name": "Sony A7S III",
    "description": "4K Video Camera",
    "price": "15000.00",
    "category": "rentals",
    "product_image": "abc123.jpg",
    "status": "active",
    "created_at": "2024-01-15 10:30:00"
  }
}
```

---

## Database Schema

### Products Table

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    category ENUM('rentals', 'sales', 'ebooks') NOT NULL,
    product_image VARCHAR(255),
    status ENUM('active', 'inactive', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

### Admin Users Table

```sql
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## Frontend Integration

### Fetch Products in JavaScript

```javascript
async function loadProducts() {
    const response = await fetch('/api/products.php');
    const result = await response.json();
    
    if (result.success) {
        console.log('Products:', result.data);
        // Render products to DOM
    }
}
```

### Display Single Product

```javascript
async function loadProduct(id) {
    const response = await fetch(`/api/product.php?id=${id}`);
    const result = await response.json();
    
    if (result.success) {
        console.log('Product:', result.data);
        // Display product details
    }
}
```

---

## Security Features

- **Password Hashing:** Uses `password_hash()` with bcrypt
- **CSRF Protection:** Token-based form validation
- **SQL Injection Prevention:** Prepared statements with PDO
- **Session Management:** 30-minute timeout
- **Login Rate Limiting:** 3 attempts then 15-minute lockout
- **XSS Protection:** `htmlspecialchars()` on output
- **File Upload Validation:** MIME type and size checks

---

## Product Categories

| Category | Description | Price Display |
|----------|-------------|---------------|
| `rentals` | Equipment for rent | ₦XX,XXX/day |
| `sales` | Equipment for sale | ₦XXX,XXX |
| `ebooks` | Digital books | ₦X,XXX or FREE |

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Cannot connect to database | Check Docker logs: `docker-compose logs db` |
| Upload folder not writable | Run: `chmod -R 755 admin/uploads` |
| 403 Forbidden | Check Apache permissions |
| Port already in use | Change port in `docker-compose.yml` |
| Session expired | Log in again |

---

## Changing Default Credentials

1. Log in to admin panel
2. Go to **Users** section (future feature)
3. Or manually update via phpMyAdmin:
   ```sql
   UPDATE admin_users 
   SET password = '$2y$10$new_hashed_password' 
   WHERE username = 'admin';
   ```

---

## Production Checklist

- [ ] Change default database password
- [ ] Delete `admin/setup.php`
- [ ] Enable HTTPS via Coolify
- [ ] Configure domain (optional)
- [ ] Set up regular backups
- [ ] Monitor logs in Coolify dashboard

---

## License

This project is proprietary software.

---

## Support

For issues or questions, contact the developer.

---

## Version

**Version:** 1.0.0  
**Last Updated:** February 2024  
**PHP Version:** 8.2  
**MySQL Version:** 8.0
