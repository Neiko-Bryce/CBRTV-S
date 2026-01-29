import { motion } from 'framer-motion';

export default function Card({
    children,
    className = '',
    hover = true,
    gradient = false,
    ...props
}) {
    return (
        <motion.div
            whileHover={hover ? { y: -8, scale: 1.02 } : {}}
            transition={{ duration: 0.3, ease: 'easeOut' }}
            className={`
                bg-white rounded-2xl p-6 
                shadow-lg shadow-gray-200/50
                border border-gray-100
                ${gradient ? 'bg-gradient-to-br from-white to-gray-50' : ''}
                ${hover ? 'cursor-pointer' : ''}
                transition-shadow duration-300
                hover:shadow-xl hover:shadow-gray-200/60
                ${className}
            `}
            {...props}
        >
            {children}
        </motion.div>
    );
}
