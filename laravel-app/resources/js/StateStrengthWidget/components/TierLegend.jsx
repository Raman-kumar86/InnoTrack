export default function TierLegend({ states, tierColor }) {
    const legendItems = [
        {
            key: 'Tier 1',
            label: 'Tier 1 (≥25 startups)',
            count: states.filter((state) => state.tier === 'Tier 1').length,
        },
        {
            key: 'Tier 2',
            label: 'Tier 2 (15–24)',
            count: states.filter((state) => state.tier === 'Tier 2').length,
        },
        {
            key: 'Tier 3',
            label: 'Tier 3 (<15)',
            count: states.filter((state) => state.tier === 'Tier 3').length,
        },
    ];

    return (
        <div className="stw-tier-legend">
            {legendItems.map((item) => (
                <div key={item.key} className={`stw-tier-pill stw-${item.key.toLowerCase().replace(' ', '-')}`}>
                    <span className="stw-tier-dot" style={{ backgroundColor: tierColor(item.key) }} />
                    <span>{item.label} · {item.count} states</span>
                </div>
            ))}
        </div>
    );
}