// export const baseUrl = process.env.NODE_ENV === 'development' ? 'http://localhost:8000/api' : 'https://vektorprogrammet.no/api';
export const baseUrl = process.env.NODE_ENV === 'development' ? 'http://localhost:8000/api' : process.env.NODE_ENV === 'staging' ? 'https://assistant-dashboard.staging.vektorprogrammet.no/api' : 'https://vektorprogrammet.no/api';
