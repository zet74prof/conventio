import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
// 1. Import Bootstrap CSS (AssetMapper injects this stylesheet)
import 'bootstrap/dist/css/bootstrap.min.css';

// 2. Import Bootstrap JS (enables modals, tooltips, dropdowns, etc.)
import 'bootstrap';

// 3. Import custom CSS
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
