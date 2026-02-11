import React, { createContext, useContext, useState, useEffect } from 'react';

const LandingContext = createContext();

export function LandingProvider({ children }) {
    const [settings, setSettings] = useState({
        organization: null,
        about: {},
        features: {},
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchSettings = async () => {
            try {
                const response = await fetch('/api/landing-page/settings');
                const data = await response.json();
                setSettings({
                    organization: data.organization,
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
