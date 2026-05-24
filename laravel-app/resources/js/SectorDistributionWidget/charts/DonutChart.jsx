import { Cell, Legend, Pie, PieChart, ResponsiveContainer, Tooltip } from 'recharts';

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

export default function DonutChart({ data, total }) {
    const centerText = formatNumber(total);

    return (
        <div className="sdw-chart-area relative w-full">
            <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                    <Pie
                        data={data}
                        dataKey="count"
                        nameKey="sector_name"
                        cx="50%"
                        cy="50%"
                        innerRadius={90}
                        outerRadius={150}
                        paddingAngle={3}
                        stroke="none"
                    >
                        {data.map((entry) => (
                            <Cell
                                key={entry.sector_id ?? entry.sector_name}
                                fill={entry.color}
                            />
                        ))}
                    </Pie>
                    <Tooltip content={<CustomTooltip />} />
                    <Legend verticalAlign="bottom" height={44} formatter={(value) => <span className="sdw-sans text-xs text-white/70">{value}</span>} />
                </PieChart>
            </ResponsiveContainer>

            <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                <p className="sdw-ring-center-label">Active Startups</p>
                <p className="sdw-ring-center-value mt-2">{centerText}</p>
                <p className="sdw-ring-center-sub mt-2">{data.length.toLocaleString('en-IN')} visible sectors from the live registry</p>
            </div>
        </div>
    );
}
