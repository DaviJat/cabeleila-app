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

// Format a phone number string to Brazilian format (e.g., "(11) 91234-5678")
export const formatPhone = (value) => {
    if (!value) return '';
    const cleanValue = value.replace(/\D/g, '');
    if (cleanValue.length === 11) {
        return cleanValue.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    }
    if (cleanValue.length === 10) {
        return cleanValue.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
    }
    return value;
};

// Format a CPF number string to Brazilian format (e.g., "123.456.789-00")
export const formatCPF = (value) => {
    if (!value) return '';
    const cleanValue = value.replace(/\D/g, '');
    if (cleanValue.length === 11) {
        return cleanValue.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
    }
    return value;
};