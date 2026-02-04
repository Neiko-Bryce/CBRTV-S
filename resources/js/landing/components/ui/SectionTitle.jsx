import React from 'react';
import { motion } from 'framer-motion';

export default function SectionTitle({
    subtitle,
    title,
    description,
    centered = true,
    light = false,
}) {
    return (
        <div className={`max-w-3xl ${centered ? 'mx-auto text-center' : ''} mb-8 sm:mb-12 lg:mb-16 px-2 sm:px-0`}>
            {subtitle && (
                <motion.span
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5 }}
                    className={`
                        inline-block px-3 py-1 sm:px-4 sm:py-1.5 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4
                        ${light 
                            ? 'bg-white/10 text-white' 
                            : 'bg-gov-green-100 text-gov-green-800'
                        }
                    `}
                >
                    {subtitle}
                </motion.span>
            )}
            <motion.h2
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: 0.1 }}
                className={`
                    text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 lg:mb-6
                    ${light ? 'text-white' : 'text-gray-900'}
                `}
            >
                {title}
            </motion.h2>
            {description && (
                <motion.p
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5, delay: 0.2 }}
                    className={`
                        text-sm sm:text-base md:text-lg lg:text-xl leading-relaxed
                        ${light ? 'text-gray-300' : 'text-gray-600'}
                    `}
                >
                    {description}
                </motion.p>
            )}
        </div>
    );
}
