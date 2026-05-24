import { createRoot } from 'react-dom/client';
import StateStrengthWidget from './StateStrengthWidget/index.jsx';

const mountNode = document.getElementById('state-strength-widget');

if (mountNode) {
    const endpoint = mountNode.dataset.apiUrl ?? '/api/dashboard/state-startup-strength';
    const title = mountNode.dataset.title ?? 'State ecosystem';
    const subtitle = mountNode.dataset.subtitle ?? 'Top startup states by active ecosystem volume.';

    createRoot(mountNode).render(
        <StateStrengthWidget
            endpoint={endpoint}
            title={title}
            subtitle={subtitle}
        />,
    );
}