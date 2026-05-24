import { useMemo, useState } from 'react';
import { Cell, Pie, PieChart, ResponsiveContainer, Tooltip } from 'recharts';
import { useSectorData } from './hooks/useSectorData.js';
import DonutChart from './charts/DonutChart.jsx';
import HorizontalBar from './charts/HorizontalBar.jsx';
import SectorTable from './SectorTable.jsx';
import ChartSwitcher from './ChartSwitcher.jsx';
import SkeletonLoader from './SkeletonLoader.jsx';
import ModalOverlay from './modal/ModalOverlay.jsx';
import ModalPanel from './modal/ModalPanel.jsx';
import './widget.css';

const chartOptions = [
    { label: 'Donut', value: 'donut' },
    { label: 'Bar', value: 'bar' },
    { label: 'Table', value: 'table' },
];

function formatNumber(value) {
    return new Intl.NumberFormat('en-IN').format(value);
}

function generateColor(index, total) {
    return `hsl(${Math.round((index * 360) / Math.max(1, total))}, 70%, 58%)`;
}

function buildColorMap(sectors) {
    return sectors.map((sector, index) => ({
        ...sector,
        color: generateColor(index, sectors.length),
    }));
}

function MiniTooltip({ active, payload }) {
    if (!active || !payload || !payload.length) {
        return null;
    }

    const sector = payload[0].payload;

    return (
        <div className="sdw-tooltip">
            <p className="sdw-tooltip-title">{sector.sector_name}</p>
            <p className="sdw-tooltip-line">Count: {formatNumber(sector.count)}</p>
            <p className="sdw-tooltip-line">Share: {Number(sector.share).toFixed(1)}%</p>
        </div>
    );
}

function renderModalContent(view, sectors, total) {
    if (view === 'donut') {
        return <DonutChart data={sectors} total={total} />;
    }

    if (view === 'bar') {
        return <HorizontalBar data={sectors} />;
    }

    return <SectorTable data={sectors} />;
}

export default function SectorDistributionWidget({ endpoint, title, subtitle }) {
    const { data, loading, error, refetch } = useSectorData(endpoint);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [activeView, setActiveView] = useState('donut');

    const sectors = useMemo(() => {
        const raw = Array.isArray(data?.sectors) ? data.sectors : [];
        return buildColorMap(raw);
    }, [data]);

    const totalActive = Number(data?.total_active_startups ?? 0);
    const topSector = sectors[0] ?? null;
    const topShare = Number(topSector?.share ?? 0);
    const totalSectors = sectors.length;

    if (!data && loading) {
        return <SkeletonLoader mode="card" />;
    }

    if (error && !data) {
        return (
            <div className="sdw-card p-6">
                <p className="sdw-eyebrow sdw-mono">Sector Distribution</p>
                <h3 className="sdw-syne mt-3 text-2xl font-bold text-white">Unable to load sector data</h3>
                <p className="sdw-sans mt-2 text-sm text-white/50">{error}</p>
                <button type="button" onClick={refetch} className="sdw-open-btn mt-4">
                    Retry →
                </button>
            </div>
        );
    }

    if (!sectors.length) {
        return (
            <div className="sdw-card p-6">
                <p className="sdw-eyebrow sdw-mono">Sector Distribution</p>
                <h3 className="sdw-syne mt-3 text-2xl font-bold text-white">No sector data available</h3>
                <p className="sdw-sans mt-2 text-sm text-white/50">The API returned zero active sectors.</p>
            </div>
        );
    }

    return (
        <>
            <div className="sdw-card p-6 sm:p-8">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="sdw-eyebrow sdw-mono">{title}</p>
                        <h3 className="sdw-headline sdw-syne mt-3">Live sector mix from the database</h3>
                        <p className="sdw-subtitle sdw-sans mt-2">{subtitle}</p>
                    </div>

                    <button
                        type="button"
                        className="sdw-open-btn"
                        onClick={() => {
                            setActiveView('donut');
                            setIsModalOpen(true);
                            refetch();
                        }}
                    >
                        Open Analysis →
                    </button>
                </div>

                <div className="mt-6">
                    <div className="sdw-ring-panel relative">
                        <ResponsiveContainer width="100%" height="100%">
                            <PieChart>
                                <Pie
                                    data={sectors}
                                    dataKey="count"
                                    nameKey="sector_name"
                                    cx="50%"
                                    cy="50%"
                                    innerRadius={100}
                                    outerRadius={148}
                                    paddingAngle={2.5}
                                    stroke="none"
                                >
                                    {sectors.map((entry) => (
                                        <Cell key={entry.sector_id ?? entry.sector_name} fill={entry.color} />
                                    ))}
                                </Pie>
                                <Tooltip content={<MiniTooltip />} />
                            </PieChart>
                        </ResponsiveContainer>

                        <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                            <p className="sdw-ring-center-label">Active Startups</p>
                            <p className="sdw-ring-center-value mt-2">{formatNumber(totalActive)}</p>
                            <p className="sdw-ring-center-sub mt-2">{formatNumber(totalSectors)} visible sectors from the live registry</p>
                        </div>
                    </div>
                </div>
            </div>

            <ModalOverlay open={isModalOpen} onClose={() => setIsModalOpen(false)}>
                <ModalPanel open={isModalOpen} onClose={() => setIsModalOpen(false)} total={totalActive} sectorsCount={totalSectors}>
                    <ChartSwitcher active={activeView} onChange={setActiveView} options={chartOptions} disabled={loading && !data} />

                    <div className="mt-5 flex flex-col gap-4">
                        <div className="sdw-stat-card sdw-stat-card-accent">
                            <p className="sdw-stat-label">Top Sector</p>
                            <p className="sdw-stat-value mt-2">{topSector?.sector_name ?? '-'}</p>
                            <p className="sdw-stat-sub mt-1">{formatNumber(topSector?.count ?? 0)} active startups</p>
                        </div>

                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div className="sdw-stat-card">
                                <p className="sdw-stat-mini-label">Visible Sectors</p>
                                <p className="sdw-stat-mini-value mt-2">{formatNumber(totalSectors)}</p>
                            </div>

                            <div className="sdw-stat-card">
                                <p className="sdw-stat-mini-label">Top Share</p>
                                <p className="sdw-stat-mini-value sdw-top-share mt-2">{topShare.toFixed(1)}%</p>
                            </div>
                        </div>

                        <div className="sdw-bottom-note">View the full analytical breakdown with switchable chart modes and the complete sector table.</div>
                    </div>

                    <div className="mt-5">
                        {loading ? <SkeletonLoader mode="modal" /> : renderModalContent(activeView, sectors, totalActive)}
                    </div>
                </ModalPanel>
            </ModalOverlay>
        </>
    );
}
