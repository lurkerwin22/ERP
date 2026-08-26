import './chat';
import './sidebar';
import './form-submit';
import './product-filters';
import './sales';
import './purchases';
import './validation';

import { initProductMarginCalculator } from './product-margin';

document.addEventListener('DOMContentLoaded', () => {
    initProductMarginCalculator();
});