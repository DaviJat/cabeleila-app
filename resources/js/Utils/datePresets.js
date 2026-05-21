// resources/js/Utils/datePresets.js

// Utility functions to generate date ranges for "Today", "This Week", "This Month", "This Year" and "All Time".


// Returns an array with today's date.
export const getTodayDate = () => {
    return [new Date()];
}

// Returns a range from Monday to Sunday of the current week.
export const getWeeklyRange = () => {
    const today = new Date();
    const currentDay = today.getDay();
    
    const distanceToMonday = currentDay === 0 ? -6 : 1 - currentDay;
    const monday = new Date(today);
    monday.setDate(today.getDate() + distanceToMonday);
    
    const sunday = new Date(monday);
    sunday.setDate(monday.getDate() + 6);
    
    return [monday, sunday];
};

// Returns a range from the 1st day of the current month until today.
export const getMonthlyRange = () => {
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    
    return [firstDayOfMonth, today];
};

// Returns a range from January 1st of the current year until today.
export const getYearlyRange = () => {
    const today = new Date();
    const firstDayOfYear = new Date(today.getFullYear(), 0, 1);
    
    return [firstDayOfYear, today];
};

// Returns an open range representing "All Time"
export const getAllTimeRange = () => {
    return [null, null];
}
