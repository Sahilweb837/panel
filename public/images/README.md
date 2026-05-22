# Logo and Images Guide

## Adding Your Logo

1. **Place your logo** in this directory as `logo.png`
2. **Recommended dimensions**: 200x200px or larger (square format)
3. **File format**: PNG with transparent background works best
4. **The logo will automatically appear** on the login page

If the logo file is not found, the application will show a fallback icon instead.

## Using the Images Folder

This folder is for storing images used throughout the application:
- Company logos
- User avatars
- Icons and graphics
- Background images

## Logo Sizing Tips

- **Square logos**: 200x200px to 400x400px
- **Rectangular logos**: Maintain aspect ratio, max-height: 80px on login page
- **Transparency**: Use PNG format with transparent background for better compatibility

## Dark Mode Consideration

The login page supports both light and dark themes. Make sure your logo looks good in both:
- Light background on login page
- Dark background in dark mode is supported via CSS

## File Structure

```
public/
├── images/
│   ├── logo.png          (Your company logo here)
│   ├── favicon.ico
│   └── README.md         (This file)
```

For more information about customizing the application, check the main README.md in the project root.
