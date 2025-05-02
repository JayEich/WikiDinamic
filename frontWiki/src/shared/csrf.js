export function getCSRFToken() {
    return document.querySelector('meta[name="csrf_token"]')?.content || '';
  }
  
  export function updateCSRFToken(newToken) {
    const meta = document.querySelector('meta[name="csrf_token"]');
    if (meta) meta.content = newToken;
  }
  