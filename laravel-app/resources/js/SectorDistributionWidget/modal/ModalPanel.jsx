export default function ModalPanel({ open, onClose, total, sectorsCount, children }) {
    return (
        <div className="sdw-modal-panel">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <div className="flex items-center gap-2">
                        <span className="inline-flex h-2 w-2 rounded-full bg-cyan-300" />
                        <p className="sdw-eyebrow sdw-mono">Sector analysis</p>
                    </div>
                    <h3 className="sdw-syne mt-2 text-[22px] font-bold text-white">Active Startup Distribution</h3>
                    <p className="sdw-sans mt-2 text-[13px] text-white/40">
                        {total.toLocaleString('en-IN')} startups across {sectorsCount.toLocaleString('en-IN')} sectors
                    </p>
                </div>

                <button type="button" className="sdw-close-btn" onClick={onClose}>
                    ×
                </button>
            </div>

            <div className="mt-5">{children}</div>

            <div className="sdw-modal-footer">
                <p className="sdw-footer-left">Data from live MySQL database via Laravel API</p>
                <p className="sdw-footer-right">↓ exportable</p>
            </div>
        </div>
    );
}