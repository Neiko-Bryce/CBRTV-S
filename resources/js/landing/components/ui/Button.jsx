import { motion } from 'framer-motion';

const variants = {
    primary: 'bg-gov-green-800 hover:bg-gov-green-900 text-white shadow-lg shadow-gov-green-800/25',
    secondary: 'bg-gov-gold-500 hover:bg-gov-gold-600 text-gov-green-900 shadow-lg shadow-gov-gold-500/25',
    outline: 'border-2 border-gov-green-800 text-gov-green-800 hover:bg-gov-green-800 hover:text-white',
    ghost: 'text-gov-green-800 hover:bg-gov-green-100',
};

const sizes = {
    sm: 'px-4 py-2 text-sm',
    md: 'px-6 py-3 text-base',
    lg: 'px-8 py-4 text-lg',
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
                inline-flex items-center justify-center gap-2 
                font-semibold rounded-xl transition-all duration-300
                focus:outline-none focus:ring-4 focus:ring-gov-green-500/20
                ${variants[variant]} 
                ${sizes[size]} 
                ${className}
            `}
            {...props}
        >
            {Icon && iconPosition === 'left' && <Icon className="w-5 h-5" />}
            {children}
            {Icon && iconPosition === 'right' && <Icon className="w-5 h-5" />}
        </motion.button>
    );
}
