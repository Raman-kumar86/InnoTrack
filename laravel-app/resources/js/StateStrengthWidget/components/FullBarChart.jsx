import {
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    LabelList,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

function formatNumber(value) {
    return new Intl.NumberFormat('en-IN').format(value);
}

function tierBadgeClass(tier) {
    if (tier === 'Tier 1') {
        return 'stw-badge-tier-1';
    }

    if (tier === 'Tier 2') {
        return 'stw-badge-tier-2';
    }

    return 'stw-badge-tier-3';
}

function CustomTooltip({ active, payload }) {
    if (!active || !payload || !payload.length) {
        return null;
    }

    const state = payload[0].payload;

    return (
        <div className="sdw-tooltip">
            <p className="sdw-tooltip-title">{state.state_name}</p>
            <p className="sdw-tooltip-line">Count: {formatNumber(state.count)}</p>
            <p className="sdw-tooltip-line">Share: {Number(state.share).toFixed(2)}%</p>
            <p className={`stw-badge mt-2 ${tierBadgeClass(state.tier)}`}>{state.tier}</p>
        </div>
    );
}

export default function FullBarChart({ states, tierColor }) {
    return (
        <div className="stw-modal-grid">
            <ResponsiveContainer width="100%" height={520}>
                <BarChart data={states} layout="vertical" margin={{ top: 12, right: 24, left: 8, bottom: 8 }} barCategoryGap="26%">
                    <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.05)" horizontal={false} />
                    <XAxis type="number" tickLine={false} axisLine={false} tick={{ fill: 'rgba(255,255,255,0.3)', fontSize: 10, fontFamily: 'inherit' }} />
                    <YAxis type="category" dataKey="state_name" width={130} tickLine={false} axisLine={false} tick={{ fill: 'rgba(255,255,255,0.6)', fontSize: 12, fontFamily: 'inherit' }} />
                    <Tooltip content={<CustomTooltip />} />
                    <Bar dataKey="count" radius={[0, 6, 6, 0]} maxBarSize={22}>
                        <LabelList dataKey="count" position="right" formatter={(value) => value} style={{ fill: 'rgba(255,255,255,0.5)', fontSize: 11, fontFamily: 'inherit' }} />
                        {states.map((entry) => (
                            <Cell key={entry.state_id ?? entry.state_name} fill={tierColor(entry.tier)} />
                        ))}
                    </Bar>
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}