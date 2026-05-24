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

export default function StateTable({ states, tierColor }) {
    const maxCount = states[0]?.count ?? 0;

    return (
        <div className="stw-table-shell">
            <div className="stw-table-scroll">
                <table className="stw-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>State</th>
                            <th className="text-right">Count</th>
                            <th className="text-right">Share</th>
                            <th>Tier</th>
                            <th>Bar</th>
                        </tr>
                    </thead>
                    <tbody>
                        {states.map((state, index) => (
                            <tr key={state.state_id ?? state.state_name} className="stw-table-row">
                                <td className="stw-rank">#{index + 1}</td>
                                <td className="stw-state">{state.state_name}</td>
                                <td className="stw-count text-right">{formatNumber(state.count)}</td>
                                <td className="stw-share text-right">{Number(state.share).toFixed(2)}%</td>
                                <td>
                                    <span className={`stw-badge ${tierBadgeClass(state.tier)}`}>{state.tier}</span>
                                </td>
                                <td>
                                    <div className="stw-bar-track" style={{ maxWidth: '100px' }}>
                                        <div
                                            className="stw-bar-fill"
                                            style={{
                                                backgroundColor: tierColor(state.tier),
                                                '--stw-width': `${maxCount > 0 ? (state.count / maxCount) * 100 : 0}%`,
                                            }}
                                        />
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}