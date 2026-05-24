function formatNumber(value) {
    return new Intl.NumberFormat('en-IN').format(value);
}

export default function SummaryPills({ states }) {
    const topState = states[0];
    const tier1Count = states.filter((state) => state.tier === 'Tier 1').length;
    const top3Cover = states.slice(0, 3).reduce((total, state) => total + Number(state.share || 0), 0);

    return (
        <div className="stw-summary-pills">
            <div className="stw-pill">
                <span>🏆 Top State:</span>
                <strong>{topState?.state_name ?? '-'}</strong>
                <strong>· {formatNumber(topState?.count ?? 0)}</strong>
            </div>
            <div className="stw-pill">
                <span>📊 Tier 1 States:</span>
                <strong>{formatNumber(tier1Count)}</strong>
            </div>
            <div className="stw-pill">
                <span>⚡ Top 3 Cover:</span>
                <strong>{top3Cover.toFixed(2)}%</strong>
            </div>
        </div>
    );
}