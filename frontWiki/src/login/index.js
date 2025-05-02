import { Swal } from '../shared/libs.js';
import { Images } from '../shared/img.js';

import { validateForm, mostrarError } from './validator.js';
import { getCSRFToken, updateCSRFToken } from '../shared/csrf.js';
import { bindLoginEvents } from './events.js';


document.addEventListener("DOMContentLoaded", () => {
  bindLoginEvents({ validateForm, mostrarError, getCSRFToken, updateCSRFToken });
});

