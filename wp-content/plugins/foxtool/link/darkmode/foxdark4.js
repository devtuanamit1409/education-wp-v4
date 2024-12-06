document.addEventListener("DOMContentLoaded", function() {
    const darkReaderConfig = {
        brightness: 90,
        contrast: 105,
        sepia: 15
    };
    const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");
    if (prefersDarkScheme.matches) {
        DarkReader.enable(darkReaderConfig);
        localStorage.setItem('darkmode', 'enabled');
    } else {
        DarkReader.disable();
        localStorage.setItem('darkmode', 'disabled');
    }
    prefersDarkScheme.addEventListener('change', function(event) {
        if (event.matches) {  
            DarkReader.enable(darkReaderConfig);
            localStorage.setItem('darkmode', 'enabled');
        } else { 
            DarkReader.disable();
            localStorage.setItem('darkmode', 'disabled');
        }
    });
});
