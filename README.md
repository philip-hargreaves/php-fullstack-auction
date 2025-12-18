# Fullstack Auction Application

A comprehensive online auction platform built as a coursework project for COMP0178: Database Fundamentals. This application enables users to create, browse, bid on, and manage auctions with features including user authentication, role-based access control, real-time notifications, and collaborative filtering recommendations.

## Features

### Core Functionality

- **User Registration & Authentication**
  - Secure user registration with email and username validation
  - Password hashing using bcrypt
  - Session-based authentication
  - Role-based access control (Buyer, Seller, Admin)

- **Auction Management**
  - Create auctions with item details, pricing, and categories
  - Edit and relist auctions
  - Upload multiple images per auction
  - Automatic auction status management (Scheduled → Active → Finished)

- **Auction Browsing & Discovery**
  - Advanced search with keyword matching
  - Multiple sorting options (ending soonest, price, date)
  - Filtering by category, price range, condition, and status
  - Recursive category filtering (parent categories include all subcategories)
  - Pagination for large result sets

- **Bidding System**
  - Place bids on active auctions
  - View bid history with semi-anonymous bidder names
  - Automatic winner determination when auctions end
  - Reserve price support

- **Additional Features**
  - Watchlist functionality
  - Email and popup notifications (outbid alerts, auction endings, etc.)
  - Messaging system between buyers and sellers
  - Rating and review system
  - Collaborative filtering recommendations
  - Admin dashboard with statistics and user/auction management

## Tech Stack

- **Backend**: PHP 8.4 (vanilla PHP, no frameworks)
- **Database**: MySQL 8.0
- **Frontend**: HTML, CSS, JavaScript (jQuery, Bootstrap)
- **Server**: Apache (via Docker)
- **Containerization**: Docker & Docker Compose

## Project Structure

```
fullstack-auction-app/
├── app/
│   ├── http/
│   │   └── controllers/ # REST controllers
│   ├── models/          # Domain models (Auction, User, Bid, etc.)
│   ├── repositories/    # Data access layer
│   └── services/        # Business logic layer
├── views/               # Presentation layer (PHP views)
├── infrastructure/      # Core infrastructure (Database, Router, DI Container)
├── public/              # Web-accessible files (CSS, JS, images)
├── db/
│   ├── schema.sql       # Database schema
│   └── seed.sql         # Sample data
├── docker-compose.yml   # Docker services configuration
├── Dockerfile           # PHP/Apache container definition
└── routes.php           # Route definitions
```

## Prerequisites

- Docker Desktop (or Docker Engine + Docker Compose)
- Git

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd fullstack-auction-app
```

### 2. Start the Application

```bash
docker-compose up -d
```

This will:
- Build and start the PHP/Apache container on port 8003
- Start the MySQL container on port 3306
- Create a persistent volume for database data

### 3. Initialize the Database

The database schema and seed data need to be imported manually:

```bash
# Connect to MySQL container
docker exec -it fullstack-auction-app-db-1 mysql -u user -ppassword

# Or use a MySQL client to connect to localhost:3306
# Username: user
# Password: password
# Database: auction_db
```

Then run the SQL files in order:

1. `db/schema.sql` - Creates all tables and indexes
2. `db/seed.sql` - Inserts sample data


Alternatively, you can import via command line:

```bash
docker exec -i fullstack-auction-app-db-1 mysql -u user -ppassword auction_db < db/schema.sql
docker exec -i fullstack-auction-app-db-1 mysql -u user -ppassword auction_db < db/seed.sql
```

### 4. Access the Application

Open your browser and navigate to:

```
http://localhost:8003
```

## Database Configuration

The application uses the following database credentials (configured in `docker-compose.yml`):

- **Host**: `db` (container name) or `localhost` (from host)
- **Port**: `3306`
- **Database**: `auction_db`
- **Username**: `user`
- **Password**: `password`

These are automatically detected by the `Database` class via environment variables.

## Test Accounts

After importing `db/user_seed.sql`, you can use these test accounts:

- **Buyer Account**:
  - Username: `john_buyer`
  - Email: `john@example.com`
  - Password: `password123`

- **Seller Account**:
  - Username: `jane_seller`
  - Email: `jane@example.com`
  - Password: `password123`

## Key Functionality Overview

### User Registration & Role Management
- New users can register and automatically receive the "buyer" role
- Buyers can upgrade to "seller" role to create auctions
- Admin users can manage all user accounts and roles

### Auction Creation & Management
- Sellers can create auctions with detailed descriptions, images, and pricing
- Auctions can be edited (with constraints based on bid status)
- Finished auctions can be relisted

### Advanced Search & Filtering
- Search by keywords (item name and optionally description)
- Filter by multiple categories (with recursive subcategory inclusion)
- Filter by price range, item condition, and auction status
- Sort by ending time, price (low-high, high-low), or creation date
- All filters, search, and sorting can be combined

### Bidding & Notifications
- Real-time bid placement with validation
- Email notifications for outbid alerts, auction endings, etc.
- Popup notifications (via AJAX polling)
- Automatic winner determination

### Admin Dashboard
- Website statistics (total users, auctions, revenue, etc.)
- User management (view, activate/deactivate, manage roles)
- Auction management (view, delete)
- Advanced analytics (top categories, most active sellers, etc.)

## Development

### Viewing Logs

```bash
# Web server logs
docker-compose logs web

# Database logs
docker-compose logs db

# All logs
docker-compose logs -f
```

### Stopping the Application

```bash
docker-compose down
```

To also remove volumes (database data):

```bash
docker-compose down -v
```

### Rebuilding Containers

```bash
docker-compose up -d --build
```

## Database Schema

The database is designed in Third Normal Form (3NF) with:

- **16 tables** covering users, auctions, bids, categories, notifications, and more
- **Referential integrity** with foreign key constraints
- **Cascade and SET NULL behaviors** for data consistency
- **Performance indexes** on frequently queried columns
- **Check constraints** for data validation

See `db/schema.sql` for the complete schema definition.

## Coursework Information

This project was developed for **COMP0178: Database Fundamentals (PG – Term 1)** at UCL.

### Design Principles

- **Normalization**: Database schema is in Third Normal Form
- **Repository Pattern**: Separation of data access from business logic
- **Service Layer**: Business logic encapsulated in service classes
- **Dependency Injection**: Loose coupling via DI container
- **MVC Architecture**: Clear separation of concerns

## License

This is a coursework project developed for educational purposes.
