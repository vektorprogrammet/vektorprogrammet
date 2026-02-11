/**
 * Main entry point for Vite build
 * Replaces gulpfile.js build process for the main site
 */

// Import main SCSS (includes Bootstrap and all custom styles)
import './main.scss';

// Import all JavaScript modules from assets/js/
// These were previously processed by Gulp's scriptsDev/scriptsProd tasks
// Order generally doesn't matter as most are self-contained modules

// Core functionality
import './js/mobile_nav.js';
import './js/access_control.js';
import './js/button_deactivator.js';

// Form utilities
import './js/bankAccountNumberValidation.js';
import './js/question_repeater.js';

// UI components
import './js/popup_lower.js';
import './js/faqCollapse.js';
import './js/stupidtable.js';

// CSV export functionality
import './js/csvGenerator.js';

// Note: CKEditor files are copied as static assets via vite.config.js
// They are loaded separately when needed, not bundled here
