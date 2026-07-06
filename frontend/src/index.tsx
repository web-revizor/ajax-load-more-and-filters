import { createRoot } from 'react-dom/client';
import './styles.scss';
import { App } from '@/src/components/App';
import { ToastProvider } from '@/src/context/ToastContext';

const settings = window.wralmSettings;

if (settings) {
  const container = document.getElementById('wralm-console');
  if (container) {
    createRoot(container).render(<App settings={settings} />);
  }
}

const toastRoot = document.createElement('div');
toastRoot.className = 'web-revizor-container';
document.body.appendChild(toastRoot);
createRoot(toastRoot).render(<ToastProvider />);
