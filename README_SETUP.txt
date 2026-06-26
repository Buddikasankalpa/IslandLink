ISDN / IslandLink Setup Instructions

1. Extract the ZIP file.
2. Copy the IslandLink folder into C:\xampp\htdocs\
3. Start Apache and MySQL from XAMPP.
4. Open phpMyAdmin.
5. Create a database named isdn if needed, or simply import the included isdn.sql file.
6. Import IslandLink/isdn.sql
7. Open http://localhost/IslandLink/

Demo accounts:
Admin: admin@gmail.com / 123456
RDC: rdc@gmail.com / 123456
Customer: customer@gmail.com / 123456
Head Office: homanager@gmail.com / 123456
Logistics Officer: logisticsofficer@gmail.com / 123456
Driver: driver@gmail.com / 123456

New inventory management added:
- RDC dashboard shows product details and current stock count.
- RDC can add stock product by product.
- Customer orders reduce stock automatically from Central RDC inventory.
- Shop page shows available stock and blocks out-of-stock items.


Admin can now add products with an initial stock value. New products automatically appear in the RDC inventory page.


Inventory note:
- Admin can create products only.
- RDC updates stock quantities from the RDC dashboard.
- New products start with 0 stock until the RDC adds inventory.
- Customer orders automatically reduce RDC stock.


Updated roles:
- Admin dashboard is now focused on report generation and KPI monitoring.
- RDC dashboard now handles Product Management, Order Management, User Management, and Inventory updates.
- Admin reports available in admin_reports.php for Sales Performance, Stock Turnover, and Delivery Efficiency.
