# InfinityFree Deployment Checklist

Use this checklist when deploying your CV website to InfinityFree to ensure you don't miss any important steps.

## Before Deployment

- [ ] Create an InfinityFree account at [infinityfree.net](https://www.infinityfree.net/)
- [ ] Verify your email address
- [ ] Create a free domain or subdomain (e.g., `yourcv.infinityfreeapp.com` or `yourname.rf.gd`)
- [ ] Create a MySQL database in the InfinityFree control panel
- [ ] Note your database credentials:
  - Host: `sql.infinityfree.com`
  - Database name: `epiz_XXXXX_cv_db`
  - Username: `epiz_XXXXX`
  - Password: `your_database_password`

## Configuration Updates

- [ ] Update `config/database.php` with your InfinityFree MySQL credentials (or copy from `database.infinityfree.php`)
- [ ] Update `config/config.php` with your InfinityFree domain (or copy from `config.infinityfree.php`)
- [ ] Check `.htaccess` file for appropriate server configurations
- [ ] Create a backup of your local database
- [ ] Check for any hardcoded local URLs (`http://localhost/cv`) in your code

## Preparing Files

- [ ] Remove unnecessary files (test files, old backups, etc.)
- [ ] Check that all file references are relative or using SITE_URL constant
- [ ] Ensure that upload directories have appropriate permissions
- [ ] Set error reporting to appropriate level for production

## FTP Upload

- [ ] Get FTP credentials from InfinityFree control panel:
  - Server: `ftpupload.net`
  - Username: from control panel
  - Password: your InfinityFree account password
  - Port: 21
- [ ] Connect using an FTP client like FileZilla
- [ ] Upload all website files to the correct directory (usually `/htdocs/`)

## Database Setup

- [ ] Import your SQL backup file through phpMyAdmin in the InfinityFree control panel
- [ ] Test database connection

## Post-Deployment Testing

- [ ] Visit your website at your InfinityFree domain
- [ ] Test the homepage and all navigation links
- [ ] Test the admin login functionality
- [ ] Try adding/editing content through the admin panel
- [ ] Test the contact form
- [ ] Check mobile responsiveness

## Troubleshooting Common Issues

- [ ] Check error logs if available
- [ ] Verify database connection parameters
- [ ] Check file permissions for uploads
- [ ] Ensure all paths are correct for the new server environment

## Security Considerations

- [ ] Change default admin password
- [ ] Make sure sensitive configuration files are protected
- [ ] Run the Security Test Suite at `/admin/test_security.php`
- [ ] Verify CSRF protection is working on all admin forms
- [ ] Check SRI implementation for all resources
- [ ] Update Content Security Policy headers for production domain
- [ ] Create a folder for error logs and ensure it's writable
- [ ] Test error handling functionality
- [ ] Test Font Awesome icon fix
- [ ] Secure the backup script with proper authentication
- [ ] Set up regular backups

## Final Steps

- [ ] Set up domain DNS if using a custom domain
- [ ] Configure email if needed
- [ ] Test all security features one final time
- [ ] Document any server-specific configurations
- [ ] Share your new website URL
- [ ] Schedule regular security checks using provided test tools
- [ ] Plan for regular updates of external libraries
