# Quick Code Snippets - Joomla Frontend

## PHP Snippets

### Get WebAssetManager
```php
$wa = \Joomla\CMS\Factory::getApplication()
      ->getDocument()
      ->getWebAssetManager();
```

### Load Script
```php
$wa->useScript('jquery');
$wa->useScript('my-custom-script');
```

### Load Style
```php
$wa->useStyle('bootstrap');
$wa->useStyle('my-custom-style');
```

### Register and Use Script Dynamically
```php
$wa->registerAndUseScript(
    'my-dynamic-script',
    'js/dynamic.js',
    ['dependencies' => ['jquery']],
    ['defer' => true]
);
```

### Add Inline Script
```php
$wa->addInlineScript('
  console.log("Inline script");
');
```

### Add Inline Style
```php
$wa->addInlineStyle('
  .my-class { color: red; }
');
```

---

## JSON Assets

### Simple Asset
```json
{
  "name": "my-script",
  "type": "script",
  "uri": "js/myfile.js",
  "dependencies": ["jquery"],
  "attributes": {
    "defer": true,
    "async": false
  },
  "version": "auto"
}
```

### Style Asset
```json
{
  "name": "my-style",
  "type": "style",
  "uri": "css/myfile.css",
  "version": "auto"
}
```

### Script with CDN
```json
{
  "name": "cdnjs-lib",
  "type": "script",
  "uri": "https://cdn.example.com/lib.min.js",
  "attributes": {
    "defer": true
  },
  "version": "1.2.3"
}
```

### Preset (Asset Group)
```json
{
  "name": "my-preset",
  "type": "preset",
  "uri": "",
  "dependencies": [
    "jquery#script",
    "bootstrap#script",
    "bootstrap#style"
  ]
}
```

---

## CSS Snippets

### Mobile First Base
```css
/* Mobile first - base styles for mobile */
.element {
  padding: 1rem;
  font-size: 14px;
}

/* Tablet (768px) */
@media (min-width: 768px) {
  .element {
    padding: 2rem;
    font-size: 16px;
  }
}

/* Desktop (992px) */
@media (min-width: 992px) {
  .element {
    padding: 3rem;
    font-size: 18px;
  }
}
```

### Responsive Flexbox
```css
.flex-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

@media (min-width: 768px) {
  .flex-container {
    flex-direction: row;
    justify-content: space-between;
  }
}
```

### Responsive Grid
```css
.grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

@media (min-width: 768px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 992px) {
  .grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
```

### Custom Button
```css
.btn-custom {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-custom:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-custom:active {
  transform: translateY(0);
}
```

### Enhanced Bootstrap Card
```css
.card-custom {
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.card-custom:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.card-custom .card-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
}
```

### Responsive Image
```css
.img-responsive {
  max-width: 100%;
  height: auto;
  display: block;
}

.img-contain {
  width: 100%;
  height: 300px;
  object-fit: contain;
  background: #f5f5f5;
}
```

### Dark Theme
```css
@media (prefers-color-scheme: dark) {
  body {
    background: #1a1a1a;
    color: #e0e0e0;
  }

  .card {
    background: #2a2a2a;
    color: #e0e0e0;
  }
}
```

### Accessibility - Reduce Motion
```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## JavaScript Snippets

### DOM Ready
```javascript
document.addEventListener('DOMContentLoaded', function() {
  // Your code here
  console.log('DOM ready');
});
```

### Select Elements
```javascript
// Single element
const element = document.querySelector('.my-class');

// Multiple elements
const elements = document.querySelectorAll('.items');

// By ID
const element2 = document.getElementById('my-id');
```

### Event Listeners
```javascript
// Click
element.addEventListener('click', function() {
  console.log('Clicked');
});

// Submit form
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
  e.preventDefault();
  console.log('Form submitted');
});

// Keyboard
document.addEventListener('keydown', function(e) {
  if (e.key === 'Enter') {
    console.log('Enter pressed');
  }
});
```

### Manipulate Classes
```javascript
const element = document.querySelector('.element');

// Add class
element.classList.add('active');

// Remove class
element.classList.remove('hidden');

// Toggle class
element.classList.toggle('visible');

// Check if has class
if (element.classList.contains('active')) {
  console.log('Has active class');
}
```

### Modify Attributes
```javascript
const element = document.querySelector('.element');

