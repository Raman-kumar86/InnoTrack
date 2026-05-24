import { useEffect } from 'react';

export default function ModalOverlay({ open, onClose, children }) {
    useEffect(() => {
        if (!open) {
            return undefined;
        }

        const onKeyDown = (event) => {
            if (event.key === 'Escape') {
                onClose();
            }
        };

        window.addEventListener('keydown', onKeyDown);

        return () => {
            window.removeEventListener('keydown', onKeyDown);
        };
    }, [open, onClose]);

    useEffect(() => {
        if (!open) {
            document.body.classList.remove('overflow-hidden');
            return;
        }

        document.body.classList.add('overflow-hidden');

        return () => {
            document.body.classList.remove('overflow-hidden');
        };
    }, [open]);

    if (!open) {
        return null;
    }

    return (
        <div
            className="sdw-modal-shell is-open"
            role="dialog"
            aria-modal="true"
            onMouseDown={(event) => {
                if (event.target === event.currentTarget) {
                    onClose();
                }
            }}
        >
            {children}
        </div>
    );
}