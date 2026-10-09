# PersianVid - پلتفرم اشتراک ویدیو ایرانی

A complete Persian video-sharing platform inspired by modern video platforms, built from scratch using PHP 8.2+ and MySQL.

## Features Implemented
- **Public & Private Video System**: Videos can be set to public or private. Private videos are protected by strict server-side authorization and only accessible to the owner and accepted friends.
- **Video Upload & Processing**: Secure video upload supporting MP4 and WebM. File extension, MIME type, and executable checks are strictly enforced. FFmpeg is used to automatically generate thumbnails upon upload.
- **Social Features**: Friend requests (send/accept/remove), comments on videos, and likes/dislikes system.
- **Custom MVC Framework**: Lightweight architecture with customized `Router`, `Controller`, `Database` (PDO), `Session`, and `Security` (CSRF token, XSS prevention) classes.
- **Modern Dark UI/UX**: Clean, responsive layout utilizing CSS3 grid, default RTL direction, and the Vazirmatn font.
- **Admin CMS**: Protected dashboard allowing administrators to toggle user statuses (ban/suspend) and moderate (approve/remove) videos.

## Architecture
The application uses a custom Model-View-Controller (MVC) architecture to cleanly separate concerns without relying on external frameworks:
- `app/Core/`: Base framework classes handling routing, database connection, secure sessions, and autoloader.
- `app/Controllers/`: Application logic mapping routes to models and views.
- `app/Models/`: Database abstraction for Users, Videos, and Social entities utilizing PDO prepared statements for SQL injection prevention.
- `app/Views/`: Native PHP templates acting as the frontend, utilizing `htmlspecialchars()` for XSS mitigation.
- `public/`: The single entry point (`index.php`) and static assets (CSS, JS).
- `storage/`: Separate paths for public and private videos outside the public web root for protected streaming.

## Installation Instructions (Local/Windows/XAMPP)

1. **Requirements**:
   - Apache
   - PHP 8.2 or newer (with PDO_MySQL enabled)
   - MySQL (Do not substitute with MariaDB/SQLite)
   - FFmpeg installed and added to the system PATH.

2. **Clone and Place Project**:
   Place the project folder in your XAMPP `htdocs` directory (e.g. `C:\xampp\htdocs\PersianVid`).

3. **Database Configuration**:
   - Start MySQL.
   - Run the provided schema file to construct the tables and insert default values:
     ```bash
     mysql -u root < database/schema.sql
     ```
   - Make sure to create a secure MySQL user (`vid_user` with password `vid_password`) and grant privileges to the `vid_platform` database, or modify `config/config.php` to match your root credentials.

4. **Apache Configuration**:
   The `public/.htaccess` file handles URL rewriting. Ensure `mod_rewrite` is enabled in your Apache `httpd.conf`:
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

5. **FFmpeg Setup**:
   Download FFmpeg for Windows from the official site. Extract it, and add the `bin` folder path (e.g., `C:\ffmpeg\bin`) to your System Environment Variables under `PATH`. Restart Apache to ensure PHP picks up the new PATH.

6. **First Administrator**:
   After installing the schema and starting the server, visit `/setup` (e.g. `http://localhost/PersianVid/public/setup`) in your browser. This dedicated secure endpoint allows you to set the username, email, and password for your first administrator account. Once created, the `/setup` endpoint permanently disables itself for security reasons.

7. **Storage Directory Setup**:
   Ensure the following directories exist and are writable by the Apache process:
   - `storage/videos/public/`
   - `storage/videos/private/`
   - `storage/thumbnails/`

8. **Running the App**:
   Visit `http://localhost/PersianVid/public/` (adjusting the folder name based on your XAMPP setup) to start using the site. For PHP's built-in server, you can run:
   ```bash
   php -S localhost:8000 -t public/
   ```

## Security Best Practices Enforced
- **SQL Injection**: Strict use of PDO prepared statements in all models.
- **XSS Protection**: Dedicated `Security::escape()` method wrapping `htmlspecialchars` in all views.
- **CSRF Protection**: Token generation and validation for all state-changing POST requests.
- **File Uploads**: Files are renamed securely with unique hashes. Extensions and MIME types are aggressively validated to prevent RCE.
- **Access Control**: Private videos are served via a custom HTTP Range streaming endpoint (`VideoController@stream`) that explicitly authenticates and authorizes the user before sending bytes. Direct access to `/storage/` via web is impossible.

## Testing Procedures
- Verify user registration logs you in seamlessly.
- Log in, upload a video. Verify FFmpeg generated a thumbnail (in `storage/thumbnails`).
- Test uploading an invalid file extension (e.g. `.php`) to verify it gets blocked.
- Set a video to private, copy the URL. Log out and try to access it; verify you are redirected or get a 403 error.
- Create a second user, attempt to view the private video; verify rejection. Add the first user as a friend, accept the request, and try viewing again; verify successful playback.
- Navigate to `/admin` as `admin` to verify you can see the dashboard, then log in as a normal user and attempt the same to verify you are blocked.

## Unfinished Work / Limitations
Due to the massive scope of building a robust CMS, secure video pipeline, custom framework, and social network from scratch, the following features described in the initial design were not implemented in this pass:
- Playlists and Watch Later functionality.
- Photo Albums.
- Profile Walls and Notifications.
- Private Messaging.
These features require additional database tables and controllers, but the existing custom MVC foundation easily accommodates scaling to include them in future iterations.
