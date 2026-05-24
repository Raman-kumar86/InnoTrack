import { createRoot } from 'react-dom/client';
import SectorDistributionWidget from './SectorDistributionWidget/index.jsx';

const mountNode = document.getElementById('sector-distribution-widget');

if (mountNode) {
    const endpoint = mountNode.dataset.apiUrl ?? '/api/dashboard/sector-distribution';
    const title = mountNode.dataset.title ?? 'Sector distribution';
    const subtitle = mountNode.dataset.subtitle ?? 'Share of active startups by dominant sector.';

    createRoot(mountNode).render(
        <SectorDistributionWidget
            endpoint={endpoint}
            title={title}
            subtitle={subtitle}
        />,
    );
}