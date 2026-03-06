# Frontend Development Checklist for Joomla 5/6 + Helix

## Pre-Development

### Environment Preparation
- [ ] Joomla 5.6+ installed locally
- [ ] Helix Ultimate 2.x+ template active
- [ ] Cache disabled in development (Site > Global Configuration > System)
- [ ] Error reporting enabled (System > Global Configuration > Server)
- [ ] Browser developer tools open (F12)
- [ ] Joomla debug extension installed (optional but recommended)

### Requirements Analysis
- [ ] Identify all necessary assets (CSS, JS)
- [ ] List dependencies between assets
- [ ] Define which assets are critical (above the fold)
- [ ] Determine responsive breakpoints (mobile, tablet, desktop)
- [ ] Create list of browsers to support

### Project Architecture
- [ ] Decide whether to use a module, component, or plugin
- [ ] Create folder structure
- [ ] Plan joomla.asset.json (presets, versioning)
- [ ] Define CSS/JS naming convention

---

## During Development

### File Structure
- [ ] Create `/css` folder in the project
- [ ] Create `/js` folder in the project
- [ ] Create `joomla.asset.json` in the project root
- [ ] Create `README.md` with instructions
- [ ] Do NOT create files in Helix core folders

### Assets Management (WebAssetManager)
- [ ] Register all assets in `joomla.asset.json`
- [ ] Use automatic versioning (`"version": "auto"`)
- [ ] Declare all dependencies correctly
- [ ] Use presets to group related assets
- [ ] Validate JSON with official schema

### CSS Development
- [ ] Use mobile-first approach (base CSS for mobile)
- [ ] Write modular and reusable CSS
- [ ] Do not use `!important` (except in extreme cases)
- [ ] Use CSS custom properties (variables)
- [ ] Follow naming convention (BEM or similar)
- [ ] Do NOT override Bootstrap classes directly
- [ ] Create custom classes instead of modifying base ones

### JavaScript Development
- [ ] Use `defer` for custom scripts
- [ ] Use `async` only for analytics/tracking
- [ ] Document functions with JSDoc
- [ ] Use `document.addEventListener('DOMContentLoaded', ...)`
- [ ] Handle errors appropriately
- [ ] Do NOT use jQuery unnecessarily (even though it is available)
- [ ] Consider Web Components for reusable components

### Responsive Design
- [ ] Design mobile-first
- [ ] Test at 375px viewport (small mobile)
- [ ] Test at 768px viewport (tablet)
- [ ] Test at 1024px viewport (desktop)
- [ ] Test at 1400px viewport (large desktop)
- [ ] Use media queries correctly
- [ ] Verify that Bootstrap grid works (col-12, col-md-6, etc.)
- [ ] Test zoom (100%, 90%, 110%)

### Custom Modules (if applicable)
- [ ] Create `joomla.asset.json` in the module root
- [ ] Create `helper.php` with helper methods
- [ ] Load assets in helper.php
- [ ] Create clean HTML template
- [ ] Add modular CSS and JS
- [ ] Make the module reusable

### SP Page Builder Addons (if applicable)
- [ ] Create sppagebuilder type plugin
- [ ] Use Custom CSS for styles
- [ ] For JS: create custom plugin
- [ ] Register assets in the plugin
- [ ] Document addon parameters
- [ ] Make the addon responsive

---

## Testing & Validation

### Desktop Browsers
- [ ] Chrome/Chromium (current version)
- [ ] Firefox (current version)
- [ ] Safari (if you have a Mac)
- [ ] Edge (current version)
- [ ] Compatibility with older versions (IE11 if required)

### Mobile Browsers
- [ ] Chrome Mobile
- [ ] Safari Mobile (iOS)
- [ ] Samsung Internet
- [ ] Firefox Mobile

