import React from 'react';
import { motion } from 'framer-motion';
import { 
    HiShieldCheck, 
    HiLockClosed, 
    HiDatabase, 
    HiDocumentText,
    HiServer,
    HiKey
} from 'react-icons/hi';
import SectionTitle from '../ui/SectionTitle';

const securityFeatures = [
    {
        icon: HiLockClosed,
        title: 'End-to-End Encryption',
        description: 'All data is encrypted using AES-256 encryption at rest and TLS 1.3 in transit, ensuring votes remain confidential.',
    },
    {
        icon: HiServer,
        title: 'Secure Cloud Hosting',
        description: 'Hosted on SOC 2 Type II compliant infrastructure with multiple availability zones and automatic failover.',
    },
    {
        icon: HiDatabase,
        title: 'Data Integrity',
        description: 'Cryptographic hash verification ensures every vote is recorded accurately and cannot be altered after submission.',
    },
    {
        icon: HiDocumentText,
        title: 'Complete Audit Trail',
        description: 'Every action is logged with timestamps and user identification for comprehensive post-election auditing.',
    },
    {
        icon: HiKey,
        title: 'Access Control',
        description: 'Role-based permissions and multi-factor authentication prevent unauthorized access to sensitive functions.',
    },
    {
        icon: HiShieldCheck,
        title: 'Compliance Ready',
        description: 'Built to meet GDPR, FERPA, and institutional data protection requirements for educational organizations.',
    },
];

const certifications = [
    { name: 'SOC 2', description: 'Type II Certified' },
    { name: 'GDPR', description: 'Compliant' },
    { name: 'ISO 27001', description: 'Security Standards' },
    { name: 'SSL/TLS', description: 'A+ Rating' },
];

export default function Security() {
    return (
        <section id="security" className="py-16 sm:py-20 lg:py-24 xl:py-32 bg-gradient-to-br from-gov-green-900 via-gov-green-800 to-gov-green-950 relative overflow-hidden">
            {/* Background Pattern */}
            <div className="absolute inset-0 opacity-5">
                <div className="absolute inset-0" style={{
                    backgroundImage: `url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='1' fill-rule='evenodd'/%3E%3C/svg%3E")`,
                }} />
            </div>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <SectionTitle
                    subtitle="Data Security & Privacy"
                    title="Enterprise-Grade Security"
                    description="Your data and your voters' privacy are protected by multiple layers of industry-leading security measures."
                    light
                />

                {/* Security Features Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-10 sm:mb-12 lg:mb-16">
                    {securityFeatures.map((feature, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 30 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ duration: 0.5, delay: index * 0.1 }}
                            whileHover={{ y: -5, scale: 1.02 }}
                            className="bg-white/5 backdrop-blur-sm rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-white/10 hover:border-gov-gold-500/30 transition-all duration-300"
                        >
                            <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gov-gold-500/20 rounded-lg sm:rounded-xl flex items-center justify-center mb-3 sm:mb-4">
                                <feature.icon className="w-5 h-5 sm:w-6 sm:h-6 text-gov-gold-400" />
                            </div>
                            <h3 className="text-base sm:text-lg font-bold text-white mb-1.5 sm:mb-2">{feature.title}</h3>
                            <p className="text-white/70 text-xs sm:text-sm leading-relaxed">{feature.description}</p>
                        </motion.div>
                    ))}
                </div>

                {/* Certifications */}
                <motion.div
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5 }}
                    className="bg-white/5 backdrop-blur-sm rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-12 border border-white/10"
                >
                    <div className="text-center mb-6 sm:mb-8">
                        <h3 className="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1.5 sm:mb-2">Security Certifications & Compliance</h3>
                        <p className="text-white/60 text-sm sm:text-base">Independently verified security and compliance standards</p>
                    </div>

                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
                        {certifications.map((cert, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, scale: 0.8 }}
                                whileInView={{ opacity: 1, scale: 1 }}
                                viewport={{ once: true }}
                                transition={{ delay: 0.2 + index * 0.1 }}
                                whileHover={{ scale: 1.05 }}
                                className="bg-white/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 lg:p-6 text-center border border-white/10 hover:border-gov-gold-500/50 transition-all"
                            >
                                <div className="w-10 h-10 sm:w-12 sm:h-12 lg:w-16 lg:h-16 bg-gov-gold-500/20 rounded-full flex items-center justify-center mx-auto mb-2 sm:mb-3 lg:mb-4">
                                    <HiShieldCheck className="w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-gov-gold-400" />
                                </div>
                                <p className="text-sm sm:text-base lg:text-xl font-bold text-white">{cert.name}</p>
                                <p className="text-white/60 text-[10px] sm:text-xs lg:text-sm">{cert.description}</p>
                            </motion.div>
                        ))}
                    </div>
                </motion.div>

                {/* Security Stats */}
                <motion.div
                    initial={{ opacity: 0 }}
                    whileInView={{ opacity: 1 }}
                    viewport={{ once: true }}
                    transition={{ delay: 0.3 }}
                    className="mt-8 sm:mt-10 lg:mt-12 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 lg:gap-16"
                >
                    {[
                        { value: '256-bit', label: 'Encryption' },
                        { value: '24/7', label: 'Monitoring' },
                        { value: '0', label: 'Data Breaches' },
                        { value: '99.99%', label: 'Uptime SLA' },
                    ].map((stat, index) => (
                        <div key={index} className="text-center">
                            <p className="text-xl sm:text-2xl lg:text-3xl xl:text-4xl font-bold text-gov-gold-400">{stat.value}</p>
                            <p className="text-white/60 text-xs sm:text-sm mt-0.5 sm:mt-1">{stat.label}</p>
                        </div>
                    ))}
                </motion.div>
            </div>
        </section>
    );
}
