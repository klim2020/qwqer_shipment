import  React, { createContext, useContext } from "react";


const QwqerContext = createContext();

const DelayedCallback = {
    data:{}

}

function QwqerProvider({ children }) {


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