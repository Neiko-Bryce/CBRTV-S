import React from 'react';
import { motion } from 'framer-motion';
import { MdHowToVote } from 'react-icons/md';
import {
    HiMail,
    HiPhone,
    HiLocationMarker,
    HiLockClosed,
    HiShieldCheck,
    HiGlobeAlt,
    HiServer
} from 'react-icons/hi';
import {
    FaFacebookF,
    FaInstagram,
    FaTelegram
} from 'react-icons/fa';

const footerLinks = {
    navigation: {
        title: 'Navigation',
        links: [
            { name: 'Election Results', href: '#live-results' },
            { name: 'Features', href: '#features' },
            { name: 'About Us', href: '#about' },
        ],
    },
    legal: {
        title: 'Legal',
        links: [
            { name: 'Privacy Policy', href: '#' },
            { name: 'Terms of Service', href: '#' },
        ],
    },
};

const socialLinks = [
    { icon: FaFacebookF, href: 'https://www.facebook.com/neiko.bryce.fantilaga.2024/', label: 'Facebook' },
    { icon: FaInstagram, href: 'https://instagram.com/mbryce_fntlg', label: 'Instagram' },
    { icon: FaTelegram, href: 'https://t.me/+639152087468', label: 'Telegram' },
];

export default function Footer() {
    return (
        <footer className="bg-gray-900 text-white overflow-hidden">
            {/* Main Footer */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 lg:py-16">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10 lg:gap-12">
                    {/* Brand Column */}
                    <div className="sm:col-span-2 lg:col-span-2">
                        <motion.a
                            href="#"
                            initial={{ opacity: 0 }}
                            whileInView={{ opacity: 1 }}
                            viewport={{ once: true }}
                            className="flex items-center gap-3 mb-4 sm:mb-6"
                        >
                            <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gov-green-600 to-gov-green-800 rounded-xl flex items-center justify-center flex-shrink-0">
                                <MdHowToVote className="w-5 h-5 sm:w-7 sm:h-7 text-white" />
                            </div>
                            <div>
                                <span className="text-lg sm:text-xl font-bold">CpsuVotewisely.com</span>
                                <span className="block text-[10px] sm:text-xs text-gray-400">Secure Cloud-Based Real-Time Voting Platform</span>
                            </div>
                        </motion.a>

                        <p className="text-gray-400 mb-4 sm:mb-6 leading-relaxed text-sm sm:text-base">
                            A professional cloud-based real-time voting system designed for secure, transparent, and efficient elections. Trusted by organizations worldwide.
                        </p>

                        {/* Contact Info */}
                        <div className="space-y-2 sm:space-y-3">
                            <div className="flex items-center gap-2 sm:gap-3 text-gray-400 text-sm sm:text-base">
                                <HiMail className="w-4 h-4 sm:w-5 sm:h-5 text-gov-gold-500 flex-shrink-0" />
                                <span className="truncate">CBRT@votingsystem.gmail.com</span>
                            </div>
                            <div className="flex items-center gap-2 sm:gap-3 text-gray-400 text-sm sm:text-base">
                                <HiPhone className="w-4 h-4 sm:w-5 sm:h-5 text-gov-gold-500 flex-shrink-0" />
                                <span>+63 946 024 1508</span>
                            </div>
                            <div className="flex items-center gap-2 sm:gap-3 text-gray-400 text-sm sm:text-base">
                                <HiLocationMarker className="w-4 h-4 sm:w-5 sm:h-5 text-gov-gold-500 flex-shrink-0" />
                                <span>Hinoba-an, Negros Occ.</span>
                            </div>
                        </div>
                    </div>

                    {/* Links Columns - 2 columns on mobile, 4 on larger screens */}
                    {Object.values(footerLinks).map((section, index) => (
                        <motion.div
                            key={section.title}
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: index * 0.1 }}
                            className="min-w-0"
                        >
                            <h4 className="text-white font-semibold mb-3 sm:mb-4 text-sm sm:text-base">{section.title}</h4>
                            <ul className="space-y-2 sm:space-y-3">
                                {section.links.map((link) => (
                                    <li key={link.name}>
                                        <a
                                            href={link.href}
                                            className="text-gray-400 hover:text-gov-gold-400 transition-colors duration-200 text-sm sm:text-base"
                                        >
                                            {link.name}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </motion.div>
                    ))}
                </div>
            </div>

            {/* Bottom Bar */}
            <div className="border-t border-gray-800">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
                        {/* Copyright */}
                        <div className="text-gray-400 text-xs sm:text-sm text-center sm:text-left">
                            <p>&copy; {new Date().getFullYear()} CpsuVotewisely.com. All rights reserved.</p>
                            <p className="mt-0.5 sm:mt-1">Empowering transparent democracy through technology.</p>
                        </div>

                        {/* Social Links */}
                        <div className="flex items-center gap-3 sm:gap-4">
                            {socialLinks.map((social) => (
                                <motion.a
                                    key={social.label}
                                    href={social.href}
                                    whileHover={{ scale: 1.1, y: -2 }}
                                    whileTap={{ scale: 0.95 }}
                                    className="w-9 h-9 sm:w-10 sm:h-10 bg-gray-800 hover:bg-gov-green-700 rounded-lg flex items-center justify-center transition-colors duration-200"
                                    aria-label={social.label}
                                >
                                    <social.icon className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
                                </motion.a>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

        </footer>
    );
}
