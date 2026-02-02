import { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { HiMenuAlt3, HiX } from 'react-icons/hi';
import { MdHowToVote } from 'react-icons/md';
import Button from '../ui/Button';

const navLinks = [
    { name: 'About', href: '#about' },
    { name: 'Features', href: '#features' },
    { name: 'How It Works', href: '#how-it-works' },
    { name: 'Roles', href: '#roles' },
    { name: 'Security', href: '#security' },
];

export default function Navbar() {
    const [isScrolled, setIsScrolled] = useState(false);
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            setIsScrolled(window.scrollY > 50);
        };
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    return (
        <motion.nav
            initial={{ y: -100 }}
            animate={{ y: 0 }}
            transition={{ duration: 0.6, ease: 'easeOut' }}
            className={`
                fixed top-0 left-0 right-0 z-50 transition-all duration-300
                ${isScrolled 
                    ? 'bg-white/95 backdrop-blur-md shadow-lg shadow-gray-200/50' 
                    : 'bg-transparent'
                }
            `}
        >
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex items-center justify-between h-20">
                    {/* Logo */}
                    <motion.a
                        href="#"
                        whileHover={{ scale: 1.05 }}
                        className="flex items-center gap-3"
                    >
                        <div className="w-12 h-12 bg-gradient-to-br from-gov-green-700 to-gov-green-900 rounded-xl flex items-center justify-center shadow-lg">
                            <MdHowToVote className="w-7 h-7 text-white" />
                        </div>
                        <div className="hidden sm:block">
                            <span className={`text-xl font-bold ${isScrolled ? 'text-gov-green-900' : 'text-white'}`}>
                                CpsuVotewisely.com
                            </span>
                            <span className={`block text-xs font-medium ${isScrolled ? 'text-gov-green-600' : 'text-white/70'}`}>
                                Secure Cloud-Based Real-Time Voting Platform
                            </span>
                        </div>
                    </motion.a>

                    {/* Desktop Navigation */}
                    <div className="hidden lg:flex items-center gap-8">
                        {navLinks.map((link, index) => (
                            <motion.a
                                key={link.name}
                                href={link.href}
                                initial={{ opacity: 0, y: -20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ duration: 0.3, delay: index * 0.1 }}
                                className={`
                                    font-medium transition-colors duration-200
                                    ${isScrolled 
                                        ? 'text-gray-700 hover:text-gov-green-800' 
                                        : 'text-white/90 hover:text-white'
                                    }
                                `}
                            >
                                {link.name}
                            </motion.a>
                        ))}
                    </div>

                    {/* CTA Button */}
                    <div className="hidden lg:flex items-center gap-4">
                        <a href="/login">
                            <Button variant={isScrolled ? 'primary' : 'secondary'} size="sm">
                                Sign In
                            </Button>
                        </a>
                    </div>

                    {/* Mobile Menu Button */}
                    <button
                        onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                        className={`lg:hidden p-2 rounded-lg transition-colors ${
                            isScrolled ? 'text-gray-700' : 'text-white'
                        }`}
                    >
                        {isMobileMenuOpen ? (
                            <HiX className="w-6 h-6" />
                        ) : (
                            <HiMenuAlt3 className="w-6 h-6" />
                        )}
                    </button>
                </div>
            </div>

            {/* Mobile Menu */}
            <AnimatePresence>
                {isMobileMenuOpen && (
                    <motion.div
                        initial={{ opacity: 0, height: 0 }}
                        animate={{ opacity: 1, height: 'auto' }}
                        exit={{ opacity: 0, height: 0 }}
                        transition={{ duration: 0.3 }}
                        className="lg:hidden bg-white border-t border-gray-100 shadow-lg"
                    >
                        <div className="px-4 py-6 space-y-4">
                            {navLinks.map((link) => (
                                <a
                                    key={link.name}
                                    href={link.href}
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="block py-2 text-gray-700 hover:text-gov-green-800 font-medium"
                                >
                                    {link.name}
                                </a>
                            ))}
                            <div className="pt-4 border-t border-gray-100 flex flex-col gap-3">
                                <a href="/login" className="w-full">
                                    <Button variant="primary" size="md" className="w-full">
                                        Sign In
                                    </Button>
                                </a>
                            </div>
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
        </motion.nav>
    );
}
