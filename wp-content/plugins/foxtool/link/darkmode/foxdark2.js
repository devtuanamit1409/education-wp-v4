document.addEventListener("DOMContentLoaded", function() {
    const darkModeToggles = document.querySelectorAll('#ft-darkmode-toggle');
	if (!darkModeToggles) return;
    darkModeToggles.forEach(function(darkModeToggle) {
        const moonIcon = darkModeToggle.querySelector('#ft-icon-moon');
        const sunIcon = darkModeToggle.querySelector('#ft-icon-sun');
        if (localStorage.getItem('darkmode') === 'enabled') {
            DarkReader.enable({
                brightness: 90,
                contrast: 105,
                sepia: 15
            });
            sunIcon.style.display = 'block';
            moonIcon.style.display = 'none';
            darkModeToggle.classList.remove('ft-sunmode');
        } else {
            sunIcon.style.display = 'none';
            moonIcon.style.display = 'block';
            darkModeToggle.classList.add('ft-sunmode');
        }
        darkModeToggle.addEventListener('click', function() {
            if (DarkReader.isEnabled()) {
                DarkReader.disable();
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
                darkModeToggle.classList.add('ft-sunmode');
                localStorage.setItem('darkmode', 'disabled');
            } else {
                DarkReader.enable({
                    brightness: 90,
                    contrast: 105,
                    sepia: 15
                });
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
                darkModeToggle.classList.remove('ft-sunmode');
                localStorage.setItem('darkmode', 'enabled');
            }
        });
    });
});