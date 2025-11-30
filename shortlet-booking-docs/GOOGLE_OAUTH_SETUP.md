# Google OAuth Setup Guide

This guide will help you configure Google OAuth credentials for the ShortletNG application.

## Prerequisites

- Google account
- Access to [Google Cloud Console](https://console.cloud.google.com/)

## Step-by-Step Setup

### 1. Create a Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click on the project dropdown at the top of the page
3. Click "New Project"
4. Enter project name: **ShortletNG** (or your preferred name)
5. Click "Create"

### 2. Enable Google+ API

1. In the left sidebar, navigate to **APIs & Services** → **Library**
2. Search for "Google+ API"
3. Click on it and click "Enable"

> **Note**: Google+ API is deprecated but still required for OAuth to work properly with Socialite.

### 3. Configure OAuth Consent Screen

1. Navigate to **APIs & Services** → **OAuth consent screen**
2. Select **External** user type (unless you have a Google Workspace account)
3. Click "Create"
4. Fill in the required fields:
   - **App name**: ShortletNG
   - **User support email**: Your email
   - **Developer contact information**: Your email
5. Click "Save and Continue"
6. **Scopes**: Click "Add or Remove Scopes"
   - Add `.../auth/userinfo.email`
   - Add `.../auth/userinfo.profile`
7. Click "Save and Continue"
8. **Test users**: Add your email for testing
9. Click "Save and Continue"

### 4. Create OAuth 2.0 Credentials

1. Navigate to **APIs & Services** → **Credentials**
2. Click "Create Credentials" → "OAuth client ID"
3. Select **Web application** as the application type
4. Enter name: **ShortletNG Web Client**
5. Add **Authorized JavaScript origins**:
   - `http://localhost:8000` (for local development)
   - `http://localhost:3000` (if using Vite dev server)
6. Add **Authorized redirect URIs**:
   - `http://localhost:8000/auth/google/callback`
   - `http://localhost:3000/auth/google/callback`
7. Click "Create"

### 5. Copy Credentials

After creation, you'll see a popup with:
- **Client ID**: `1234567890-abcdefg.apps.googleusercontent.com`
- **Client Secret**: `GOCSPX-xxxxxxxxxxxxx`

**Important**: Keep these credentials secure!

### 6. Update .env File

1. Open your `.env` file in the project root
2. Find the Google OAuth section:
   ```env
   # Google OAuth Configuration
   GOOGLE_CLIENT_ID=your_client_id_here
   GOOGLE_CLIENT_SECRET=your_client_secret_here
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```
3. Replace `your_client_id_here` with your actual Client ID
4. Replace `your_client_secret_here` with your actual Client Secret

### 7. Update APP_URL (if needed)

Make sure your `APP_URL` in `.env` matches your development server:

```env
APP_URL=http://localhost:8000
```

Or if using `php artisan serve` with a different port:
```env
APP_URL=http://localhost:PORT
```

## Testing the Setup

1. Start your development server:
   ```bash
   php artisan serve
   npm run dev
   ```

2. Visit: `http://localhost:8000/auth/google`

3. You should be redirected to Google's consent screen

4. Grant permissions and you'll be redirected back to your app

## Troubleshooting

### Error: "redirect_uri_mismatch"
- Ensure the redirect URI in Google Console exactly matches your callback URL
- Check for trailing slashes
- Verify `APP_URL` in `.env` is correct

### Error: "invalid_client"
- Double-check your Client ID and Client Secret in `.env`
- Ensure there are no extra spaces or quotes

### Error: "access_denied"
- User cancelled the OAuth flow (expected behavior)
- Or your app is not configured correctly in Google Console

## Production Setup

When deploying to production:

1. Add your production domain to Google Console:
   - Authorized JavaScript origins: `https://yourdomain.com`
   - Authorized redirect URIs: `https://yourdomain.com/auth/google/callback`

2. Update `.env` on production server with the same credentials

3. Consider creating separate OAuth credentials for production

## Security Notes

- **Never commit** `.env` file to Git
- Keep your Client Secret secure
- Use environment variables for all sensitive data
- Regularly rotate credentials if compromised

---

**Need Help?**
- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Laravel Socialite Documentation](https://laravel.com/docs/socialite)
