document.addEventListener('DOMContentLoaded', function() {
    var adsclickElement = document.querySelector('[data-adsclick]');
    if (!adsclickElement) return;
    var links = adsclickElement.getAttribute('data-links').split(',');
    var miniWindow = adsclickElement.getAttribute('data-mini') || null;
    var hours = parseInt(adsclickElement.getAttribute('data-hours'), 10);
    var clickTarget = adsclickElement.getAttribute('data-click-target'); 
    function setLocalStorage(domain) {
        var expires = new Date();
        expires.setTime(expires.getTime() + (hours * 60 * 60 * 1000));
        var data = {
            value: 1,
            expiry: expires.getTime() // Lưu thời gian hết hạn dưới dạng timestamp
        };
        localStorage.setItem("adsclick_" + domain, JSON.stringify(data));
    }
    function isDomainInLocalStorage(domain) {
        var storedData = localStorage.getItem("adsclick_" + domain);
        if (storedData) {
            var parsedData = JSON.parse(storedData);
            if (parsedData.expiry > new Date().getTime()) {
                return true;
            } else {
                localStorage.removeItem("adsclick_" + domain); // Xóa nếu hết hạn
                return false;
            }
        }
        return false;
    }
    function AffClickHandler(event) {
        for (var i = 0; i < links.length; i++) {
            var newLink = links[i];
            var parser = document.createElement('a');
            parser.href = newLink;
            var domain = parser.hostname + parser.pathname; 
            if (!isDomainInLocalStorage(domain)) {
                setLocalStorage(domain);
                window.open(newLink, '_blank', miniWindow);
                break;
            }
        }
    }
    if (clickTarget === 'link') {
		document.querySelectorAll('a, button').forEach(function(element) {
			element.addEventListener('click', AffClickHandler);
		});
	} else {
		document.addEventListener('click', AffClickHandler);
	}
});
