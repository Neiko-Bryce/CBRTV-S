import React, { useEffect, useRef } from 'react';
import Navbar from './components/sections/Navbar';
import Hero from './components/sections/Hero';
import LiveResults from './components/sections/LiveResults';
import About from './components/sections/About';
import Features from './components/sections/Features';
import Footer from './components/sections/Footer';

export default function App() {
    const typedKeys = useRef('');
    const secretCode = 'cbrtvs';

    useEffect(() => {
        const handleKeyPress = (e) => {
            // Only track letter keys
            if (e.key.length === 1 && e.key.match(/[a-z]/i)) {
                typedKeys.current += e.key.toLowerCase();

                // Keep only the last 6 characters
                if (typedKeys.current.length > secretCode.length) {
                    typedKeys.current = typedKeys.current.slice(-secretCode.length);
                }

                // Check if secret code is typed
                if (typedKeys.current === secretCode) {
                    typedKeys.current = '';
                    window.location.href = '/register';
                }
            }
        };

        window.addEventListener('keypress', handleKeyPress);
        return () => window.removeEventListener('keypress', handleKeyPress);
    }, []);

    return (
        <div className="min-h-screen bg-white font-sans antialiased overflow-x-hidden">
            <Navbar />
            <main className="overflow-x-hidden">
                <Hero />
                <LiveResults />
                <About />
                <Features />
            </main>
            <Footer />
        </div>
    );
}
