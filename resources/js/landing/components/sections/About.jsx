import { motion } from 'framer-motion';
import { HiCheckCircle } from 'react-icons/hi';
import { MdSchool, MdGroups, MdAccountBalance } from 'react-icons/md';
import SectionTitle from '../ui/SectionTitle';
import IconBox from '../ui/IconBox';

const benefits = [
    'Instant vote counting with live result updates',
    'Complete audit trail for every election',
    'Mobile-friendly voting from any device',
    'Customizable election rules and workflows',
    'Automated voter verification system',
    'Detailed analytics and reporting',
];

const useCases = [
    {
        icon: MdSchool,
        title: 'Educational Institutions',
        description: 'Perfect for student council elections, class representatives, and academic committee voting.',
    },
    {
        icon: MdGroups,
        title: 'Community Organizations',
        description: 'Ideal for homeowner associations, clubs, unions, and community-based decision making.',
    },
    {
        icon: MdAccountBalance,
        title: 'Government Bodies',
        description: 'Designed for local government polls, advisory boards, and civic engagement initiatives.',
    },
];

export default function About() {
    return (
        <section id="about" className="py-24 lg:py-32 bg-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <SectionTitle
                    subtitle="About The System"
                    title="Redefining Digital Democracy"
                    description="CivicVote is a comprehensive cloud-based voting platform that brings transparency, security, and real-time capabilities to institutional elections and community decision-making."
                />

                <div className="grid lg:grid-cols-2 gap-16 items-center mt-16">
                    {/* Left: Image/Illustration */}
                    <motion.div
                        initial={{ opacity: 0, x: -50 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.6 }}
                        className="relative"
                    >
                        <div className="relative bg-gradient-to-br from-gov-green-50 to-gov-green-100 rounded-3xl p-8 lg:p-12">
                            {/* Stats Grid */}
                            <div className="grid grid-cols-2 gap-6">
                                <motion.div
                                    whileHover={{ scale: 1.05 }}
                                    className="bg-white rounded-2xl p-6 shadow-lg"
                                >
                                    <p className="text-4xl font-bold text-gov-green-800">500+</p>
                                    <p className="text-gray-600 mt-1">Institutions</p>
                                </motion.div>
                                <motion.div
                                    whileHover={{ scale: 1.05 }}
                                    className="bg-white rounded-2xl p-6 shadow-lg"
                                >
                                    <p className="text-4xl font-bold text-gov-green-800">2M+</p>
                                    <p className="text-gray-600 mt-1">Votes Cast</p>
                                </motion.div>
                                <motion.div
                                    whileHover={{ scale: 1.05 }}
                                    className="bg-white rounded-2xl p-6 shadow-lg"
                                >
                                    <p className="text-4xl font-bold text-gov-green-800">99.9%</p>
                                    <p className="text-gray-600 mt-1">Uptime</p>
                                </motion.div>
                                <motion.div
                                    whileHover={{ scale: 1.05 }}
                                    className="bg-white rounded-2xl p-6 shadow-lg"
                                >
                                    <p className="text-4xl font-bold text-gov-gold-600">0</p>
                                    <p className="text-gray-600 mt-1">Security Breaches</p>
                                </motion.div>
                            </div>

                            {/* Decorative Elements */}
                            <div className="absolute -top-4 -right-4 w-24 h-24 bg-gov-gold-400/20 rounded-full blur-2xl" />
                            <div className="absolute -bottom-4 -left-4 w-32 h-32 bg-gov-green-400/20 rounded-full blur-2xl" />
                        </div>
                    </motion.div>

                    {/* Right: Content */}
                    <motion.div
                        initial={{ opacity: 0, x: 50 }}
                        whileInView={{ opacity: 1, x: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.6 }}
                    >
                        <h3 className="text-2xl font-bold text-gray-900 mb-6">
                            Built for Trust and Transparency
                        </h3>
                        <p className="text-gray-600 mb-8 leading-relaxed">
                            Our platform empowers organizations to conduct fair, transparent, and 
                            efficient elections. With real-time vote counting, comprehensive audit 
                            logs, and bank-grade security, CivicVote ensures every voice is heard 
                            and every vote counts.
                        </p>

                        {/* Benefits List */}
                        <div className="grid sm:grid-cols-2 gap-4">
                            {benefits.map((benefit, index) => (
                                <motion.div
                                    key={index}
                                    initial={{ opacity: 0, y: 20 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ delay: index * 0.1 }}
                                    className="flex items-start gap-3"
                                >
                                    <HiCheckCircle className="w-6 h-6 text-gov-green-600 flex-shrink-0 mt-0.5" />
                                    <span className="text-gray-700">{benefit}</span>
                                </motion.div>
                            ))}
                        </div>
                    </motion.div>
                </div>

                {/* Use Cases */}
                <div className="mt-24">
                    <motion.h3
                        initial={{ opacity: 0, y: 20 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        className="text-2xl font-bold text-gray-900 text-center mb-12"
                    >
                        Designed For Every Organization
                    </motion.h3>

                    <div className="grid md:grid-cols-3 gap-8">
                        {useCases.map((useCase, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ delay: index * 0.15 }}
                                whileHover={{ y: -8 }}
                                className="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:border-gov-green-200 transition-colors"
                            >
                                <IconBox icon={useCase.icon} variant="gradient" size="lg" className="mb-6" />
                                <h4 className="text-xl font-bold text-gray-900 mb-3">{useCase.title}</h4>
                                <p className="text-gray-600 leading-relaxed">{useCase.description}</p>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
