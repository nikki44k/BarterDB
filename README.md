# Barter Database Project

This repository contains the source code and database schema for the BarterDB project, built with PHP, MySQL, HTML/CSS, and JavaScript. BarterDB is a secure, anonymous platform for users to exchange items based on equivalence values and hash-based trade identifiers.

---

## Table of Contents

- [Database](#database)  
  - [db.php](#dbphp)  
  - [barterdb.sql](#barterdbsql)  
- [Front-End Pages](#front-end-pages)  
  - [index.html](#indexhtml)  
  - [about.html](#abouthtml)  
  - [services.html](#serviceshtml)  
  - [contact.html](#contacthtml)  
  - [signin.html](#signinhtml)  
  - [signup.html](#signuphtml)  
- [Back-End Scripts](#back-end-scripts)  
  - [contact.php](#contactphp)  
  - [login.php](#loginphp)  
  - [signup.php](#signupphp)  
  - [registration.php](#registrationphp)  
  - [user_dashboard.php](#user_dashboardphp)  
  - [admin_dashboard.php](#admin_dashboardphp)  
  - [admin_actions.php](#admin_actionsp hp)  
  - [post_item.php](#post_itemphp)  
  - [handle_transaction.php](#handle_transactionphp)  
  - [trade.php](#tradephp)  
  - [match_items.php](#match_itemsphp)  
  - [get_partner_items.php](#get_partner_itemsphp)  
  - [view_items.php](#view_itemsphp)  
- [Public Assets](#public-assets)  
- [Getting Started](#getting-started)  
  - [Set Up the Database](#set-up-the-database)  
  - [Configure `db.php`](#configure-dbphp)  
  - [Run a Local Server](#run-a-local-server)  
  - [Register and Use the Application](#register-and-use-the-application)  
- [License](#license)  

---

## Database

### `db.php`
- Establishes a PDO connection to the `barterdb` database.
- Sets `PDO::ERRMODE_EXCEPTION` to throw exceptions on errors.
- Provides the database connection object for all other scripts.

### `barterdb.sql`
Defines the schema and initial data for BarterDB:
- **Users Table (`users`)**  
  - Columns: `user_id`, `name`, `email`, `password_hash`, `phone`, `address`, `role`, `status`, `created_at`.  
  - `role` differentiates between normal users and admins.  
  - `status` indicates whether a user is `active` or `suspended`.

- **Items Table (`items`)**  
  - Columns: `item_id`, `user_id` (FK), `name`, `description`, `item_type` (`product` or `service`), `quantity`, `value`, `status`.  
  - `user_id` links each item to its owner.  
  - `status` can be `available` or `traded`.  
  - `value` is used in the equivalence system.

- **Transactions Table (`transactions`)**  
  - Columns: `transaction_id`, `item_id` (FK), `user_id` (initiator, FK), `partner_id` (FK), `hash_key`, `status`, `cost_summary`, `start_date`, `end_date`.  
  - Tracks ongoing and completed trades.  
  - `status` can be `active`, `completed`, or `canceled`.

- **Equivalence Table (`equivalence`)**  
  - Columns: `id`, `item1`, `item2`, `equivalent_value`.  
  - Defines how much of one item equals another.

- **Contact Messages Table (`contact_messages`)**  
  - Columns: `message_id`, `name`, `email`, `message`, `received_at`.  
  - Stores messages submitted via the contact form.

- **Schema Updates**  
  - Adds a `value` column to `items` for item equivalence.  
  - Updates `role` column in `users` to differentiate admins.  
  - Includes example SQL for promoting a user to admin.

---

## Front-End Pages

### `index.html`
- Main landing page.  
- Features:  
  - Welcome message.  
  - Navigation links to About, Services, Contact, Sign Up, and Sign In.  
  - Optional: featured items or services.

### `about.html`
- Project overview and background.  
- Explains:  
  - Mission of BarterDB.  
  - How the barter system works (anonymous trading, hash key security).  
  - Security measures.

### `services.html`
- Outlines BarterDB services.  
- Describes:  
  - Equivalence value calculations.  
  - Secure exchange process using hash keys.

### `contact.html`
- Contact form for user inquiries.  
- Fields:  
  - Name  
  - Email  
  - Message  
- Form submission triggers `contact.php`.

### `signin.html`
- User login page.  
- Fields:  
  - Email  
  - Password  
- Links to `signup.html` for new users.

### `signup.html`
- User registration page.  
- Fields:  
  - Name  
  - Email  
  - Phone  
  - Password  
  - Address  
- Links to `signin.html` for existing users.

---

## Back-End Scripts

### `contact.php`
- Processes contact form submissions from `contact.html`.  
- Steps:  
  1. Retrieve `name`, `email`, and `message` from `$_POST`.  
  2. Validate inputs (e.g., non-empty, valid email).  
  3. Insert message into `contact_messages` table.  
  4. Redirect or show confirmation.

### `login.php`
- Processes login requests from `signin.html`.  
- Steps:  
  1. Retrieve `email` and `password` from `$_POST`.  
  2. Validate credentials by comparing hashed password against `users` table.  
  3. On success: set session variables and redirect to dashboard.  
  4. On failure: display error and prompt to retry.

### `signup.php`
- Handles user registration from `signup.html`.  
- Steps:  
  1. Retrieve `name`, `email`, `phone`, `password`, and `address`.  
  2. Validate inputs (unique email, required fields).  
  3. Hash the password before storage.  
  4. Insert new user into `users` table with default role `user`.  
  5. Redirect to `signin.html` or user dashboard.

### `registration.php`
- Alternative or extended registration handler.  
- May include:  
  - Assigning default roles.  
  - Sending verification emails.  
  - Additional validation checks.

### `user_dashboard.php`
- Displays and manages a user’s account activity.  
- Features:  
  - **Posted Items:**  
    - Lists all `available` items posted by the logged-in user (`item_id`, `name`, `description`, `quantity`, `value`).  
    - “Initiate Trade” button next to each item → calls `match_items.php`.  
  - **All Available Items:**  
    - Shows items from other users (`name`, `description`, `quantity`, `value`, `owner_id`).  
  - **Active Transactions:**  
    - Lists trades where the user is initiator or partner (`item_name`, `status`, `partner_id`, `value`, `hash_key`).  
    - If user is partner, provides “Accept” or “Decline” buttons → calls `handle_transaction.php`.  
  - **Completed Transactions:**  
    - Shows all completed trades involving the user.  
  - **Actions:**  
    - Link to `post_item.php` to add a new item.

### `admin_dashboard.php`
- Admin control panel for managing users and transactions.  
- **User Management:**  
  - Table of all users (`user_id`, `name`, `email`, `phone`, `role`, `status`).  
  - “Suspend” or “Delete” buttons → submit to `admin_actions.php`.  
- **Transaction Management:**  
  - Table of all transactions (`transaction_id`, `item_name`, `initiator_name`, `partner_name`, `hash_key`, `status`).  
  - “Mark as Completed” or “Cancel” buttons → submit to `admin_actions.php`.  

### `admin_actions.php`
- Handles admin actions triggered from `admin_dashboard.php`.  
- Validates that the current user has `admin` privileges.  
- **User Management:**  
  - **Suspend:** Update `users.status` to `suspended`.  
  - **Delete:** Remove user from `users` table (cascade deletes related items/transactions).  
- **Transaction Management:**  
  - **Complete:** Update `transactions.status` to `completed`.  
  - **Cancel:** Update `transactions.status` to `canceled`.  
- Sets a session message indicating action outcome, then redirects back to `admin_dashboard.php`.

### `post_item.php`
- Allows users to post new items for trade; also used by admins for transaction management.  
- **For Regular Users:**  
  - Displays a form (`name`, `description`, `value`, `quantity`).  
  - Validates inputs (non-empty fields).  
  - Inserts new item into `items` table with status `available`.  
  - Redirects to `user_dashboard.php`.  
- **For Admins:**  
  - Provides additional controls to complete or cancel transactions via form submissions.

### `handle_transaction.php`
- Manages accepting or declining trades on `user_dashboard.php`.  
- **Session Check:** Redirect to login if user is not authenticated.  
- Fetches the `transaction_id` from `$_POST`.  
- Validates the logged-in user is the `partner` in that transaction.  
- **Accept:**  
  - Update `transactions.status` to `completed`.  
  - Set both involved `items.status` to `traded`.  
- **Decline:**  
  - Update initiator’s item status to `available`.  
  - Delete the transaction record.  
- Redirect back to `user_dashboard.php`.

### `trade.php`
- Facilitates trade initiation: user selects one of their own items and a partner’s item.  
- Steps:  
  1. Check session for logged-in user.  
  2. Retrieve `item_id1` (offered item), `item_id2` (requested item), `partner_id` from `$_POST`.  
  3. Validate that both items exist, are `available`, and have matching `value`.  
  4. Generate a unique 16-character `hash_key`.  
  5. Insert new transaction into `transactions` table with status `active`.  
  6. Update both items’ statuses to `traded`.  
  7. Display confirmation with `hash_key`.

### `match_items.php`
- Front-end for selecting items to exchange.  
- **User Authentication:** Redirect if not logged in.  
- **Selection Interface:**  
  - Dropdown for user’s own `available` items.  
  - Dropdown for selecting a partner user.  
  - On partner selection, AJAX → calls `get_partner_items.php` to populate partner’s `available` items.  
  - Submit form posts to `trade.php` to create the transaction.  
- Validates matching `value` before submission.

### `get_partner_items.php`
- Returns JSON list of available items for a specified `partner_id`.  
- Accepts `partner_id` via `$_GET`.  
- Queries `items` table for rows where `user_id = partner_id` and `status = 'available'`.  
- Outputs JSON array: `[ { "item_id": ..., "name": ... }, … ]`.  
- Front-end JS uses this data to populate the partner items dropdown.

### `view_items.php`
- Displays all available items from all users.  
- Fetches items where `status = 'available'`.  
- Shows `item_id`, `name`, `description`, `quantity`, `value`.  
- Provides a form to enter a `partner_id` and select an item for trade → posts to `match_items.php`.

---

## Public Assets

All front-end assets are located in the `public/` folder:

- **CSS**  
  - `style.css`  
  - `styles.css`  
  - Bootstrap (minified CSS)

- **JavaScript**  
  - Bootstrap JS (minified)  
  - Custom JS (e.g., AJAX for `get_partner_items.php`)

- **Images**  
  - Any logos or icons used across pages

---

## Getting Started

### Set Up the Database

1. **Create a MySQL database** named `barterdb`.  
2. **Import `barterdb.sql`:**  
   ```bash
   mysql -u your_user -p barterdb < barterdb.sql
**Configure db.php**
Update the PDO connection parameters (host, dbname, username, password) to match your local environment.

## Run a Local Server
If using XAMPP or a similar local server package, place this project folder in htdocs (for XAMPP) or the appropriate web root directory.
Start Apache and MySQL services.
Open your browser and navigate to:
	http://localhost/BarterDB/index.html

## Register and Use the Application
Navigate to Sign Up (signup.html) to create a new user or admin account.
After signing in via Sign In (signin.html), you will be directed to your dashboard.
User Dashboard: Post items, view available items, initiate or manage trades.
Admin Dashboard: Manage users (suspend/delete) and transactions (complete/cancel).

## License
This project is released under the MIT License.
