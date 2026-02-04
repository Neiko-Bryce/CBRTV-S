import React from 'react';
import { motion } from 'framer-motion';
import { 
    HiCog, 
    HiClipboardList, 
    HiUser,
    HiCheckCircle
} from 'react-icons/hi';
import { MdAdminPanelSettings, MdSupervisorAccount, MdPerson } from 'react-icons/md';
import SectionTitle from '../ui/SectionTitle';
import Card from '../ui/Card';

const roles = [
    {
        icon: MdAdminPanelSettings,
        title: 'Administrator',
        subtitle: 'System Management',
        color: 'from-gov-green-700 to-gov-green-900',
        responsibilities: [
            'Configure system settings and security policies',
            'Manage user accounts and permissions',
            'Create and oversee multiple elections',
            'Access comprehensive audit logs',
            'Generate system-wide reports',
        ],
        permissions: [
            'Full system access',
            'User management',
            'Election creation',
            'Data export',
        ],
    },
    {
        icon: MdSupervisorAccount,
        title: 'Election Officer',
        subtitle: 'Election Management',
        color: 'from-gov-gold-500 to-gov-gold-600',
        responsibilities: [
            'Set up and configure individual elections',
            'Define ballot structure and candidates',
            'Monitor voting progress in real-time',
            'Manage voter eligibility lists',
            'Publish and certify election results',
        ],
        permissions: [
            'Election setup',
            'Candidate management',
            'Voter list control',
            'Result publication',
        ],
    },
    {
        icon: MdPerson,
        title: 'Voter',
        subtitle: 'Student / Citizen',
        color: 'from-blue-500 to-blue-600',
        responsibilities: [
            'View available elections and candidates',
            'Cast votes during open election periods',
            'Review personal voting history',
            'Access election results after closure',
            'Update personal profile information',
        ],
        permissions: [
            'Vote casting',
            'Result viewing',
            'Profile management',
            'History access',
        ],
    },
];

export default function UserRoles() {
    return (
        <section id="roles" className="py-16 sm:py-20 lg:py-24 xl:py-32 bg-gray-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <SectionTitle
                    subtitle="User Roles"
                    title="Role-Based Access Control"
                    description="Clear separation of responsibilities ensures security and accountability at every level of the voting process."
                />

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    {roles.map((role, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 40 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, margin: "-50px" }}
                            transition={{ duration: 0.5, delay: index * 0.15 }}
                        >
                            <Card className="h-full relative overflow-hidden" hover={false}>
                                {/* Header Gradient */}
                                <div className={`absolute top-0 left-0 right-0 h-32 bg-gradient-to-br ${role.color} opacity-10 rounded-t-2xl`} />
                                
                                <div className="relative">
                                    {/* Icon & Title */}
                                    <div className="flex items-start gap-4 mb-6">
                                        <motion.div
                                            whileHover={{ rotate: [0, -10, 10, 0] }}
                                            transition={{ duration: 0.5 }}
                                            className={`w-16 h-16 bg-gradient-to-br ${role.color} rounded-2xl flex items-center justify-center shadow-lg`}
                                        >
                                            <role.icon className="w-8 h-8 text-white" />
                                        </motion.div>
                                        <div>
                                            <h3 className="text-xl font-bold text-gray-900">{role.title}</h3>
                                            <p className="text-sm text-gray-500">{role.subtitle}</p>
                                        </div>
                                    </div>

                                    {/* Responsibilities */}
                                    <div className="mb-6">
                                        <h4 className="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                                            <HiClipboardList className="w-4 h-4" />
                                            Responsibilities
                                        </h4>
                                        <ul className="space-y-2">
                                            {role.responsibilities.map((item, idx) => (
                                                <motion.li
                                                    key={idx}
                                                    initial={{ opacity: 0, x: -10 }}
                                                    whileInView={{ opacity: 1, x: 0 }}
                                                    viewport={{ once: true }}
                                                    transition={{ delay: 0.2 + idx * 0.05 }}
                                                    className="flex items-start gap-2 text-sm text-gray-600"
                                                >
                                                    <span className="w-1.5 h-1.5 bg-gov-green-500 rounded-full mt-2 flex-shrink-0" />
                                                    {item}
                                                </motion.li>
                                            ))}
                                        </ul>
                                    </div>

                                    {/* Permissions */}
                                    <div className="pt-4 border-t border-gray-100">
                                        <h4 className="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3 flex items-center gap-2">
                                            <HiCog className="w-4 h-4" />
                                            Permissions
                                        </h4>
                                        <div className="flex flex-wrap gap-2">
                                            {role.permissions.map((perm, idx) => (
                                                <motion.span
                                                    key={idx}
                                                    initial={{ opacity: 0, scale: 0.8 }}
                                                    whileInView={{ opacity: 1, scale: 1 }}
                                                    viewport={{ once: true }}
                                                    transition={{ delay: 0.3 + idx * 0.05 }}
                                                    className="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-full"
                                                >
                                                    <HiCheckCircle className="w-3.5 h-3.5 text-gov-green-600" />
                                                    {perm}
                                                </motion.span>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </Card>
                        </motion.div>
                    ))}
                </div>

                {/* Role Comparison Note */}
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ delay: 0.5 }}
                    className="mt-12 text-center"
                >
                    <p className="text-gray-500 text-sm">
                        Custom roles and permissions can be configured based on your organization's specific requirements.
                    </p>
                </motion.div>
            </div>
        </section>
    );
}
