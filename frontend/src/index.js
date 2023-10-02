import React from 'react';
import ReactDOM from 'react-dom';
import { createRoot } from 'react-dom/client';
import App from './App';
import Main from './Main';
import 'helpers/initFA';

import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

import  { Breakpoint, BreakpointProvider } from 'react-socks';

const container = document.getElementById('main');
const root = createRoot(container);

root.render(
  <React.StrictMode>
    <ToastContainer
      theme="light"
      position="top-center"
      autoClose={3000}
      hideProgressBar={false}
      newestOnTop={true}
      rtl={false}
      pauseOnFocusLoss={false}
      draggable
      draggablePercent={60}
      pauseOnHover
      limit={5}
    />
    <Main>
      <BreakpointProvider>
        <App />
      </BreakpointProvider>
    </Main>
  </React.StrictMode>
);
