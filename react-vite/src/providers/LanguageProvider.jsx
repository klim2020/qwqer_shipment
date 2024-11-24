
import { createContext, useContext } from "react";
import React from 'react';


const LanguageContext = createContext();

function LanguageProvider({ children }) {


  const value = {
    hello:"world",
    data: {
        ...window.shipping_qwqer.langs
    },
    t:(key)=>{
        if (value.data[key] !== undefined){
            return value.data[key] 
        }
        return key;
    }
  }

  return (
    <LanguageContext.Provider value={ value }>{ children }</LanguageContext.Provider>
  );

}

function useLanguage() {
  const context = useContext(LanguageContext);
  if (context === undefined)
    throw new Error("PostContext was used outside of the PostProvider");
  return context;
}

export { LanguageProvider, useLanguage };