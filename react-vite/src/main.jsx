import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import { createTheme, ThemeProvider } from "@mui/material/styles";
import { QwqerProvider } from "./providers/QwqerProvider";
//import reportWebVitals from './reportWebVitals';

//import { createTheme } from '@mui/material/styles';



const theme = createTheme({
  palette: {
    secondary: {
      main: '#8373ce',
      contrastText: "#ffff !important"
    },
  },
  typography: {
    fontFamily: [
      'Roboto',
      'Arial',
      'sans-serif',
    ].join(','),
   
  },
  padding: 0,
  });

  console.log('qwqer main.jsx ')
  
window.ReactDOM = ReactDOM

const root = ReactDOM.createRoot(document.getElementById('shipping_qwqer_mount'));
root.render(
    <ThemeProvider theme={theme}>
      <QwqerProvider>
        <App />
      </QwqerProvider>
    </ThemeProvider>
);

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitalsconsole.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals

