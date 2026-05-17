// resources/js/utils/formatters.js

// Format a date string (e.g., "2024-06-01") to Brazilian format (e.g., "01/06/2024")
export const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', { timeZone: 'UTC' });
};

// Format a time string (e.g., "14:30:00") to "HH:mm" format (e.g., "14:30")
export const formatTime = (timeString) => {
    if (!timeString) return '';
    return timeString.substring(0, 5);
};

// Format a number as Brazilian Real currency (R$ 1.234,56)
export const formatCurrency = (value) => {
    return Number(value).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    });
};