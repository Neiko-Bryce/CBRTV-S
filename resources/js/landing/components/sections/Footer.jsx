import { motion } from 'framer-motion';
import { MdHowToVote } from 'react-icons/md';
import { 
    HiMail, 
    HiPhone, 
    HiLocationMarker 
} from 'react-icons/hi';
import { 
    FaFacebookF, 
    FaTwitter, 
    FaLinkedinIn, 
    FaYoutube 
} from 'react-icons/fa';

const footerLinks = {
    product: {
        title: 'Product',
        links: [
            { name: 'Features', href: '#features' },
            { name: 'Security', href: '#security' },
            { name: 'Pricing', href: '#' },
            { name: 'Integrations', href: '#' },
            { name: 'API Documentation', href: '#' },
        ],
    },
    company: {
        title: 'Company',
        links: [
            { name: 'About Us', href: '#about' },
            { name: 'Careers', href: '#' },
            { name: 'Press Kit', href: '#' },
            { name: 'Contact', href: '#' },
            { name: 'Partners', href: '#' },
        ],
    },
    resources: {
        title: 'Resources',
        links: [
            { name: 'Help Center', href: '#' },
            { name: 'Blog', href: '#' },
            { name: 'Case Studies', href: '#' },
            { name: 'Webinars', href: '#' },
            { name: 'System Status', href: '#' },
        ],
    },
    legal: {
        title: 'Legal',
        links: [
            { name: 'Privacy Policy', href: '#' },
            { name: 'Terms of Service', href: '#' },
            { name: 'Cookie Policy', href: '#' },
            { name: 'GDPR Compliance', href: '#' },
            { name: 'Accessibility', href: '#' },
        ],
    },
};

const socialLinks = [
    { icon: FaFacebookF, href: '#', label: 'Facebook' },
    { icon: FaTwitter, href: '#', label: 'Twitter' },
    { icon: FaLinkedinIn, href: '#', label: 'LinkedIn' },
    { icon: FaYoutube, href: '#', label: 'YouTube' },
];

export default function Footer() {
    return (
        <footer className="bg-gray-900 text-white">
            {/* Main Footer */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div className="grid lg:grid-cols-6 gap-12">
                    {/* Brand Column */}
                    <div className="lg:col-span-2">
                        <motion.a
                            href="#"
                            initial={{ opacity: 0 }}
                            whileInView={{ opacity: 1 }}
                            viewport={{ once: true }}
                            className="flex items-center gap-3 mb-6"
                        >
                            <div className="w-12 h-12 bg-gradient-to-br from-gov-green-600 to-gov-green-800 rounded-xl flex items-center justify-center">
                                <MdHowToVote className="w-7 h-7 text-white" />
                            </div>
                            <div>
                                <span className="text-xl font-bold">CivicVote</span>
                                <span className="block text-xs text-gray-400">Cloud Voting System</span>
                            </div>
                        </motion.a>

                        <p className="text-gray-400 mb-6 leading-relaxed">
                            A professional cloud-based real-time voting system designed for schools, 
                            universities, and community organizations. Secure, transparent, and trusted.
                        </p>

                        {/* Contact Info */}
                        <div className="space-y-3">
                            <div className="flex items-center gap-3 text-gray-400">
                                <HiMail className="w-5 h-5 text-gov-gold-500" />
                                <span>support@civicvote.com</span>
                            </div>
                            <div className="flex items-center gap-3 text-gray-400">
                                <HiPhone className="w-5 h-5 text-gov-gold-500" />
                                <span>+1 (555) 123-4567</span>
                            </div>
                            <div className="flex items-center gap-3 text-gray-400">
                                <HiLocationMarker className="w-5 h-5 text-gov-gold-500" />
                                <span>123 Democracy Ave, Suite 100</span>
                            </div>
                        </div>
                    </div>

                    {/* Links Columns */}
                    {Object.values(footerLinks).map((section, index) => (
                        <motion.div
                            key={section.title}
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: index * 0.1 }}
                        >
                            <h4 className="text-white font-semibold mb-4">{section.title}</h4>
                            <ul className="space-y-3">
                                {section.links.map((link) => (
                                    <li key={link.name}>
                                        <a
                                            href={link.href}
                                            className="text-gray-400 hover:text-gov-gold-400 transition-colors duration-200"
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
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div className="flex flex-col md:flex-row items-center justify-between gap-4">
                        {/* Copyright */}
                        <div className="text-gray-400 text-sm text-center md:text-left">
                            <p>&copy; {new Date().getFullYear()} CivicVote. All rights reserved.</p>
                            <p className="mt-1">Empowering transparent democracy through technology.</p>
                        </div>

                        {/* Social Links */}
                        <div className="flex items-center gap-4">
                            {socialLinks.map((social) => (
                                <motion.a
                                    key={social.label}
                                    href={social.href}
                                    whileHover={{ scale: 1.1, y: -2 }}
                                    whileTap={{ scale: 0.95 }}
                                    className="w-10 h-10 bg-gray-800 hover:bg-gov-green-700 rounded-lg flex items-center justify-center transition-colors duration-200"
                                    aria-label={social.label}
                                >
                                    <social.icon className="w-4 h-4" />
                                </motion.a>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            {/* Trust Badges */}
            <div className="bg-gray-950 py-4">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-wrap items-center justify-center gap-6 text-gray-500 text-sm">
                        <span className="flex items-center gap-2">
                            <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                            </svg>
                            256-bit SSL Secured
                        </span>
                        <span className="flex items-center gap-2">
                            <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            SOC 2 Compliant
                        </span>
                        <span className="flex items-center gap-2">
                            <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            GDPR Ready
                        </span>
                        <span className="flex items-center gap-2">
                            <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7-.8 1.3z"/>
                            </svg>
                            99.9% Uptime
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    );
}
