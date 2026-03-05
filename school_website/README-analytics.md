# Google Analytics Setup for Everest School Website

## Getting Started

This website uses Google Analytics 4 (GA4) to track user interactions and page views. 

## Setting up Google Analytics

1. **Create a Google Analytics Property**
   - Go to [Google Analytics](https://analytics.google.com/)
   - Sign in with your Google account
   - Click "Admin" in the bottom left
   - Create a new property for your website
   - Select "Web" as the platform
   - Enter your website URL
   - Complete the setup process

2. **Get Your Measurement ID**
   - After creating the property, you'll receive a Measurement ID
   - It will look like: `G-XXXXXXXXXX` (for GA4) or `UA-XXXXXXXXX-X` (for Universal Analytics)
   - Note this ID for the next step

3. **Configure the Website**
   - Open `/config/analytics.php`
   - Replace `'GA_MEASUREMENT_ID'` with your actual Measurement ID
   - Example: `define('GOOGLE_ANALYTICS_ID', 'G-1234567890');`

## Configuration Options

The analytics configuration file (`/config/analytics.php`) includes the following options:

- `GOOGLE_ANALYTICS_ID`: Your Google Analytics Measurement ID
- `ANALYTICS_ENHANCED_ECOMMERCE`: Enable enhanced e-commerce tracking (true/false)
- `ANALYTICS_DEBUG_MODE`: Enable debug mode for testing (true/false)

## Features Included

- **Page View Tracking**: Automatically tracks all page views
- **Form Submission Tracking**: Tracks when users submit forms
- **Button Click Tracking**: Tracks clicks on buttons and links with class "btn"
- **Enhanced Tracking**: Includes scroll depth, outbound link clicks, and more

## Testing Your Installation

1. Set `ANALYTICS_DEBUG_MODE` to `true`
2. Visit your website
3. Open browser developer tools (F12)
4. Check the Console tab for "Analytics Debug Mode Enabled" message
5. Verify real-time reports in Google Analytics dashboard

## Troubleshooting

- If tracking isn't working, check that your Measurement ID is correct
- Ensure the site is served over HTTP or HTTPS (not file://)
- Verify that ad blockers aren't blocking Google Analytics
- Check browser console for any JavaScript errors

## Privacy Considerations

- The website respects user privacy
- Data collection complies with applicable privacy laws
- Users can opt out of tracking if desired

## Need Help?

For technical issues with the tracking code, contact your web developer.
For questions about Google Analytics data, visit the [Google Analytics Help Center](https://support.google.com/analytics/).