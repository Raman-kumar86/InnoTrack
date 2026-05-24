import { useMemo, useState } from 'react';
import { useStateData } from './hooks/useStateData.js';
import InlineBarChart from './components/InlineBarChart.jsx';
import FullBarChart from './components/FullBarChart.jsx';
import StateTable from './components/StateTable.jsx';
import ChartSwitcher from './components/ChartSwitcher.jsx';
import TierLegend from './components/TierLegend.jsx';
import SummaryPills from './components/SummaryPills.jsx';
import SkeletonLoader from './components/SkeletonLoader.jsx';
import ModalOverlay from '../SectorDistributionWidget/modal/ModalOverlay.jsx';
import './state-strength-widget.css';
import '../SectorDistributionWidget/widget.css';

const chartOptions = [
    { label: 'Bar Chart', value: 'bar' },
    { label: 'Table', value: 'table' },
];

function formatNumber(value) {
    return new Intl.NumberFormat('en-IN').format(value);
}

export function tierColor(tier) {
    return {
        'Tier 1': '#6ee7f7',
        'Tier 2': '#34d399',
        'Tier 3': 'rgba(255,255,255,0.25)',
    }[tier] ?? 'rgba(255,255,255,0.2)';
}

function getTierBadgeClass(tier) {
    if (tier === 'Tier 1') {
        return 'stw-badge-tier-1';
    }

    if (tier === 'Tier 2') {
        return 'stw-badge-tier-2';
    }

    return 'stw-badge-tier-3';
}

export default function StateStrengthWidget({ endpoint, title, subtitle }) {
    const { data, loading, error, refetch } = useStateData(endpoint);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [activeView, setActiveView] = useState('bar');

    const states = useMemo(() => {
        const raw = Array.isArray(data?.states) ? data.states : [];
        return raw;
    }, [data]);

    const totalActive = Number(data?.total_active_startups ?? 0);
    const topState = states[0] ?? null;
    const totalStates = states.length;
    const maxCount = states[0]?.count ?? 0;
    const top3Cover = states.slice(0, 3).reduce((sum, state) => sum + Number(state.share || 0), 0);

    if (!data && loading) {
        return <SkeletonLoader mode="card" />;
    }

    if (error && !data) {
        return (
            <div className="sdw-card p-6 sm:p-8">
                <p className="sdw-eyebrow sdw-mono">State Ecosystem</p>
                <h3 className="sdw-headline sdw-syne mt-3">Failed to load state data</h3>
                <p className="sdw-subtitle sdw-sans mt-2">{error}</p>
                <button type="button" onClick={refetch} className="sdw-open-btn mt-4">
                    Retry →
                </button>
            </div>
        );
    }

    if (!states.length) {
        return (
            <div className="sdw-card p-6 sm:p-8">
                <p className="sdw-eyebrow sdw-mono">State Ecosystem</p>
                <h3 className="sdw-headline sdw-syne mt-3">No state data available</h3>
                <p className="sdw-subtitle sdw-sans mt-2">The API returned zero active states.</p>
            </div>
        );
    }

    return (
        <>
            <div className="sdw-card p-6 sm:p-8">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="sdw-eyebrow sdw-mono">STATE ECOSYSTEM</p>
                        <h3 className="sdw-headline sdw-syne mt-3">State-wise startup strength</h3>
                        <p className="sdw-subtitle sdw-sans mt-2">Top startup states by active ecosystem volume.</p>
                    </div>

                    <button
                        type="button"
                        className="sdw-open-btn"
                        onClick={() => {
                            setActiveView('bar');
                            setIsModalOpen(true);
                            refetch();
                        }}
                    >
                        Open Analysis ↗
                    </button>
                </div>

                <div className="mt-6">
                    <InlineBarChart states={states.slice(0, 7)} maxCount={maxCount} tierColor={tierColor} />
                </div>

                <div className="mt-6">
                    <SummaryPills states={states} />
                </div>
            </div>

            <ModalOverlay open={isModalOpen} onClose={() => setIsModalOpen(false)}>
                <div className={`sdw-modal-panel ${isModalOpen ? 'opacity-100' : ''}`}>
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <div className="flex items-center gap-2">
                                <span className="inline-flex h-2 w-2 rounded-full bg-cyan-300" />
                                <p className="sdw-eyebrow sdw-mono">STATE ANALYSIS</p>
                            </div>
                            <h3 className="sdw-headline sdw-syne mt-2 text-[22px]">State-wise Ecosystem Ranking</h3>
                            <p className="sdw-subtitle sdw-sans mt-2">{formatNumber(totalActive)} startups ranked across {formatNumber(totalStates)} states</p>
                        </div>

                        <button type="button" className="sdw-close-btn" onClick={() => setIsModalOpen(false)}>
                            ×
                        </button>
                    </div>

                    <div className="mt-5 flex flex-wrap items-center gap-3">
                        <ChartSwitcher active={activeView} onChange={setActiveView} options={chartOptions} disabled={loading && !data} />
                    </div>

                    <div className="mt-5">
                        <TierLegend states={states} tierColor={tierColor} />
                    </div>

                    <div className="mt-5">
                        {loading ? (
                            <SkeletonLoader mode="modal" />
                        ) : activeView === 'bar' ? (
                            <FullBarChart states={states} tierColor={tierColor} tierBadgeClass={getTierBadgeClass} />
                        ) : (
                            <StateTable states={states} tierColor={tierColor} tierBadgeClass={getTierBadgeClass} />
                        )}
                    </div>

                    <div className="sdw-modal-footer">
                        <p className="sdw-footer-left">Data from live MySQL database via Laravel API</p>
                        <p className="sdw-footer-right">↓ exportable</p>
                    </div>
                </div>
            </ModalOverlay>
        </>
    );
}