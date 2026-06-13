import React, { createContext, useContext, useState } from 'react';
import en from '../translations/en.json';
import ta from '../translations/ta.json';
import hi from '../translations/hi.json';
import te from '../translations/te.json';

const translations = { en, ta, hi, te };

const LanguageContext = createContext(null);

export const LanguageProvider = ({ children }) => {
  const [language, setLanguageState] = useState(() => {
    return localStorage.getItem('language') || 'en';
  });

  const setLanguage = (lang) => {
    if (translations[lang]) {
      setLanguageState(lang);
      localStorage.setItem('language', lang);
    }
  };

  const t = (key) => {
    const dict = translations[language] || translations['en'];
    // Fallback chain: selected language key -> english key -> raw key
    return dict[key] || translations['en'][key] || key;
  };

  return (
    <LanguageContext.Provider value={{ language, setLanguage, t }}>
      {children}
    </LanguageContext.Provider>
  );
};

export const useLanguage = () => useContext(LanguageContext);
