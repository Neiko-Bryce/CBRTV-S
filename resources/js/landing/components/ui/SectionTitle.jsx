import { motion } from 'framer-motion';

export default function SectionTitle({
    subtitle,
    title,
    description,
    centered = true,
    light = false,
}) {
    return (
        <div className={`max-w-3xl ${centered ? 'mx-auto text-center' : ''} mb-16`}>
            {subtitle && (
                <motion.span
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ duration: 0.5 }}
                    className={`
                        inline-block px-4 py-1.5 rounded-full text-sm font-semibold mb-4
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
                    text-3xl md:text-4xl lg:text-5xl font-bold mb-6
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
                        text-lg md:text-xl leading-relaxed
                        ${light ? 'text-gray-300' : 'text-gray-600'}
                    `}
                >
                    {description}
                </motion.p>
            )}
        </div>
    );
}
