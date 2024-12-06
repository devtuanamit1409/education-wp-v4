document.addEventListener("DOMContentLoaded", function() {
    const darkReaderConfig = {
        brightness: 90,
        contrast: 105,
        sepia: 15
    };
    const darkModeStartHour = 18;  
    const darkModeStartMinute = 30;
    const darkModeEndHour = 3;     
    const darkModeEndMinute = 30;
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();
    function isNightTime() {
        const isAfterStart = (currentHour > darkModeStartHour) || 
                             (currentHour === darkModeStartHour && currentMinute >= darkModeStartMinute);
        const isBeforeEnd = (currentHour < darkModeEndHour) || 
                            (currentHour === darkModeEndHour && currentMinute < darkModeEndMinute);
        const isNight = currentHour >= darkModeStartHour || currentHour < darkModeEndHour;
        
        return isNight && (isAfterStart || isBeforeEnd);
    }
    if (isNightTime()) {
        DarkReader.enable(darkReaderConfig);
        localStorage.setItem('darkmode', 'enabled');
    } else {
        DarkReader.disable();
        localStorage.setItem('darkmode', 'disabled');
    }
});
