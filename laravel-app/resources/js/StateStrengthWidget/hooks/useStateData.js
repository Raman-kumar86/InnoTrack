import { useCallback, useEffect, useState } from 'react';
import axios from 'axios';

export function useStateData(endpoint) {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const fetchData = useCallback(async () => {
        if (!endpoint) {
            setError('State data endpoint is missing.');
            setLoading(false);
            return;
        }

        setLoading(true);
        setError('');

        try {
            const response = await axios.get(endpoint, {
                headers: {
                    Accept: 'application/json',
                },
            });

            setData(response.data);
        } catch (exception) {
            setError(exception?.response?.data?.message ?? 'Unable to load state startup strength data.');
        } finally {
            setLoading(false);
        }
    }, [endpoint]);

    useEffect(() => {
        fetchData();
    }, [fetchData]);

    return { data, loading, error, refetch: fetchData };
}