# product-sales_SQL
product sales plateform containing both supplier and customer login page to add a product on the dashbord for customer's purchase.

index.php

Landing page for the sales and stock management portal.
Provides entry points to the customer and supplier login portals with a responsive hero section and portal selection cards.
db.php

Database connection helper.
Initializes a MySQLi connection to the market_db database and handles connection failure.
login.php

Handles login for both customer and supplier users.
Verifies credentials, creates sessions, and redirects authenticated users to the proper dashboard.

registration.php

User registration page for customers and suppliers.
Creates new user accounts, hashes passwords securely, and prevents duplicate email registration.
logout.php

Logs users out by clearing and destroying the session.
Redirects users back to index.php.
customer_dashboard.php

Customer-facing dashboard with profile management, product browsing, and purchase flow.
Updates account details, allows purchases, and adjusts stock plus sales records.
supplier_dashboard.php

Supplier management dashboard.
Lets suppliers update profile info, add new products, and view their own product listings.
stock.php

Live stock and sales tracking page.
Displays all products with supplier info, current inventory status, and completed transaction history.
login.js

Client-side JavaScript for login/registration forms.
Provides password visibility toggling and trims email input before submission.
style.css

Shared styling for the portal.
Defines responsive hero layout, card designs, buttons, and page layout for the public-facing interface.
