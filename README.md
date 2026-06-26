# IslandLink

IslandLink is a web-based inventory, ordering, and logistics management system designed for a fresh produce supply chain. It provides role-based dashboards for administrators, regional distribution centers (RDCs), head office staff, logistics officers, drivers, and customers.

## Features
- Product and inventory management
- Stock updates from RDC operations
- Customer order placement and order tracking
- Delivery and route management
- Role-based dashboards for different users
- Sales, stock, and delivery reporting

## Technology Stack
- PHP
- MySQL
- Bootstrap and jQuery
- XAMPP for local development

## Setup Instructions
1. Place the project folder in your XAMPP web root, usually `C:\xampp\htdocs\IslandLink`.
2. Start Apache and MySQL from XAMPP.
3. Open phpMyAdmin and create/import the database named `isdn`.
4. Import the SQL file from [isdn.sql](isdn.sql).
5. Open the application in your browser at `http://localhost/IslandLink/`.

## Default Login Accounts
- Admin: `admin@gmail.com` / `123456`
- RDC: `rdc@gmail.com` / `123456`
- Customer: `customer@gmail.com` / `123456`
- Head Office: `homanager@gmail.com` / `123456`
- Logistics Officer: `logisticsofficer@gmail.com` / `123456`
- Driver: `driver@gmail.com` / `123456`

## Database Configuration
The application connects to the MySQL database using the settings in [db.php](db.php). By default, it uses:
- Host: `localhost`
- Username: `root`
- Password: empty
- Database: `isdn`

## License
This project is distributed under the terms of the included license files.