### Code Validation
- [ ] Validate HTML (https://validator.w3.org/)
- [ ] Validate CSS (https://jigsaw.w3.org/css-validator/)
- [ ] Check console.log in Developer Tools (no errors)
- [ ] Check Network tab (no 404s)
- [ ] Use Lighthouse for auditing

### Performance
- [ ] PageSpeed Insights score > 80
- [ ] Lighthouse Performance > 80
- [ ] First Contentful Paint < 2s
- [ ] Largest Contentful Paint < 4s
- [ ] Cumulative Layout Shift < 0.1
- [ ] Total Blocking Time < 200ms
- [ ] Minify CSS/JS in production

### Accessibility
- [ ] WCAG 2.1 Level AA compliance
- [ ] Keyboard navigation functional
- [ ] Visible focus indicators
- [ ] Adequate color contrast (4.5:1 minimum)
- [ ] Labels on forms
- [ ] Alt text on images
- [ ] Correct HTML5 landmarks (header, main, footer)
- [ ] aria-labels where necessary

### Assets Loading
- [ ] `<jdoc:include type="head" />` in template
- [ ] Scripts load in correct order (Network tab)
- [ ] No circular dependencies
- [ ] All assets are available
- [ ] Versioning works (check asset URLs)
- [ ] Cache busting in production

### Responsive Testing
- [ ] Use responsive mode in DevTools
- [ ] Test portrait and landscape
- [ ] Verify media queries are applied
- [ ] Responsive images work
- [ ] Touch is functional (handle hover effects)
- [ ] Overflow controlled on mobile
- [ ] Touch targets > 48px

### Cross-browser Testing
- [ ] CSS variables supported in all browsers
- [ ] Flexbox renders the same
- [ ] Grid layout works
- [ ] Smooth CSS animations
- [ ] Fonts loaded correctly
- [ ] Responsive forms
- [ ] Audio/Video works (if applicable)

---

## Pre-Production

### Code Review
- [ ] Review joomla.asset.json for errors
- [ ] Review CSS for excessive specificity
- [ ] Review JS for memory leaks
- [ ] Review comments and documentation
- [ ] Clean up debug code (console.log, etc.)
- [ ] Verify no sensitive data in code

### Optimization
- [ ] Minify CSS (optional)
- [ ] Minify JS (optional)
- [ ] Optimize images (WEBP where possible)
- [ ] Use lazy loading for off-screen images
- [ ] Prefetch/preload critical resources (optional)
- [ ] Implement gzip compression
- [ ] Use CDN for large assets (optional)

### Security
- [ ] Validate form inputs
- [ ] Escape HTML outputs
- [ ] CSRF protection on forms
- [ ] Do not store sensitive data in localStorage
- [ ] HTTPS configured
- [ ] Content Security Policy headers (optional)

### Documentation
- [ ] Document project structure
- [ ] Create README.md with instructions
- [ ] Document CSS/JS conventions
- [ ] Create usage examples
- [ ] Document module parameters
- [ ] Create customization guide

### Backup & Versioning
- [ ] Code in Git/Version Control
- [ ] Version tags created
- [ ] Changelog updated
- [ ] Database backup pre-deployment
- [ ] File backup pre-deployment

---

## Production

### Final Configuration
- [ ] Cache enabled (Site > Global Configuration > Cache)
- [ ] Gzip enabled on server
- [ ] CDN configured (optional)
- [ ] Analytics installed
- [ ] Monitoring setup (Uptime Robot, New Relic, etc.)
- [ ] Error logging configured
- [ ] Automatic backups enabled

### Post-Deployment
- [ ] Review site in production
- [ ] Test all functionalities
- [ ] Verify assets load from CDN
- [ ] Check Console in DevTools (no errors)
- [ ] Check Network tab (optimal loading)
- [ ] PageSpeed Insights in production
- [ ] Unit/integration tests (if applicable)
- [ ] Monitor uptime and performance

### Maintenance
- [ ] Review error logs regularly
- [ ] Update Joomla when new version available
- [ ] Update plugins/extensions
- [ ] Monitor performance metrics
- [ ] Check broken links monthly
- [ ] Update obsolete content
- [ ] Daily database backups

---

## Quick Troubleshooting

### Assets Not Loading
```
1. Check <jdoc:include type="head" /> in template
2. Check path in joomla.asset.json
3. Clear Joomla cache (System > Clear Cache)
4. Check Network tab in DevTools (404?)
5. Check file permissions (755)
```

### CSS/JS Not Applying
```
1. Verify that $wa->useScript() was called
2. Check that joomla.asset.json is correct
3. Check CSS specificity (other styles overriding?)
4. Check Network tab (is the asset there?)
5. Hard refresh browser (Ctrl+Shift+R)
```

### Bootstrap Breaks
```
1. Verify that Bootstrap is in $wa->useStyle('bootstrap')
2. Do not override Bootstrap variables
3. Do not use !important on overrides
4. Check that Bootstrap classes are correct
5. Check load order (Bootstrap must load first)
```

### Helix Breaks
```
1. NEVER edit Helix core files (template.css, etc.)
2. ALWAYS use custom.css
3. ALWAYS use Custom Code section for global snippets
4. Check that Custom Code has no errors
5. Clear cache and hard refresh
```

---

## Useful Resources

- Joomla Manual: https://manual.joomla.org/
- Bootstrap Docs: https://getbootstrap.com/
- Helix Framework: https://www.joomshaper.com/
- Google DevTools: https://developer.chrome.com/docs/devtools/
- W3C HTML Validator: https://validator.w3.org/
- WebAIM Contrast Checker: https://webaim.org/resources/contrastchecker/
- GTmetrix Performance: https://gtmetrix.com/

---

Version: 1.0
Last updated: March 2026
