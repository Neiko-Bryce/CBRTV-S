import { motion } from 'framer-motion';
import { HiCheckCircle, HiStar, HiTrendingUp } from 'react-icons/hi';
import { MdVerified, MdThumbUp, MdAutoGraph } from 'react-icons/md';
import SectionTitle from '../ui/SectionTitle';
import Button from '../ui/Button';

const trustPoints = [
    {
        icon: MdVerified,
        title: 'Verified Results',
        description: 'Every election result is cryptographically verified and independently auditable.',
    },
    {
        icon: MdThumbUp,
        title: 'Fair Process',
        description: 'Equal opportunity for all candidates with unbiased ballot ordering options.',
    },
    {
        icon: MdAutoGraph,
        title: 'Full Transparency',
        description: 'Complete visibility into the voting process from registration to result publication.',
    },
];

const stats = [
    { value: '99.9%', label: 'System Uptime', icon: HiTrendingUp },
    { value: '< 1s', label: 'Result Generation', icon: MdAutoGraph },
    { value: '500+', label: 'Happy Institutions', icon: HiStar },
    { value: '2M+', label: 'Successful Votes', icon: HiCheckCircle },
];

const testimonials = [
    {
        quote: "CivicVote transformed our student council elections. The real-time results feature kept everyone engaged, and the transparency gave legitimacy to the entire process.",
        author: "Dr. Sarah Mitchell",
        role: "Dean of Student Affairs",
        institution: "State University",
    },
    {
        quote: "The ease of setup and the comprehensive audit trails make compliance reporting a breeze. Our board elections have never been smoother.",
        author: "James Chen",
        role: "Executive Director",
        institution: "Community Association",
    },
];

export default function Trust() {
    return (
        <section className="py-16 sm:py-20 lg:py-24 xl:py-32 bg-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <SectionTitle
                    subtitle="Trust & Transparency"
                    title="Building Confidence in Every Vote"
                    description="Our commitment to fairness, integrity, and accountability ensures every election is trusted by all stakeholders."
                />

                {/* Trust Points */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-8 mb-12 sm:mb-16 lg:mb-20">
                    {trustPoints.map((point, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 30 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ delay: index * 0.15 }}
                            className="text-center"
                        >
                            <motion.div
                                whileHover={{ scale: 1.1, rotate: 5 }}
                                className="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-gov-green-100 to-gov-green-200 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6"
                            >
                                <point.icon className="w-7 h-7 sm:w-8 sm:h-8 lg:w-10 lg:h-10 text-gov-green-800" />
                            </motion.div>
                            <h3 className="text-lg sm:text-xl font-bold text-gray-900 mb-2 sm:mb-3">{point.title}</h3>
                            <p className="text-gray-600 leading-relaxed text-sm sm:text-base">{point.description}</p>
                        </motion.div>
                    ))}
                </div>

                {/* Stats Bar */}
                <motion.div
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    className="bg-gradient-to-r from-gov-green-800 to-gov-green-900 rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-12 mb-12 sm:mb-16 lg:mb-20"
                >
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                        {stats.map((stat, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, scale: 0.8 }}
                                whileInView={{ opacity: 1, scale: 1 }}
                                viewport={{ once: true }}
                                transition={{ delay: 0.2 + index * 0.1 }}
                                className="text-center"
                            >
                                <stat.icon className="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 text-gov-gold-400 mx-auto mb-2 sm:mb-3" />
                                <p className="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold text-white mb-1 sm:mb-2">{stat.value}</p>
                                <p className="text-white/70 text-xs sm:text-sm lg:text-base">{stat.label}</p>
                            </motion.div>
                        ))}
                    </div>
                </motion.div>

                {/* Testimonials */}
                <div className="mb-12 sm:mb-16">
                    <motion.h3
                        initial={{ opacity: 0 }}
                        whileInView={{ opacity: 1 }}
                        viewport={{ once: true }}
                        className="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 text-center mb-8 sm:mb-12"
                    >
                        Trusted by Leading Institutions
                    </motion.h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                        {testimonials.map((testimonial, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, x: index === 0 ? -30 : 30 }}
                                whileInView={{ opacity: 1, x: 0 }}
                                viewport={{ once: true }}
                                transition={{ delay: 0.2 }}
                                className="bg-gray-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 lg:p-8 border border-gray-100 relative"
                            >
                                {/* Quote Icon */}
                                <div className="absolute -top-3 sm:-top-4 left-5 sm:left-8">
                                    <div className="w-8 h-8 sm:w-10 sm:h-10 bg-gov-gold-500 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                                        <svg className="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                        </svg>
                                    </div>
                                </div>

                                <blockquote className="text-gray-700 text-sm sm:text-base lg:text-lg leading-relaxed mb-4 sm:mb-6 pt-3 sm:pt-4">
                                    "{testimonial.quote}"
                                </blockquote>

                                <div className="flex items-center gap-3 sm:gap-4">
                                    <div className="w-10 h-10 sm:w-12 sm:h-12 bg-gov-green-200 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span className="text-gov-green-800 font-bold text-sm sm:text-lg">
                                            {testimonial.author.split(' ').map(n => n[0]).join('')}
                                        </span>
                                    </div>
                                    <div className="min-w-0">
                                        <p className="font-semibold text-gray-900 text-sm sm:text-base truncate">{testimonial.author}</p>
                                        <p className="text-xs sm:text-sm text-gray-500 truncate">{testimonial.role}, {testimonial.institution}</p>
                                    </div>
                                </div>
                            </motion.div>
                        ))}
                    </div>
                </div>

                {/* CTA */}
                <motion.div
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    className="text-center bg-gradient-to-r from-gov-green-50 to-gov-gold-50 rounded-2xl sm:rounded-3xl p-6 sm:p-8 lg:p-12"
                >
                    <h3 className="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">
                        Ready to Transform Your Elections?
                    </h3>
                    <p className="text-gray-600 text-sm sm:text-base lg:text-lg mb-6 sm:mb-8 max-w-2xl mx-auto">
                        Join hundreds of institutions that trust Votewisely.cpsu for secure, transparent, 
                        and efficient elections. Get started with a free demo today.
                    </p>
                    <div className="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                        <Button variant="primary" size="lg" className="w-full sm:w-auto justify-center">
                            Request Demo
                        </Button>
                        <Button variant="outline" size="lg" className="w-full sm:w-auto justify-center">
                            Contact Sales
                        </Button>
                    </div>
                </motion.div>
            </div>
        </section>
    );
}
