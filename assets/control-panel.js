/**
 * Control Panel entry point for Vite build
 * Replaces gulpfile.js build process for the admin/control panel
 */

// Import control panel SCSS (includes CoreUI and admin-specific styles)
import './scss/control_panel.scss';

// Import control panel JavaScript
// This file requires jQuery (loaded via vendor.js)
import './js/control_panel.js';

// Import shared JS modules that are also used in the control panel
import './js/access_control.js';
import './js/button_deactivator.js';
import './js/bankAccountNumberValidation.js';
import './js/question_repeater.js';
import './js/popup_lower.js';
import './js/stupidtable.js';
import './js/csvGenerator.js';

// Note: CKEditor files are copied as static assets via vite.config.js
// CoreUI is also copied as a static vendor file
