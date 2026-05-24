import { useEffect, useState } from 'react';

export default function InlineBarChart({ states, maxCount, tierColor }) {
    const [animate, setAnimate] = useState(false);

    useEffect(() => {
        const timer = window.setTimeout(() => setAnimate(true), 30);
        return () => window.clearTimeout(timer);
    }, [states]);

    return (
        <div className="stw-inline-list">
            {states.map((state, index) => {
                const width = maxCount > 0 ? (state.count / maxCount) * 100 : 0;

                return (
                    <div key={state.state_id ?? state.state_name} className="stw-inline-row">
                        <div className="stw-state-name">{state.state_name}</div>
                        <div className="stw-bar-track">
                            <div
                                className="stw-bar-fill"
                                style={{
                                    backgroundColor: tierColor(state.tier),
                                    '--stw-width': animate ? `${width}%` : '0%',
                                    transitionDelay: `${index * 80}ms`,
                                }}
                            />
                        </div>
                        <div className="stw-count">{state.count}</div>
                        <div className="stw-share">{state.share.toFixed(2)}%</div>
                    </div>
                );
            })}
        </div>
    );
}