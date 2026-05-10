// resources/js/utils/formatters.js

/**
 * Formata a data ISO para DD/MM/YYYY.
 */
export const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', { timeZone: 'UTC' });
};

/**
 * Pega apenas as horas e minutos (ex: de "08:00:00" para "08:00")
 */
export const formatTime = (timeString) => {
    if (!timeString) return '';
    return timeString.substring(0, 5);
};