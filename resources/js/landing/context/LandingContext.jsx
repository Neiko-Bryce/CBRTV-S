import React, { createContext, useContext, useState, useEffect } from 'react';

const LandingContext = createContext();

export function LandingProvider({ children }) {
    const [settings, setSettings] = useState({
        organization: null,
        school: null,
        about: {},
        features: {},
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchSettings = async () => {
            try {
                const schoolId = window.SCHOOL_CONTEXT?.id;
                const url = `/api/landing-page/settings${schoolId ? `?school_id=${schoolId}` : ''}`;
                const response = await fetch(url);
                const data = await response.json();
                setSettings({
                    organization: data.organization,
                    school: data.school,
                    about: data.about || {},
                    features: data.features || {},
                });
            } catch (error) {
                console.error('Error fetching landing settings:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchSettings();
    }, []);

    return (
        <LandingContext.Provider value={{ ...settings, loading }}>
            {children}
        </LandingContext.Provider>
    );
}

export function useLanding() {
    return useContext(LandingContext);
}
