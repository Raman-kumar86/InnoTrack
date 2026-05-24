import { Bar, BarChart, CartesianGrid, Cell, LabelList, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

function formatNumber(value) {
    return new Intl.NumberFormat('en-IN').format(value);
}

function CustomTooltip({ active, payload }) {
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

export default function HorizontalBar({ data }) {
    return (
        <div className="sdw-chart-area w-full">
            <ResponsiveContainer width="100%" height="100%">
                <BarChart data={data} layout="vertical" margin={{ top: 12, right: 36, left: 8, bottom: 8 }} barCategoryGap="26%">
                    <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.05)" horizontal={false} />
                    <XAxis type="number" tickLine={false} axisLine={false} tick={{ fill: 'rgba(255,255,255,0.5)', fontSize: 11, fontFamily: 'DM Mono' }} />
                    <YAxis type="category" dataKey="sector_name" width={110} tickLine={false} axisLine={false} tick={{ fill: 'rgba(255,255,255,0.72)', fontSize: 11, fontFamily: 'DM Sans' }} />
                    <Tooltip content={<CustomTooltip />} />
                    <Bar dataKey="count" radius={[0, 6, 6, 0]}>
                        <LabelList dataKey="count" position="right" fill="rgba(255,255,255,0.75)" fontSize={11} fontFamily="DM Mono" />
                        {data.map((entry) => (
                            <Cell key={entry.sector_id ?? entry.sector_name} fill={entry.color} />
                        ))}
                    </Bar>
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}