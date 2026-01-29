import { motion } from 'framer-motion';
import { 
    HiUserAdd, 
    HiFingerPrint, 
    HiClipboardCheck, 
    HiPresentationChartBar 
} from 'react-icons/hi';
import SectionTitle from '../ui/SectionTitle';

const steps = [
    {
        number: '01',
        icon: HiUserAdd,
        title: 'Voter Registration & Verification',
        description: 'Eligible voters are registered and verified through secure identity validation. Each voter receives unique credentials for authentication.',
        details: [
            'Bulk import from existing systems',
            'Email/SMS verification',
            'ID document verification',
            'Role-based access assignment',
        ],
    },
    {
        number: '02',
        icon: HiFingerPrint,
        title: 'Secure Authentication',
        description: 'Voters authenticate using multi-factor authentication to ensure only authorized individuals can cast their votes.',
        details: [
            'Two-factor authentication',
            'Biometric options available',
            'Session management',
            'Anti-fraud detection',
        ],
    },
    {
        number: '03',
        icon: HiClipboardCheck,
        title: 'Cast Your Vote',
        description: 'Navigate the intuitive ballot interface to make your selections. Review and confirm before submitting your encrypted vote.',
        details: [
            'User-friendly ballot design',
            'Accessibility features',
            'Vote preview & confirmation',
            'Encrypted submission',
        ],
    },
    {
        number: '04',
        icon: HiPresentationChartBar,
        title: 'Instant Results & Reports',
        description: 'Once the election closes, results are calculated instantly. Comprehensive reports are generated for transparency.',
        details: [
            'Real-time result updates',
            'Visual result dashboards',
            'Exportable reports',
            'Audit trail access',
        ],
    },
];

export default function HowItWorks() {
    return (
        <section id="how-it-works" className="py-24 lg:py-32 bg-white overflow-hidden">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <SectionTitle
                    subtitle="How It Works"
                    title="Simple, Secure Voting Process"
                    description="Our streamlined four-step process ensures every election runs smoothly from start to finish."
                />

                <div className="relative">
                    {/* Connection Line - Desktop */}
                    <div className="hidden lg:block absolute top-1/2 left-0 right-0 h-1 bg-gradient-to-r from-gov-green-200 via-gov-green-400 to-gov-green-200 -translate-y-1/2 z-0" />

                    <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 relative z-10">
                        {steps.map((step, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, y: 50 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, margin: "-50px" }}
                                transition={{ duration: 0.5, delay: index * 0.15 }}
                                className="relative"
                            >
                                {/* Step Card */}
                                <div className="bg-white rounded-2xl p-6 lg:p-8 shadow-lg border border-gray-100 hover:border-gov-green-200 transition-all duration-300 hover:shadow-xl group">
                                    {/* Step Number Badge */}
                                    <div className="absolute -top-4 left-6 lg:left-1/2 lg:-translate-x-1/2 bg-gov-green-800 text-white text-sm font-bold px-4 py-1.5 rounded-full shadow-lg">
                                        Step {step.number}
                                    </div>

                                    {/* Icon */}
                                    <motion.div
                                        whileHover={{ scale: 1.1, rotate: 5 }}
                                        className="w-16 h-16 bg-gradient-to-br from-gov-green-100 to-gov-green-200 rounded-2xl flex items-center justify-center mx-auto mt-4 mb-6 group-hover:from-gov-green-700 group-hover:to-gov-green-900 transition-all duration-300"
                                    >
                                        <step.icon className="w-8 h-8 text-gov-green-800 group-hover:text-white transition-colors duration-300" />
                                    </motion.div>

                                    {/* Content */}
                                    <h3 className="text-xl font-bold text-gray-900 text-center mb-3">
                                        {step.title}
                                    </h3>
                                    <p className="text-gray-600 text-center mb-6 leading-relaxed">
                                        {step.description}
                                    </p>

                                    {/* Details */}
                                    <ul className="space-y-2">
                                        {step.details.map((detail, idx) => (
                                            <motion.li
                                                key={idx}
                                                initial={{ opacity: 0, x: -10 }}
                                                whileInView={{ opacity: 1, x: 0 }}
                                                viewport={{ once: true }}
                                                transition={{ delay: 0.3 + idx * 0.1 }}
                                                className="flex items-center gap-2 text-sm text-gray-600"
                                            >
                                                <span className="w-1.5 h-1.5 bg-gov-gold-500 rounded-full flex-shrink-0" />
                                                {detail}
                                            </motion.li>
                                        ))}
                                    </ul>
                                </div>

                                {/* Arrow for Desktop */}
                                {index < steps.length - 1 && (
                                    <div className="hidden lg:block absolute top-1/2 -right-3 -translate-y-1/2 z-20">
                                        <motion.div
                                            animate={{ x: [0, 5, 0] }}
                                            transition={{ duration: 1.5, repeat: Infinity }}
                                            className="w-6 h-6 bg-gov-green-600 rounded-full flex items-center justify-center"
                                        >
                                            <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M9 5l7 7-7 7" />
                                            </svg>
                                        </motion.div>
                                    </div>
                                )}
                            </motion.div>
                        ))}
                    </div>
                </div>

                {/* Bottom CTA */}
                <motion.div
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, delay: 0.5 }}
                    className="mt-16 text-center"
                >
                    <p className="text-gray-600 mb-6">
                        Ready to experience seamless voting?
                    </p>
                    <motion.button
                        whileHover={{ scale: 1.05 }}
                        whileTap={{ scale: 0.95 }}
                        className="inline-flex items-center gap-2 bg-gov-green-800 hover:bg-gov-green-900 text-white font-semibold px-8 py-4 rounded-xl shadow-lg shadow-gov-green-800/25 transition-colors"
                    >
                        Try Demo Election
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </motion.button>
                </motion.div>
            </div>
        </section>
    );
}
