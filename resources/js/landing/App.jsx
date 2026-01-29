import { useEffect, useRef } from 'react';
import Navbar from './components/sections/Navbar';
import Hero from './components/sections/Hero';
import About from './components/sections/About';
import Features from './components/sections/Features';
import HowItWorks from './components/sections/HowItWorks';
import UserRoles from './components/sections/UserRoles';
import Security from './components/sections/Security';
import Trust from './components/sections/Trust';
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
        <div className="min-h-screen bg-white font-sans antialiased">
            <Navbar />
            <main>
                <Hero />
                <About />
                <Features />
                <HowItWorks />
                <UserRoles />
                <Security />
                <Trust />
            </main>
            <Footer />
        </div>
    );
}
