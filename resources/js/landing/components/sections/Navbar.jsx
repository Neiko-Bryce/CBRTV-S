import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { HiMenuAlt3, HiX, HiInformationCircle, HiLightningBolt, HiPlay, HiUserGroup, HiShieldCheck } from 'react-icons/hi';
import { MdHowToVote, MdEmojiEvents } from 'react-icons/md';
import Button from '../ui/Button';

const navLinks = [
    { name: 'Election Results', href: '#live-results', icon: MdEmojiEvents },
    { name: 'About', href: '#about', icon: HiInformationCircle },
    { name: 'Features', href: '#features', icon: HiLightningBolt },
    { name: 'How It Works', href: '#how-it-works', icon: HiPlay },
    { name: 'Roles', href: '#roles', icon: HiUserGroup },
    { name: 'Security', href: '#security', icon: HiShieldCheck },
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

                    {/* CTA Buttons – Register hidden on landing; use /register/access directly if needed */}
                    <div className="hidden lg:flex items-center gap-3">
                        <a href="/login">
                            <Button variant={isScrolled ? 'primary' : 'secondary'} size="sm">
                                Sign In
                            </Button>
                        </a>
                    </div>

                    {/* Mobile Menu Button */}
                    <button
                        onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                        className={`lg:hidden p-2 rounded-lg transition-colors ${isScrolled ? 'text-gray-700' : 'text-white'
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

            {/* Mobile Menu – professional dropdown */}
            <AnimatePresence>
                {isMobileMenuOpen && (
                    <>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            transition={{ duration: 0.2 }}
                            className="lg:hidden fixed inset-0 top-20 bg-black/30 backdrop-blur-sm z-40"
                            onClick={() => setIsMobileMenuOpen(false)}
                            aria-hidden="true"
                        />
                        <motion.div
                            initial={{ opacity: 0, y: -8 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -8 }}
                            transition={{ duration: 0.25, ease: 'easeOut' }}
                            className="lg:hidden fixed left-0 right-0 top-20 z-50 mx-4 rounded-2xl bg-white shadow-xl border border-gray-100 overflow-hidden"
                        >
                            {/* Menu header */}
                            <div className="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 bg-gradient-to-br from-gov-green-600 to-gov-green-800 rounded-xl flex items-center justify-center shadow">
                                        <MdHowToVote className="w-5 h-5 text-white" />
                                    </div>
                                    <span className="text-sm font-semibold text-gray-800">Menu</span>
                                </div>
                                <button
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="p-2 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-colors"
                                    aria-label="Close menu"
                                >
                                    <HiX className="w-6 h-6" />
                                </button>
                            </div>
                            {/* Nav links */}
                            <nav className="px-4 py-4">
                                <ul className="space-y-0.5">
                                    {navLinks.map((link) => {
                                        const Icon = link.icon;
                                        const isElectionResults = link.name === 'Election Results';
                                        return (
                                            <li key={link.name}>
                                                <a
                                                    href={link.href}
                                                    onClick={() => setIsMobileMenuOpen(false)}
                                                    className={`flex items-center gap-3 py-3.5 px-3 rounded-xl font-medium text-[15px] transition-colors ${isElectionResults
                                                            ? 'text-gov-green-800 hover:bg-gov-green-50 hover:text-gov-green-900'
                                                            : 'text-gray-700 hover:text-gov-green-800 hover:bg-gray-50'
                                                        }`}
                                                >
                                                    <Icon
                                                        className={`w-5 h-5 flex-shrink-0 ${isElectionResults ? 'text-gov-gold-500' : 'text-gov-green-600'
                                                            }`}
                                                    />
                                                    <span>{link.name}</span>
                                                </a>
                                            </li>
                                        );
                                    })}
                                </ul>
                            </nav>
                            {/* Sign In & Results */}
                            <div className="px-4 pt-2 space-y-3">
                                <a
                                    href="/login"
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="block w-full"
                                >
                                    <Button variant="primary" size="md" className="w-full rounded-xl py-3 font-semibold">
                                        Sign In
                                    </Button>
                                </a>
                                {/* Results Revealed – like reference: gold-style CTA with trophy */}
                                <a
                                    href="#live-results"
                                    onClick={() => setIsMobileMenuOpen(false)}
                                    className="flex items-center justify-center gap-2 w-full rounded-xl py-3 px-4 bg-gov-gold-400 hover:bg-gov-gold-500 text-gov-green-900 font-bold text-sm uppercase tracking-wide transition-colors"
                                >
                                    <MdEmojiEvents className="w-5 h-5 flex-shrink-0" />
                                    Results Revealed
                                </a>
                            </div>
                            <div className="px-4 pb-5" />
                        </motion.div>
                    </>
                )}
            </AnimatePresence>
        </motion.nav>
    );
}
