import React from 'react';
import { motion } from 'framer-motion';

const variants = {
    primary: 'bg-gov-green-800 hover:bg-gov-green-900 text-white shadow-lg shadow-gov-green-800/25',
    secondary: 'bg-gov-gold-500 hover:bg-gov-gold-600 text-gov-green-900 shadow-lg shadow-gov-gold-500/25',
    outline: 'border-2 border-gov-green-800 text-gov-green-800 hover:bg-gov-green-800 hover:text-white',
    ghost: 'text-gov-green-800 hover:bg-gov-green-100',
};

const sizes = {
    sm: 'px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm',
    md: 'px-4 py-2.5 sm:px-6 sm:py-3 text-sm sm:text-base',
    lg: 'px-6 py-3 sm:px-8 sm:py-4 text-sm sm:text-base lg:text-lg',
};

const iconSizes = {
    sm: 'w-4 h-4',
    md: 'w-4 h-4 sm:w-5 sm:h-5',
    lg: 'w-4 h-4 sm:w-5 sm:h-5',
};

export default function Button({
    children,
    variant = 'primary',
    size = 'md',
    className = '',
    icon: Icon,
    iconPosition = 'left',
    ...props
}) {
    return (
        <motion.button
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            className={`
                inline-flex items-center justify-center gap-1.5 sm:gap-2 
                font-semibold rounded-lg sm:rounded-xl transition-all duration-300
                focus:outline-none focus:ring-4 focus:ring-gov-green-500/20
                min-h-[44px] touch-manipulation
                ${variants[variant]} 
                ${sizes[size]} 
                ${className}
            `}
            {...props}
        >
            {Icon && iconPosition === 'left' && <Icon className={iconSizes[size]} />}
            {children}
            {Icon && iconPosition === 'right' && <Icon className={iconSizes[size]} />}
        </motion.button>
    );
}
