import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import '../../css/app.css';

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('landing-root');
    
    if (rootElement) {
        ReactDOM.createRoot(rootElement).render(
            <React.StrictMode>
                <App />
            </React.StrictMode>
        );
    }
});
