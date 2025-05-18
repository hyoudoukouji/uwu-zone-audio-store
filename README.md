
Built by https://www.blackbox.ai

---

# UwU Zone - Audio Equipment Store

## Project Overview
UwU Zone is a web-based audio equipment store that allows users to explore, save, and purchase various audio products. The application features user authentication, a product catalog, a wishlist, and a responsive design using Tailwind CSS. 

## Installation
To set up the UwU Zone project on your local machine, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd uwu-zone
   ```

2. **Set up the database:**
   - Make sure you have MySQL installed and running.
   - Import the `database/uwu_zone.sql` schema file into your MySQL database to create the necessary tables and structure. You can use the provided `setup_database.php` or do it manually.
   - Edit the database configuration settings in `config/database.php` as needed.

3. **Run the setup script:**
   ```bash
   php setup_database.php
   ```

4. **Install dependencies:**
   Make sure you have Composer installed, and then install the necessary PHP packages:
   ```bash
   composer install
   ```

## Usage
- Start a local server using your preferred method (e.g., built-in PHP server, XAMPP, etc.).
- Access the application by navigating to `http://localhost/path-to-your-project/index.php` in your web browser.
- Register for an account to start shopping or log in with existing credentials.

## Features
- **User Authentication:** Users can register, log in, and manage their profiles.
- **Product Browsing:** Users can explore products, filter by price, and sort according to various criteria.
- **Wishlist Functionality:** Users can save favorite products to their wishlist for later purchase.
- **Responsive Design:** The application has a mobile-friendly layout powered by Tailwind CSS.
- **Integration with Font Awesome:** Use of icons for better visual representation of actions.

## Dependencies
The following libraries and frameworks are used in this project:
- [Tailwind CSS](https://tailwindcss.com/) – For styling.
- [Font Awesome](https://fontawesome.com/) – For icons.

Make sure to include the library links in your HTML files as shown in `index.html`.

## Project Structure
Here's a quick overview of the project's folder structure:

```
/uwu-zone
│
├── index.php               # Main entry point of the application
├── index.html              # HTML template for the store
├── login.php               # User login page
├── register.php            # User registration page
├── profile.php             # User profile management page
├── settings.php            # User settings, including password management
├── explore.php             # Product exploration page with filtering capabilities
├── product.php             # Individual product detail page
├── saved.php               # User's wishlist page
├── logout.php              # Logout functionality
├── setup_database.php      # Database setup script
│
├── config                  # Configuration files for database and authentication
│   ├── database.php        # Database connection settings
│   └── auth.php            # Authentication logic
│
├── partials                # Common partial templates
│   ├── auth-modal-new.php  # Authentication modal
│   └── layout.php          # HTML layout template
│
├── assets                  # Static assets like CSS and JS files
│   ├── css
│   └── js
│
└── database                # SQL file for setting up the database structure
    └── uwu_zone.sql       # SQL schema
```

## Contributing
Contributions are welcome! Please feel free to submit a Pull Request or open an Issue for any bugs or feature requests.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.