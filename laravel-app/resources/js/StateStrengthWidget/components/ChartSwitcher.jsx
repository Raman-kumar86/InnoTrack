export default function ChartSwitcher({ active, onChange, options, disabled = false }) {
    return (
        <div className="sdw-switcher">
            {options.map((option) => {
                const isActive = active === option.value;

                return (
                    <button
                        key={option.value}
                        type="button"
                        disabled={disabled}
                        onClick={() => onChange(option.value)}
                        className={`sdw-switch-btn ${isActive ? 'is-active' : ''} ${disabled ? 'cursor-not-allowed opacity-60' : ''}`}
                    >
                        {option.label}
                    </button>
                );
            })}
        </div>
    );
}