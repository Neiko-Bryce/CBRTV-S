import React from 'react';
import {
    UsersIcon,
    CheckBadgeIcon,
    SparklesIcon
} from '@heroicons/react/24/outline';
import { useLanding } from '../../context/LandingContext';

export default function About() {
    const { about, loading } = useLanding();

    const settings = {
        subtitle: about.subtitle?.value || null,
        title: about.title?.value || null,
        description: about.description?.value || null,
        benefits: about.benefits?.extra || null,
        team_section_title: about.team_section_title?.value || null,
        team_section_subtitle: about.team_section_subtitle?.value || null,
        team_members: about.team_members?.extra || null,
    };

    if (loading) {
        return (
            <section className="py-20 bg-gradient-to-b from-gray-50 to-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="animate-pulse space-y-8">
                        <div className="h-12 bg-gray-200 rounded w-1/3 mx-auto"></div>
                        <div className="h-6 bg-gray-200 rounded w-2/3 mx-auto"></div>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                            {[1, 2, 3].map((i) => (
                                <div key={i} className="h-64 bg-gray-200 rounded-xl"></div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>
        );
    }

    // Get settings with fallbacks
    const subtitle = settings.subtitle || 'About The System';
    const title = settings.title || 'Redefining Digital Democracy';
    const description = settings.description ||
        'CpsuVotewisely.com is a comprehensive cloud-based voting platform that brings transparency, security, and real-time capabilities to student council elections.';
    const benefits = settings.benefits || [
        'Instant vote counting with live result updates',
        'Complete audit trail for every election',
        'Mobile-friendly voting from any device',
        'Customizable election rules and workflows',
        'Automated voter verification system',
        'Detailed analytics and reporting',
    ];

    // Team members data
    const teamSectionTitle = settings.team_section_title || 'Meet The Team';
    const teamSectionSubtitle = settings.team_section_subtitle || 'The dedicated team behind this voting system';
    const teamMembers = settings.team_members || [];

    return (
        <section id="about" className="py-20 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden">
            {/* Background Pattern */}
            <div className="absolute inset-0 opacity-5">
                <svg className="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" strokeWidth="0.5" />
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                {/* Header */}
                <div className="text-center mb-16">
                    <span className="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold mb-4">
                        <SparklesIcon className="w-4 h-4 mr-2" />
                        {subtitle}
                    </span>
                    <h2 className="text-4xl md:text-5xl font-bold text-gray-900 heading-font mb-6">
                        {title}
                    </h2>
                    <p className="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                        {description}
                    </p>
                </div>

                {/* Benefits Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {benefits.map((benefit, index) => (
                        <div
                            key={index}
                            className="flex items-start p-5 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 hover:-translate-y-1"
                        >
                            <div className="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                <CheckBadgeIcon className="w-6 h-6 text-green-600" />
                            </div>
                            <p className="text-gray-700 font-medium leading-relaxed">{benefit}</p>
                        </div>
                    ))}
                </div>

                {/* Team Members Section */}
                {teamMembers.length > 0 && (
                    <div className="mt-20 pt-16 border-t border-gray-200">
                        <div className="text-center mb-12">
                            <h3 className="text-3xl md:text-4xl font-bold text-gray-900 heading-font mb-4">
                                {teamSectionTitle}
                            </h3>
                            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                                {teamSectionSubtitle}
                            </p>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            {teamMembers.map((member, index) => (
                                <div
                                    key={index}
                                    className="group text-center"
                                >
                                    <div className="relative inline-block mb-4">
                                        {/* Circular Photo Frame */}
                                        <div className="w-40 h-40 rounded-full overflow-hidden border-4 border-white shadow-lg mx-auto group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                                            {member.image ? (
                                                <img
                                                    src={`/storage/${member.image}`}
                                                    alt={member.name || 'Team Member'}
                                                    className="w-full h-full object-cover"
                                                />
                                            ) : (
                                                <div className="w-full h-full bg-gradient-to-br from-gray-100 to-gray-300 flex items-center justify-center">
                                                    <UsersIcon className="w-16 h-16 text-gray-400" />
                                                </div>
                                            )}
                                        </div>
                                        {/* Decorative ring on hover */}
                                        <div className="absolute inset-0 rounded-full border-4 border-green-400 opacity-0 group-hover:opacity-50 transition-opacity duration-300 -m-1"></div>
                                    </div>

                                    <h4 className="text-xl font-bold text-gray-800 mb-1 group-hover:text-green-600 transition-colors">
                                        {member.name || 'Team Member'}
                                    </h4>
                                    {member.role && (
                                        <p className="text-sm font-semibold text-green-600 mb-2">
                                            {member.role}
                                        </p>
                                    )}
                                    {member.bio && (
                                        <p className="text-sm text-gray-500 px-4">
                                            {member.bio}
                                        </p>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </section>
    );
}
