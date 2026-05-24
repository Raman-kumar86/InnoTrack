export default function SkeletonLoader({ mode = 'card' }) {
    if (mode === 'modal') {
        return (
            <div className="stw-modal-grid">
                <div className="stw-shimmer h-16" />
                <div className="stw-shimmer h-28" />
                <div className="stw-shimmer h-64" />
            </div>
        );
    }

    return (
        <div className="sdw-card p-6 sm:p-8">
            <div className="space-y-5">
                <div className="flex items-start justify-between gap-4">
                    <div className="space-y-3">
                        <div className="sdw-shimmer h-3 w-36" />
                        <div className="sdw-shimmer h-8 w-72" />
                        <div className="sdw-shimmer h-4 w-56" />
                    </div>
                    <div className="sdw-shimmer h-10 w-36 rounded-[10px]" />
                </div>

                <div className="space-y-3">
                    {Array.from({ length: 7 }).map((_, index) => (
                        <div key={index} className="flex items-center gap-3">
                            <div className="sdw-shimmer h-4 w-32" />
                            <div className="sdw-shimmer h-2 flex-1 rounded-full" />
                            <div className="sdw-shimmer h-4 w-10" />
                            <div className="sdw-shimmer h-4 w-14" />
                        </div>
                    ))}
                </div>

                <div className="flex flex-wrap gap-3">
                    <div className="sdw-shimmer h-10 w-56 rounded-full" />
                    <div className="sdw-shimmer h-10 w-44 rounded-full" />
                    <div className="sdw-shimmer h-10 w-44 rounded-full" />
                </div>
            </div>
        </div>
    );
}