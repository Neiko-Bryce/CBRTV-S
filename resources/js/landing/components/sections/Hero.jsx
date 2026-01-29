import { motion } from 'framer-motion';
import { HiArrowRight, HiPlay } from 'react-icons/hi';
import { MdHowToVote, MdVerified, MdSpeed } from 'react-icons/md';
import Button from '../ui/Button';

export default function Hero() {
    const floatingIcons = [
        { icon: MdHowToVote, delay: 0, position: 'top-20 left-10' },
        { icon: MdVerified, delay: 0.5, position: 'top-40 right-20' },
        { icon: MdSpeed, delay: 1, position: 'bottom-32 left-20' },
    ];

    return (
        <section className="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-gov-green-900 via-gov-green-800 to-gov-green-950 w-full max-w-full">
            {/* Background Pattern */}
            <div className="absolute inset-0 opacity-10">
                <div className="absolute inset-0" style={{
                    backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
                }} />
            </div>

            {/* Floating Background Shapes - Hidden on mobile to prevent overflow */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none hidden sm:block">
                <motion.div
                    animate={{ 
                        y: [0, -30, 0],
                        rotate: [0, 5, 0],
                    }}
                    transition={{ duration: 8, repeat: Infinity, ease: 'easeInOut' }}
                    className="absolute top-1/4 right-1/4 w-64 h-64 lg:w-96 lg:h-96 bg-gov-gold-500/10 rounded-full blur-3xl"
                />
                <motion.div
                    animate={{ 
                        y: [0, 40, 0],
                        rotate: [0, -5, 0],
                    }}
                    transition={{ duration: 10, repeat: Infinity, ease: 'easeInOut' }}
                    className="absolute bottom-1/4 left-1/4 w-56 h-56 lg:w-80 lg:h-80 bg-gov-green-400/10 rounded-full blur-3xl"
                />
            </div>

            {/* Floating Icons - Hidden on mobile */}
            {floatingIcons.map((item, index) => (
                <motion.div
                    key={index}
                    initial={{ opacity: 0, scale: 0 }}
                    animate={{ 
                        opacity: 0.2, 
                        scale: 1,
                        y: [0, -20, 0],
                    }}
                    transition={{ 
                        delay: item.delay,
                        duration: 4,
                        y: { duration: 4, repeat: Infinity, ease: 'easeInOut' }
                    }}
                    className={`absolute ${item.position} hidden lg:block`}
                >
                    <item.icon className="w-16 h-16 text-white/30" />
                </motion.div>
            ))}

            <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 md:py-28 lg:py-32 xl:py-40">
                <div className="grid lg:grid-cols-2 gap-8 sm:gap-10 lg:gap-16 xl:gap-20 items-center">
                    {/* Content */}
                    <div className="text-center lg:text-left">
                        <motion.div
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.6 }}
                            className="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-3 py-1.5 sm:px-4 sm:py-2 rounded-full mb-4 sm:mb-6"
                        >
                            <span className="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gov-gold-400 rounded-full animate-pulse" />
                            <span className="text-white/90 text-xs sm:text-sm font-medium">
                                Central Philippine State University
                            </span>
                        </motion.div>

                        <motion.h1
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.6, delay: 0.1 }}
                            className="text-3xl xs:text-4xl sm:text-5xl lg:text-5xl xl:text-6xl 2xl:text-7xl font-bold text-white leading-tight mb-4 sm:mb-6"
                        >
                            Cloud Based{' '}
                            <span className="text-gov-gold-400">Real-Time</span>
                            <br className="hidden xs:block" />
                            <span className="xs:hidden"> </span>
                            Voting System
                        </motion.h1>

                        <motion.p
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.6, delay: 0.2 }}
                            className="text-base sm:text-lg lg:text-xl text-white/80 mb-6 sm:mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed px-2 sm:px-0"
                        >
                            A secure and transparent digital voting platform for CPSU 
                            student council elections. Experience democracy with complete 
                            transparency and instant results.
                        </motion.p>

                        <motion.div
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.6, delay: 0.3 }}
                            className="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start px-4 sm:px-0"
                        >
                            <Button 
                                variant="secondary" 
                                size="lg"
                                icon={HiArrowRight}
                                iconPosition="right"
                                className="w-full sm:w-auto justify-center text-sm sm:text-base"
                            >
                                Launch Demo
                            </Button>
                            <Button 
                                variant="outline" 
                                size="lg"
                                icon={HiPlay}
                                className="w-full sm:w-auto justify-center border-white/30 text-white hover:bg-white/10 hover:text-white text-sm sm:text-base"
                            >
                                Watch Overview
                            </Button>
                        </motion.div>

                        {/* Trust Badges - Responsive grid on mobile */}
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ duration: 0.6, delay: 0.5 }}
                            className="mt-8 sm:mt-12 grid grid-cols-1 xs:grid-cols-3 gap-3 sm:gap-4 lg:gap-6 justify-items-center lg:justify-items-start px-4 sm:px-0"
                        >
                            <div className="flex items-center gap-2 text-white/60">
                                <MdVerified className="w-4 h-4 sm:w-5 sm:h-5 text-gov-gold-400 flex-shrink-0" />
                                <span className="text-xs sm:text-sm whitespace-nowrap">Secure & Encrypted</span>
                            </div>
                            <div className="flex items-center gap-2 text-white/60">
                                <MdVerified className="w-4 h-4 sm:w-5 sm:h-5 text-gov-gold-400 flex-shrink-0" />
                                <span className="text-xs sm:text-sm whitespace-nowrap">Real-Time Results</span>
                            </div>
                            <div className="flex items-center gap-2 text-white/60">
                                <MdVerified className="w-4 h-4 sm:w-5 sm:h-5 text-gov-gold-400 flex-shrink-0" />
                                <span className="text-xs sm:text-sm whitespace-nowrap">Transparent Process</span>
                            </div>
                        </motion.div>
                    </div>

                    {/* Hero Illustration - Shows compact version on tablet, full on desktop */}
                    <motion.div
                        initial={{ opacity: 0, scale: 0.8, x: 50 }}
                        animate={{ opacity: 1, scale: 1, x: 0 }}
                        transition={{ duration: 0.8, delay: 0.3 }}
                        className="relative hidden md:block"
                    >
                        <div className="relative">
                            {/* Main Card - Responsive sizing */}
                            <motion.div
                                animate={{ y: [0, -15, 0] }}
                                transition={{ duration: 5, repeat: Infinity, ease: 'easeInOut' }}
                                className="bg-white rounded-2xl lg:rounded-3xl p-4 sm:p-6 lg:p-8 shadow-2xl"
                            >
                                <div className="flex items-center gap-3 lg:gap-4 mb-4 lg:mb-6">
                                    <div className="w-10 h-10 lg:w-14 lg:h-14 bg-gov-green-100 rounded-xl lg:rounded-2xl flex items-center justify-center flex-shrink-0">
                                        <MdHowToVote className="w-5 h-5 lg:w-8 lg:h-8 text-gov-green-800" />
                                    </div>
                                    <div className="min-w-0">
                                        <h3 className="text-sm lg:text-lg font-bold text-gray-900 truncate">Student Council Election</h3>
                                        <p className="text-xs lg:text-sm text-gray-500">Live Results</p>
                                    </div>
                                </div>
                                
                                {/* Progress Bars */}
                                <div className="space-y-3 lg:space-y-4">
                                    {[
                                        { name: 'Candidate A', votes: 342, percent: 45 },
                                        { name: 'Candidate B', votes: 287, percent: 38 },
                                        { name: 'Candidate C', votes: 128, percent: 17 },
                                    ].map((candidate, idx) => (
                                        <div key={idx} className="space-y-1.5 lg:space-y-2">
                                            <div className="flex justify-between text-xs lg:text-sm">
                                                <span className="font-medium text-gray-700">{candidate.name}</span>
                                                <span className="text-gray-500">{candidate.votes} <span className="hidden sm:inline">votes</span> ({candidate.percent}%)</span>
                                            </div>
                                            <div className="h-2 lg:h-3 bg-gray-100 rounded-full overflow-hidden">
                                                <motion.div
                                                    initial={{ width: 0 }}
                                                    animate={{ width: `${candidate.percent}%` }}
                                                    transition={{ duration: 1.5, delay: 0.5 + idx * 0.2 }}
                                                    className={`h-full rounded-full ${
                                                        idx === 0 ? 'bg-gov-green-600' : 
                                                        idx === 1 ? 'bg-gov-gold-500' : 'bg-gray-400'
                                                    }`}
                                                />
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <div className="mt-4 lg:mt-6 pt-3 lg:pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <div className="flex items-center gap-1.5 lg:gap-2">
                                        <span className="w-1.5 h-1.5 lg:w-2 lg:h-2 bg-green-500 rounded-full animate-pulse" />
                                        <span className="text-xs lg:text-sm text-gray-500">Live Updating</span>
                                    </div>
                                    <span className="text-xs lg:text-sm text-gray-500">757 total votes</span>
                                </div>
                            </motion.div>

                            {/* Floating Badge - Responsive positioning */}
                            <motion.div
                                initial={{ opacity: 0, scale: 0 }}
                                animate={{ opacity: 1, scale: 1 }}
                                transition={{ delay: 1, duration: 0.5 }}
                                className="absolute -top-2 -right-2 lg:-top-4 lg:-right-4 bg-gov-gold-500 text-gov-green-900 px-2.5 py-1 lg:px-4 lg:py-2 rounded-lg lg:rounded-xl shadow-lg font-bold text-xs lg:text-base"
                            >
                                Real-Time
                            </motion.div>

                            {/* Security Badge - Hidden on tablet, visible on desktop */}
                            <motion.div
                                initial={{ opacity: 0, x: -20 }}
                                animate={{ opacity: 1, x: 0 }}
                                transition={{ delay: 1.2, duration: 0.5 }}
                                className="absolute -bottom-3 -left-3 lg:-bottom-4 lg:-left-4 bg-white px-2.5 py-2 lg:px-4 lg:py-3 rounded-lg lg:rounded-xl shadow-lg hidden lg:flex items-center gap-2 lg:gap-3"
                            >
                                <div className="w-8 h-8 lg:w-10 lg:h-10 bg-green-100 rounded-md lg:rounded-lg flex items-center justify-center">
                                    <MdVerified className="w-4 h-4 lg:w-6 lg:h-6 text-green-600" />
                                </div>
                                <div>
                                    <p className="text-xs lg:text-sm font-semibold text-gray-900">Verified & Secure</p>
                                    <p className="text-[10px] lg:text-xs text-gray-500">End-to-end encrypted</p>
                                </div>
                            </motion.div>
                        </div>
                    </motion.div>
                </div>
            </div>

            {/* Scroll Indicator - Hidden on very small screens */}
            <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1, y: [0, 10, 0] }}
                transition={{ 
                    opacity: { delay: 1.5 },
                    y: { duration: 2, repeat: Infinity }
                }}
                className="absolute bottom-4 sm:bottom-8 left-1/2 -translate-x-1/2 hidden sm:block"
            >
                <div className="flex flex-col items-center gap-2">
                    <span className="text-white/50 text-xs sm:text-sm">Scroll to explore</span>
                    <div className="w-5 h-8 sm:w-6 sm:h-10 border-2 border-white/30 rounded-full flex justify-center pt-1.5 sm:pt-2">
                        <div className="w-1 h-2 sm:w-1.5 sm:h-3 bg-white/50 rounded-full" />
                    </div>
                </div>
            </motion.div>
        </section>
    );
}
