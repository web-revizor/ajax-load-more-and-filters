import {createRoot} from 'react-dom/client';
import './styles.css';
import {App} from "@/src/components/App";

const settings = window.wralmSettings;

if (settings) {
    const container = document.getElementById('wralm-console');
    if (container) {
        createRoot(container).render(<App settings={settings}/>);
    }
}
