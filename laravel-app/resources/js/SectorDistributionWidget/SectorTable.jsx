function formatNumber(value) {
    return new Intl.NumberFormat('en-IN').format(value);
}

export default function SectorTable({ data }) {
    const maxShare = data.reduce((max, sector) => Math.max(max, Number(sector.share || 0)), 0);

    return (
        <div className="sdw-chart-area">
            <div className="sdw-scroll">
                <table className="sdw-table">
                    <thead className="sdw-table-head text-left">
                        <tr>
                            <th>Rank</th>
                            <th>Color</th>
                            <th>Sector</th>
                            <th className="text-right">Count</th>
                            <th className="text-right">Share</th>
                            <th>Bar</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.map((sector, index) => (
                            <tr key={sector.sector_id ?? sector.sector_name} className="sdw-table-row">
                                <td className="sdw-rank">{index + 1}</td>
                                <td>
                                    <span className="sdw-dot" style={{ backgroundColor: sector.color }} />
                                </td>
                                <td className="sdw-sector-cell">{sector.sector_name}</td>
                                <td className="sdw-num-cell">{formatNumber(sector.count)}</td>
                                <td className="sdw-share-cell">{Number(sector.share).toFixed(1)}%</td>
                                <td>
                                    <div className="sdw-bar-track">
                                        <div
                                            className="sdw-bar-fill"
                                            style={{
                                                '--bar-width': `${maxShare > 0 ? (Number(sector.share) / maxShare) * 100 : 0}%`,
                                                '--bar-color': sector.color,
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
