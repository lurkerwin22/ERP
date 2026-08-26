import './chat';
import './sidebar';
import './form-submit';
import './product-filters';
import './sales';
import './purchases';
import './validation';

import { initProductMarginCalculator } from './product-margin';

// Run when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductMarginCalculator);
} else {
    // DOM is already ready
    initProductMarginCalculator();
}