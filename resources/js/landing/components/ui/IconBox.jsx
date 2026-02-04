import React from 'react';
import { motion } from 'framer-motion';

export default function IconBox({
    icon: Icon,
    variant = 'primary',
    size = 'md',
    className = '',
}) {
    const variants = {
        primary: 'bg-gov-green-800 text-white',
        secondary: 'bg-gov-gold-500 text-gov-green-900',
        outline: 'bg-gov-green-100 text-gov-green-800 border-2 border-gov-green-200',
        gradient: 'bg-gradient-to-br from-gov-green-700 to-gov-green-900 text-white',
    };

    const sizes = {
        sm: 'w-10 h-10',
        md: 'w-14 h-14',
        lg: 'w-16 h-16',
        xl: 'w-20 h-20',
    };

    const iconSizes = {
        sm: 'w-5 h-5',
        md: 'w-7 h-7',
        lg: 'w-8 h-8',
        xl: 'w-10 h-10',
    };

    return (
        <motion.div
            whileHover={{ rotate: [0, -10, 10, 0] }}
            transition={{ duration: 0.5 }}
            className={`
                ${sizes[size]} 
                ${variants[variant]} 
                rounded-xl flex items-center justify-center
                shadow-lg
                ${className}
            `}
        >
            <Icon className={iconSizes[size]} />
        </motion.div>
    );
}
