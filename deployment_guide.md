# Deploying Your CV Website to InfinityFree

This guide will walk you through the steps to deploy your CV website to InfinityFree hosting with a free domain.

## Step 1: Create an InfinityFree Account

1. Go to [InfinityFree](https://www.infinityfree.net/) and click "Sign Up" to create a free account.
2. Verify your email address by clicking the link in the verification email.

## Step 2: Set Up Your Free Domain

1. Log in to your InfinityFree account.
2. Click on "New Free Domain" or "Free Subdomain".
3. Choose between:
   - A subdomain of a free domain (like `yourcv.infinityfreeapp.com`)
   - A free domain from their partners (like `yourname.rf.gd`, `yourname.epizy.com`, etc.)
4. Complete the domain registration process.

## Step 3: Prepare Your Database

1. In your InfinityFree control panel, navigate to "MySQL Databases".
2. Click "Create a New Database" and note your:

   - Database name (usually starts with `epiz_` or similar prefix)
   - Database username
   - Database password
   - Database hostname (usually `sql.infinityfree.com`)

3. Export your local database:
   - In phpMyAdmin (from your XAMPP installation), select your `cv_db` database
   - Click "Export" and choose "Quick" export method with SQL format
   - Download the SQL file

## Step 4: Update Your Configuration Files

Before uploading, update your configuration files for InfinityFree:

1. Update `config/config.php`:

   - Change `SITE_URL` to your InfinityFree domain
   - Update any other configuration settings if needed

2. Update `config/database.php`:
   - Update database credentials with your InfinityFree MySQL details

## Step 5: Upload Your Files

Using an FTP client (like FileZilla):

1. Get your FTP credentials from the InfinityFree control panel:

   - Host: `ftpupload.net`
   - Username: Found in your control panel
   - Password: Your InfinityFree account password
   - Port: 21

2. Connect to the FTP server.

3. Navigate to the correct directory:

   - For the main domain: `/htdocs/`
   - For a subdomain: `/htdocs/subdomain/`

4. Upload all your website files to this directory.

## Step 6: Import Your Database

1. In your InfinityFree control panel, go to "MySQL Databases" and click on "phpMyAdmin".
2. Select your database from the left sidebar.
3. Click on the "Import" tab.
4. Choose the SQL file you exported earlier and click "Go".

## Step 7: Security Checks for Production

Before finalizing your deployment, run these important security checks:

1. Run the Security Test Suite at `/admin/test_security.php` to verify all security measures are working.
2. Update SRI hashes for production using the `generate_sri.php` script.
3. Make sure CSRF protection is enabled on all admin forms.
4. Update the Content Security Policy for your production domain.
5. Set proper file permissions:

   - Set directories to 755 (drwxr-xr-x)
   - Set files to 644 (rw-r--r--)
   - Set sensitive config files to 600 (rw-------)

6. Ensure error logging is properly configured:
   - Create a `/logs` directory and make it writable
   - Test error logging functionality

## Step 8: Test Your Website

1. Visit your newly deployed website at your InfinityFree domain.
2. Test all functionality, including:
   - Navigation
   - Admin login
   - Content display
   - Contact form
   - File uploads
   - Security features (CSRF, SRI)

## Troubleshooting

- **500 Internal Server Error**: Check your config files for incorrect paths or PHP version compatibility issues.
- **Database Connection Errors**: Verify your database credentials in `config/database.php`.
- **Missing Images**: Make sure your upload directory is correctly set and has proper permissions.
- **PHP Error Messages**: Check the error log in your InfinityFree control panel.

## Limitations of InfinityFree Free Hosting

- 5GB disk space
- Limited bandwidth
- No SSH access
- Limited CPU resources
- 50MB maximum database size
- 10MB maximum file upload size
- No support for some PHP extensions

If you encounter limitations, consider upgrading to their premium hosting or other affordable hosting providers.

## Maintenance

1. Regularly backup your website and database from the InfinityFree control panel or via FTP and phpMyAdmin to prevent data loss.
2. Run periodic security checks using the provided test tools:
   - Complete Security Test Suite (`/admin/test_security.php`)
   - CSRF Protection Test (`/admin/test_csrf.php`)
   - SRI Implementation Test (`/admin/test_sri.php`)
3. Monitor error logs for unusual activities or recurring issues.
4. Update external libraries (Bootstrap, Font Awesome, etc.) when new versions are released.
5. Regenerate SRI hashes after updating any assets using `generate_sri.php`.

## Security Documentation

For a comprehensive overview of the security features implemented in this website, please refer to the [Security and Error Handling Documentation](/admin/documentation.php) available in the admin area.
