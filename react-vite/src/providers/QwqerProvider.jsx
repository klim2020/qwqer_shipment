import  React, { createContext, useContext } from "react";


const QwqerContext = createContext();

const DelayedCallback = {
    data:{}

}

function QwqerProvider({ children }) {
  if (typeof window.shipping_qwqer === 'undefined'){
    console.error("QwqerProvider - shipping_qwer is undefined");
  }

  const value = {
    qwqer:window.shipping_qwqer
  }

  return (
    <QwqerContext.Provider value={ value }>{ children }</QwqerContext.Provider>
  );

}

function useQwqer() {
  const context = useContext(QwqerContext);
  if (context === undefined)
    throw new Error("PostContext was used outside of the PostProvider");
  return context;
}

export { QwqerProvider, useQwqer };