// Get attribute
const value = element.getAttribute('data-id');

// Set attribute
element.setAttribute('data-id', '123');

// Check attribute
if (element.hasAttribute('data-toggle')) {
  console.log('Has attribute');
}
```

### AJAX / Fetch
```javascript
// Fetch GET
fetch('/api/data')
  .then(response => response.json())
  .then(data => {
    console.log(data);
  })
  .catch(error => console.error('Error:', error));

// Fetch POST
fetch('/api/submit', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    name: 'John',
    email: 'john@example.com'
  })
})
.then(response => response.json())
.then(data => console.log('Success:', data));
```

### Custom Event
```javascript
// Create custom event
const event = new CustomEvent('my-event', {
  detail: { message: 'Hello' },
  bubbles: true,
  composed: true
});

// Dispatch event
element.dispatchEvent(event);

// Listen for custom event
document.addEventListener('my-event', function(e) {
  console.log(e.detail.message);
});
```

### Smooth Scroll
```javascript
document.querySelectorAll('a[href^="#"]').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if(target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});
```

### LocalStorage
```javascript
// Save data
localStorage.setItem('myData', 'value');

// Get data
const value = localStorage.getItem('myData');

// Remove data
localStorage.removeItem('myData');

// Clear all
localStorage.clear();
```

### Validate Form
```javascript
const form = document.querySelector('form');

form.addEventListener('submit', function(e) {
  const email = document.querySelector('input[name="email"]');

  if (!email.value.includes('@')) {
    e.preventDefault();
    email.classList.add('is-invalid');
    console.log('Invalid email');
  }
});
```

### Throttle/Debounce
```javascript
// Debounce - execute after user stops typing
function debounce(func, wait) {
  let timeout;
  return function(...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

const search = debounce(function(query) {
  console.log('Searching:', query);
}, 300);

// Throttle - execute at most every X time
function throttle(func, limit) {
  let inThrottle;
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}

const onScroll = throttle(function() {
  console.log('Scrolling');
}, 1000);
```

---

## jQuery (if you are using it)

### DOM Ready
```javascript
jQuery(document).ready(function($) {
  // Your jQuery code
});
```

### Select Elements
```javascript
const element = $('.my-class');
const elements = $('.items');
const element2 = $('#my-id');
```

### Event Listeners
```javascript
$('.element').on('click', function() {
  console.log('Clicked');
});
```

### Modify Classes
```javascript
$('.element').addClass('active');
$('.element').removeClass('hidden');
$('.element').toggleClass('visible');
```

### AJAX jQuery
```javascript
$.ajax({
  url: '/api/data',
  type: 'GET',
  dataType: 'json',
  success: function(data) {
    console.log(data);
  },
  error: function(error) {
    console.error('Error:', error);
  }
});
```

---

## Web Components

### Use Web Component
```html
<my-component
  title="My Component"
  description="Description"
  clickable="true">
</my-component>
```

### Listen for Custom Event
```javascript
document.addEventListener('my-event', function(e) {
  console.log(e.detail);
});
```

### Create Simple Web Component
```javascript
class MyComponent extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
      <div class="my-component">
        <h2>${this.getAttribute('title')}</h2>
      </div>
    `;
  }
}

customElements.define('my-component', MyComponent);
```

---

## Helix Custom Code

### CSS in Helix
```css
/* Template Options > Custom Code > Custom CSS */
.my-navbar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.my-button {
  background: #667eea;
  color: white;
  padding: 12px 24px;
}
```

### JavaScript in Helix
```javascript
/* Template Options > Custom Code > Custom JavaScript */
document.addEventListener('DOMContentLoaded', function() {
  console.log('Helix Custom JS');

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if(target) target.scrollIntoView({behavior: 'smooth'});
    });
  });
});
```

---

## Debugging

### Console
```javascript
console.log('Message');              // Information
console.warn('Warning');             // Warning
console.error('Error');              // Error
console.table(data);                 // Table
console.time('name');                // Start timer
console.timeEnd('name');             // End timer
```

### Breakpoints
```javascript
debugger; // Code pauses here in DevTools
```

### View the DOM
```javascript
console.log(document.documentElement.outerHTML); // Complete HTML
```

---

Last updated: March 2026
