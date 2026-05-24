export default function SkeletonLoader({ mode = 'card' }) {
    if (mode === 'modal') {
        return (
            <div className="sdw-modal-skeleton">
                <div className="sdw-shimmer h-16" />
                <div className="sdw-shimmer h-28" />
                <div className="sdw-shimmer h-64" />
            </div>
        );
    }

    return (
        <div className="sdw-card p-6">
            <div className="space-y-5">
                <div className="flex items-start justify-between gap-4">
                    <div className="space-y-3">
                        <div className="sdw-shimmer h-3 w-36" />
                        <div className="sdw-shimmer h-9 w-72" />
                        <div className="sdw-shimmer h-4 w-56" />
                    </div>
                    <div className="sdw-shimmer h-10 w-36 rounded-[10px]" />
                </div>

                <div className="grid gap-4 md:grid-cols-[1.15fr_0.85fr]">
                    <div className="sdw-shimmer h-70 rounded-2xl" />
                    <div className="space-y-4">
                        <div className="sdw-shimmer h-28 rounded-2xl" />
                        <div className="grid grid-cols-2 gap-4">
                            <div className="sdw-shimmer h-20 rounded-2xl" />
                            <div className="sdw-shimmer h-20 rounded-2xl" />
                        </div>
                        <div className="sdw-shimmer h-20 rounded-2xl" />
                    </div>
                </div>
            </div>
        </div>
    );
}