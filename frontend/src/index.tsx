import { createRoot } from 'react-dom/client';
import { App } from './components/App';
import './styles.css';

const settings = window.wralmSettings;

if (settings) {
  const container = document.getElementById('wralm-console');
  if (container) {
    createRoot(container).render(<App settings={settings} />);
  }
}
