					Barter DataBase Project -NIKKI And Aditya
		
	-Created a php file called db.php to connect my database (barterdb)
		Sets the PDO error mode to PDO::ERRMODE_EXCEPTION to throw exceptions in case of errors during database operations.
		This file provides the necessary database connection for all other scripts that interact with the database.

	-barterdb.sql - Database Schema and Structure
	 	This file defines the structure of the barterdb database, which includes several tables for users, items, transactions, and other necessary functionalities.
		The main components include:
			Users Table (users)
			Stores user details such as user_id, name, email, password_hash, phone, address, role, status, and created_at.
			The role column differentiates between normal users and admins.
			The status column indicates whether a user is active or suspended.
			
			Items Table (items)
			Stores information about items available for trade, including item_id, user_id (linked to the user who posted the item), name, description, item_type (product or service), quantity, and status.
			The user_id column links each item to a specific user.
			The status column can be either 'available' (item is available for trade) or 'traded' (item has been traded).
			
			Transactions Table (transactions)
			Stores details about transactions between users, such as transaction_id, item_id (linked to the item being traded), user_id (user initiating the trade), partner_id (the partner user), 
			hash_key (unique trade identifier), status, cost_summary (total cost associated with the transaction), and start_date/end_date (transaction duration).
			The status column tracks whether the transaction is active, completed, or cancelled.
			The table has foreign keys linking to the items and users tables.
			
			Equivalence Table (equivalence)
			Stores the equivalence values between different items, allowing users to exchange items with different values.
			The table has columns for item1, item2, and equivalent_value, which represents how much of one item equals another.
			
			Contact Messages Table (contact_messages)
			Stores user-submitted messages on the contact page, including message_id, name, email, message, and the time the message was received (received_at).
			This allows users to send inquiries or feedback through the website.
			
			Alterations and Updates
			Adds a new value column in the items table for item value (used in the equivalence system).
			The role column for users is added or updated to differentiate between user roles (admin and regular users).
			Example query to update user roles, specifically for promoting a user to an admin.
				
	-index.html - Home Page
	This is the main landing page for your BarterDB website. It typically includes:

		A welcoming message or introduction to the barter system.

		Navigation links to other pages like About, Services, Contact, Sign Up, and Sign In.

		Display of key information like featured items or services available for trade.

	-about.html - About Page
	This page provides information about the BarterDB platform, explaining its mission, how it works, and the benefits of using the system. It could include:

		A brief history or background of the project.
		An explanation of how the barter system works (e.g., how users can trade items securely).
		Details about the security measures in place (e.g., anonymous trading, hash key system).

	-services.html - Services Page
	This page outlines the services provided by BarterDB. It can include:

		How the equivalence values work in the system.
		Information about the secure exchange process, including how the hash key system works.

	-contact.html - Contact Page (Frontend)
	This page provides a contact form for users to send messages or inquiries. It typically includes:

		A form with fields for the user's name, email, and message.
		Instructions for users to reach out for support, feedback, or general inquiries.
		A submit button to send the contact message to the backend script (contact.php).

	-contact.php - Contact Form Processing (Backend)
	This PHP file processes the contact form submitted on contact.html. It typically:

		Retrieves the form data (name, email, and message).
		Validates the form data (e.g., ensuring that the email is valid and the message is not empty).
		Stores the submitted message in the database (usually in the contact_messages table).
		
	-signin.html - Sign In Page (Frontend)
	This page allows users to sign in to their BarterDB accounts. It includes:

		A form with fields for the user's name, email and password.
		A submit button for submitting the login credentials.
		A link to the registration page (signup.html) if the user doesn’t have an account.

	-login.php - Login Script (Backend)
	This PHP file processes the sign-in form submitted on signin.html. It typically:

		Retrieves the email and password from the form.
		Validates the credentials by comparing them with the database (checking the hashed password).
		If the login is successful, the user is redirected to their dashboard or home page.
		If the login fails, an error message is displayed prompting the user to try again.

	-signup.html - Sign Up Page (Frontend)
	This page allows new users to register for BarterDB. It includes:

		A form with fields for the user's name, email, phone, password, and address.
		A submit button for submitting the registration details.
		A link to the sign-in page (signin.html) for users who already have an account.

	-signup.php - Sign Up Script (Backend)
	This PHP file processes the sign-up form submitted on signup.html. It typically:

		Retrieves the form data (name, email, password, etc.).
		Validates the data (e.g., checking if the email is unique, ensuring that required fields are not empty).
		Hashes the password before storing it in the database for security.
		Adds the new user’s information into the users table in the database.
		Redirects the user to the login page or their dashboard after successful registration.

	-registration.php - Alternative Registration Handling (Backend)
	This file could be an alternative method for handling the registration process, depending on the project setup. It may:

		Include more complex registration features (e.g., assigning a default role, sending a verification email).
		Perform additional checks like confirming that the user is not already registered.

	-user_dashboard.php file is designed to display and manage various aspects of a user's activity on the platform. Here’s a breakdown of its features and functionalities:

		User Authentication and Authorization:
			The page starts by checking if the user is logged in and has a "user" role. If not, they are redirected to the sign-in page.
	
		Displayed Information:

			Posted Items:
			Displays all items posted by the logged-in user that are marked as 'available'. Each item is shown with its name, description, quantity, and value.
			Users can initiate a trade by clicking a button next to each item, which leads to the match_items.php script to start the exchange process.
		
			All Available Items:
			Displays items posted by other users that are available for trade. These are listed with their name, description, quantity, value, and the user ID of the poster.
			
			Active Transactions:
			Displays ongoing trades in which the user is involved, either as the primary or partner user. This section includes the item name, trade status, partner ID, item value, and hash key for secure identification. 
			For transactions where the user is the partner, options to accept or decline the trade are available.
			
			Completed Transactions:
			Shows completed transactions that the user was involved in, with item name, trade status, partner ID, and hash key for each trade.
			
			Actions:
			Initiating a Trade: Users can initiate a trade for their posted items by clicking the "Initiate Trade" button.
		
			Accepting/Declining Active Trades: Users who are partners in a trade can accept or decline the transaction via buttons on the dashboard.
		
			Link to Post New Item:
			A link to post_item.php allows users to add new items to the platform.
		
		Overall, the user dashboard is designed to allow users to view and manage their items, transactions, and interactions with other users in the barter system.

	-admin_dashboard.php
	The Admin Dashboard allows administrators to manage users and transactions within the BarterDB system. The functionalities presented in this file include:

		User Management:
	
			View Users: Displays a table with all users, including their details such as user ID, name, email, phone, role, and status.
		
			Actions:
				Suspend: Admins can suspend users by changing their status.
				Delete User: Admins have the ability to delete users from the system.
		
			Each user is presented with a button to either suspend and delete them, which triggers a form submission to admin_actions.php.
		
		Transaction Management:

			View Transactions: Shows a table of transactions, including item name, user and partner names, hash key, status, and available actions.
		
			Actions:
				Mark as Completed: Admin can mark a transaction as completed.
				Cancel Transaction: Admin can cancel an active transaction.
				Each transaction has action buttons for the admin to control its status (active, completed, or canceled), which also submit to admin_actions.php.

	-admin_actions.php file is responsible for managing administrative actions on the system. It ensures that only logged-in users with an "admin" role can perform actions 
	such as suspending, deleting users, or managing transaction statuses.

		Key Functionalities:
			
			Admin Role Verification:
			It starts by checking if the user is logged in and has admin privileges (via session data). 
			If the user does not have admin rights, they are redirected to the login page.
			
			User Management:
				Suspending a User: By changing the user's status to 'suspended' in the database.
				Deleting a User: By removing the user from the users table entirely.
			
			Transaction Management:
				Completed: Updates the transaction status to 'completed'.
				Canceled: Changes the transaction status to 'canceled'.
			
			Form for Admin Actions:
				A form is provided to submit the user ID and the desired action (suspend or delete). 
				The admin can also submit a transaction ID along with the desired action (complete or cancel).

			Session Message:
				After executing an action, a session message is set to notify the admin of the outcome
			
			Redirection:
				After the action is completed, the admin is redirected back to the admin dashboard to maintain a smooth user experience.

	-post_item.php file is designed for both admins and users in the BarterDB system, with specific functionalities:

		Admin Privileges: The file ensures that only admins can perform actions such as suspending or deleting users, as well as 
		completing or canceling transactions. It validates that the logged-in user is an admin before allowing access to these features.

		User Item Posting: For regular users, the file allows them to post new items for trade. Users can input details like the item name, description, value, and quantity. 
		This data is then inserted into the items table in the database with a default status of 'available'.

		Form Validation: Basic validation checks are performed to ensure that all fields (name, description, value, and quantity) are filled out before submitting the form.

		Transaction Handling: Admins can manage transactions (complete or cancel) via the provided form. Upon completion or cancellation, the transaction status is updated in the database.

		User Interface: The form provides a user-friendly interface for posting items, including fields for name, description, value, and quantity. After posting an item, users are redirected to the user dashboard.		

	
	-handle_transaction.php - Transaction Handler for User Dashboard
		
		Purpose: Manages the acceptance or decline of trade transactions on the user dashboard.

		Key Functionalities:
			Session Check: Verifies that the user is logged in. Redirects to sign-in if not.
			
			Transaction Retrieval: Fetches transaction details using transaction_id.
			
			Authorization Check: Ensures the logged-in user is the transaction's partner.
			
			Accept Transaction: Updates the transaction to "completed" and marks items as "traded".
			
			Decline Transaction: Resets item status to "available" and deletes the transaction.
			
			Redirect: After processing, redirects back to the user dashboard.

		This file handles transaction actions, ensuring items and transaction statuses are correctly updated based on user input.
	
	-trade.php - User Trade Functionality

		Purpose: Allows users to initiate a trade by offering one item and requesting another from a partner.

		Key Functionalities:
			Session Management: Uses the current user's session to fetch user_id.

			Form Submission: Takes item_id1 (offered item), item_id2 (requested item), and partner_id (trade partner).
			
			Validation:
				Checks if the partner exists in the database.
				Verifies that both items are available.

			Transaction Recording: If valid, generates a unique 16-character hash and records the trade in the transactions table.

			Confirmation: Displays a success message with the trade's hash key.
	
		This file facilitates item exchanges between users by validating and recording trades, generating a unique trade identifier.
	
	-match_items.php file allows users to initiate a trade by selecting items to exchange with another active user. Key functionalities include:

		User Authentication: Redirects to login if the user is not logged in.
		
		Item and Partner Selection: Users can select their item (item_a) and a partner's item (item_b) for trade.
		
		Validation: Ensures both items are available and have matching values.
		
		Transaction Creation: Creates a transaction with a unique hash key if the items are valid.
		
		Item Status Update: Updates item status to "traded" after a successful transaction.

		Dynamic Partner Item Loading: Uses JavaScript to load the selected partner's items.

	-get_partner_items.php file 
		
		Retrieves available items for trade from a specified partner. 
		
		It accepts a partner_id via URL, queries the database for items with the status "available," and returns them in JSON format. 
		
		This data is used by the frontend to populate a dropdown menu with the partner's items for selection, allowing users to choose items for trade. 
		
		If no partner_id is provided, an empty array is returned.

	-view_items.php - User Dashboard: View Available Items
	
		This file displays a list of all available items for trade, fetched from the database. 
		
		Each item shows its name, description, and quantity. Users can initiate a trade by entering the partner's user ID and submitting a form, which triggers the trade process through match_items.php. 
		
		It allows users to view items and start trades with others.

	-public folder for styles, images, javascript :
		style.css, styles.css, bootstrap and js
