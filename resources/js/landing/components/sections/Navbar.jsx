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
    const [isRegisterModalOpen, setIsRegisterModalOpen] = useState(false);
    const [registerCode, setRegisterCode] = useState('');
    const [registerError, setRegisterError] = useState('');
    const [registerLoading, setRegisterLoading] = useState(false);

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

                    {/* CTA Buttons */}
                    <div className="hidden lg:flex items-center gap-3">
                        <button
                            type="button"
                            onClick={() => { setIsRegisterModalOpen(true); setRegisterError(''); setRegisterCode(''); }}
                            className={`text-sm font-medium px-4 py-2 rounded-lg transition-colors ${
                                isScrolled ? 'text-gov-green-800 hover:bg-gov-green-50' : 'text-white/90 hover:text-white hover:bg-white/10'
                            }`}
                        >
                            Register
                        </button>
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
                                                    className={`flex items-center gap-3 py-3.5 px-3 rounded-xl font-medium text-[15px] transition-colors ${
                                                        isElectionResults
                                                            ? 'text-gov-green-800 hover:bg-gov-green-50 hover:text-gov-green-900'
                                                            : 'text-gray-700 hover:text-gov-green-800 hover:bg-gray-50'
                                                    }`}
                                                >
                                                    <Icon
                                                        className={`w-5 h-5 flex-shrink-0 ${
                                                            isElectionResults ? 'text-gov-gold-500' : 'text-gov-green-600'
                                                        }`}
                                                    />
                                                    <span>{link.name}</span>
                                                </a>
                                            </li>
                                        );
                                    })}
                                </ul>
                            </nav>
                            {/* Register & Sign In */}
                            <div className="px-4 pt-2 space-y-3">
                                <button
                                    type="button"
                                    onClick={() => { setIsMobileMenuOpen(false); setIsRegisterModalOpen(true); setRegisterError(''); setRegisterCode(''); }}
                                    className="w-full rounded-xl py-3 px-4 font-semibold border-2 border-gov-green-700 text-gov-green-800 hover:bg-gov-green-50 transition-colors"
                                >
                                    Register
                                </button>
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

            {/* Registration checkpoint modal – enter code (e.g. cbrtvs) to access /register */}
            <AnimatePresence>
                {isRegisterModalOpen && (
                    <>
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60]"
                            onClick={() => setIsRegisterModalOpen(false)}
                            aria-hidden="true"
                        />
                        <motion.div
                            initial={{ opacity: 0, scale: 0.95 }}
                            animate={{ opacity: 1, scale: 1 }}
                            exit={{ opacity: 0, scale: 0.95 }}
                            transition={{ duration: 0.2 }}
                            className="fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-[70] w-full max-w-md mx-4 bg-white rounded-2xl shadow-xl border border-gray-200 p-6"
                            onClick={(e) => e.stopPropagation()}
                        >
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-lg font-semibold text-gray-900">Registration access</h3>
                                <button
                                    type="button"
                                    onClick={() => setIsRegisterModalOpen(false)}
                                    className="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                                    aria-label="Close"
                                >
                                    <HiX className="w-5 h-5" />
                                </button>
                            </div>
                            <p className="text-sm text-gray-600 mb-4">
                                Enter the access code to continue to registration. Without it you cannot create an account.
                            </p>
                            <form
                                onSubmit={async (e) => {
                                    e.preventDefault();
                                    setRegisterError('');
                                    setRegisterLoading(true);
                                    try {
                                        const formData = new FormData();
                                        formData.append('access_code', registerCode);
                                        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
                                        const res = await fetch('/register/access', {
                                            method: 'POST',
                                            body: formData,
                                            credentials: 'same-origin',
                                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                        });
                                        const data = await res.json().catch(() => ({}));
                                        if (res.ok && data.success && data.redirect) {
                                            window.location.href = data.redirect;
                                            return;
                                        }
                                        setRegisterError(data.message || 'Invalid access code.');
                                    } catch {
                                        setRegisterError('Something went wrong. Please try again.');
                                    } finally {
                                        setRegisterLoading(false);
                                    }
                                }}
                            >
                                <input
                                    type="text"
                                    value={registerCode}
                                    onChange={(e) => setRegisterCode(e.target.value)}
                                    placeholder="Access code"
                                    className="w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-gov-green-500 focus:border-gov-green-500 mb-3"
                                    required
                                    autoComplete="off"
                                    autoFocus
                                />
                                {registerError && (
                                    <p className="text-sm text-red-600 mb-3">{registerError}</p>
                                )}
                                <button
                                    type="submit"
                                    disabled={registerLoading}
                                    className="w-full py-3 rounded-xl font-semibold text-white bg-gov-green-700 hover:bg-gov-green-800 disabled:opacity-70 transition-colors"
                                >
                                    {registerLoading ? 'Checking…' : 'Continue to registration'}
                                </button>
                            </form>
                        </motion.div>
                    </>
                )}
            </AnimatePresence>
        </motion.nav>
    );
}
