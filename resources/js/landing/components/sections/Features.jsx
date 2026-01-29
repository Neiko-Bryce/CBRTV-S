import { motion } from 'framer-motion';
import { 
    HiChartBar, 
    HiCloud, 
    HiDocumentReport, 
    HiDeviceMobile,
    HiShieldCheck,
    HiClock
} from 'react-icons/hi';
import SectionTitle from '../ui/SectionTitle';
import Card from '../ui/Card';
import IconBox from '../ui/IconBox';

const features = [
    {
        icon: HiChartBar,
        title: 'Real-Time Vote Tallying',
        description: 'Watch results update live as votes are cast. Our instant counting system ensures transparency and eliminates waiting periods.',
        color: 'primary',
    },
    {
        icon: HiCloud,
        title: 'Secure Cloud Infrastructure',
        description: 'Built on enterprise-grade cloud servers with automatic scaling, redundancy, and 99.9% uptime guarantee.',
        color: 'gradient',
    },
    {
        icon: HiDocumentReport,
        title: 'Audit Logs & Reports',
        description: 'Comprehensive audit trails for every action. Generate detailed transparency reports for stakeholder review.',
        color: 'secondary',
    },
    {
        icon: HiDeviceMobile,
        title: 'Multi-Device Access',
        description: 'Vote from any device - desktop, tablet, or mobile. Responsive design ensures a seamless experience everywhere.',
        color: 'outline',
    },
    {
        icon: HiShieldCheck,
        title: 'Advanced Security',
        description: 'End-to-end encryption, multi-factor authentication, and advanced threat protection keep your elections secure.',
        color: 'primary',
    },
    {
        icon: HiClock,
        title: 'Automated Scheduling',
        description: 'Set up elections in advance with automatic start/end times, voter notifications, and result announcements.',
        color: 'gradient',
    },
];

const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
        opacity: 1,
        transition: {
            staggerChildren: 0.1,
        },
    },
};

const itemVariants = {
    hidden: { opacity: 0, y: 30 },
    visible: {
        opacity: 1,
        y: 0,
        transition: { duration: 0.5 },
    },
};

export default function Features() {
    return (
        <section id="features" className="py-16 sm:py-20 lg:py-24 xl:py-32 bg-gray-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <SectionTitle
                    subtitle="Core Features"
                    title="Everything You Need for Fair Elections"
                    description="Our comprehensive feature set ensures secure, transparent, and efficient elections for organizations of all sizes."
                />

                <motion.div
                    variants={containerVariants}
                    initial="hidden"
                    whileInView="visible"
                    viewport={{ once: true, margin: "-100px" }}
                    className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8"
                >
                    {features.map((feature, index) => (
                        <motion.div key={index} variants={itemVariants}>
                            <Card className="h-full" gradient>
                                <IconBox 
                                    icon={feature.icon} 
                                    variant={feature.color} 
                                    size="lg" 
                                    className="mb-6"
                                />
                                <h3 className="text-xl font-bold text-gray-900 mb-3">
                                    {feature.title}
                                </h3>
                                <p className="text-gray-600 leading-relaxed">
                                    {feature.description}
                                </p>
                            </Card>
                        </motion.div>
                    ))}
                </motion.div>

                {/* Feature Highlight */}
                <motion.div
                    initial={{ opacity: 0, y: 40 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.6 }}
                    className="mt-12 sm:mt-16 lg:mt-20 bg-gradient-to-r from-gov-green-800 to-gov-green-900 rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-12 overflow-hidden relative"
                >
                    {/* Background Pattern */}
                    <div className="absolute inset-0 opacity-10">
                        <div className="absolute inset-0" style={{
                            backgroundImage: `radial-gradient(circle at 2px 2px, white 1px, transparent 0)`,
                            backgroundSize: '32px 32px',
                        }} />
                    </div>

                    <div className="relative grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 items-center">
                        <div>
                            <span className="inline-block bg-gov-gold-500 text-gov-green-900 text-xs sm:text-sm font-semibold px-3 py-1 sm:px-4 sm:py-1.5 rounded-full mb-3 sm:mb-4">
                                Premium Feature
                            </span>
                            <h3 className="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 sm:mb-4">
                                Real-Time Analytics Dashboard
                            </h3>
                            <p className="text-white/80 text-sm sm:text-base lg:text-lg mb-4 sm:mb-6 leading-relaxed">
                                Monitor election progress with our powerful analytics dashboard. 
                                Track voter turnout, geographic distribution, and voting patterns 
                                in real-time.
                            </p>
                            <ul className="space-y-2 sm:space-y-3">
                                {[
                                    'Live participation metrics',
                                    'Demographic breakdowns',
                                    'Exportable reports in multiple formats',
                                    'Custom dashboard widgets',
                                ].map((item, idx) => (
                                    <li key={idx} className="flex items-center gap-2 sm:gap-3 text-white/90 text-sm sm:text-base">
                                        <span className="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gov-gold-400 rounded-full flex-shrink-0" />
                                        {item}
                                    </li>
                                ))}
                            </ul>
                        </div>

                        <div className="relative mt-4 lg:mt-0">
                            <motion.div
                                animate={{ y: [0, -10, 0] }}
                                transition={{ duration: 4, repeat: Infinity }}
                                className="bg-white/10 backdrop-blur-sm rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-white/20"
                            >
                                {/* Mini Dashboard Preview */}
                                <div className="grid grid-cols-2 gap-3 sm:gap-4 mb-3 sm:mb-4">
                                    <div className="bg-white/10 rounded-lg sm:rounded-xl p-3 sm:p-4">
                                        <p className="text-white/60 text-xs sm:text-sm">Total Votes</p>
                                        <p className="text-xl sm:text-2xl font-bold text-white">12,847</p>
                                    </div>
                                    <div className="bg-white/10 rounded-lg sm:rounded-xl p-3 sm:p-4">
                                        <p className="text-white/60 text-xs sm:text-sm">Turnout</p>
                                        <p className="text-xl sm:text-2xl font-bold text-gov-gold-400">78.4%</p>
                                    </div>
                                </div>
                                <div className="bg-white/10 rounded-lg sm:rounded-xl p-3 sm:p-4">
                                    <p className="text-white/60 text-xs sm:text-sm mb-2 sm:mb-3">Hourly Activity</p>
                                    <div className="flex items-end gap-1 h-12 sm:h-16">
                                        {[30, 45, 60, 80, 65, 90, 75, 85].map((height, idx) => (
                                            <motion.div
                                                key={idx}
                                                initial={{ height: 0 }}
                                                whileInView={{ height: `${height}%` }}
                                                viewport={{ once: true }}
                                                transition={{ delay: idx * 0.1 }}
                                                className="flex-1 bg-gov-gold-400/80 rounded-t"
                                            />
                                        ))}
                                    </div>
                                </div>
                            </motion.div>
                        </div>
                    </div>
                </motion.div>
            </div>
        </section>
    );
}
