document.addEventListener("DOMContentLoaded", function() {
    const darkModeToggles = document.querySelectorAll('[id^="ft-darkmode-switch-"]');
	if (!darkModeToggles) return;
    darkModeToggles.forEach(function(darkModeToggle) {
        const uniqueId = darkModeToggle.id;
        const checkbox = document.getElementById(uniqueId);
        if (localStorage.getItem('darkmode') === 'enabled') {
            DarkReader.enable({
                brightness: 90,
                contrast: 105,
                sepia: 15
            });
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                DarkReader.enable({
                    brightness: 90,
                    contrast: 105,
                    sepia: 15
                });
                localStorage.setItem('darkmode', 'enabled');
            } else {
                DarkReader.disable();
                localStorage.setItem('darkmode', 'disabled');
            }
        });
    });
});
