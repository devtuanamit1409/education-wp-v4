// add code color editor
jQuery(document).ready(function($) {
    $('.ft-dev').each(function() {
        var editor = CodeMirror.fromTextArea(this, {
            lineNumbers: true,
			lineWrapping: true,
			matchBrackets: true,
            mode: 'text/x-perl',
            theme: 'cobalt',
			extraKeys: {
                "Ctrl-Z": "undo",   // hỗ trợ Ctrl + Z để quay lại
                "Ctrl-Y": "redo",   // hỗ trợ Ctrl + Y để làm lại
                "Cmd-Z": "undo",    // hỗ trợ Command + Z trên macOS
                "Cmd-Y": "redo",    // hỗ trợ Command + Y trên macOS
				"Ctrl-F": "find",   // hỗ trợ Ctrl + F để tìm kiếm
                "Cmd-F": "find",
				"Ctrl-H": "replace", // hỗ trợ Ctrl + H để thay thế
                "Cmd-H": "replace",
            }
        });
        $(this).data('CodeMirrorInstance', editor);
		// dong bo voi textarea
		editor.on('change', function() {
            $(editor.getTextArea()).val(editor.getValue());
        });
    });
});
// tab
function fttab(evt, tabname) {
  var i, x, sotab;
  x = document.getElementsByClassName("ftbox");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  sotab = document.getElementsByClassName("sotab");
  for (i = 0; i < x.length; i++) {
    sotab[i].className = sotab[i].className.replace(" sotab-select", "");
  }
  document.getElementById(tabname).style.display = "block";
  evt.currentTarget.className += " sotab-select";
  localStorage.setItem('ftranksel', tabname);
	// tại codeEditor cho fox-codex2 tab 2 tro di
    jQuery(document).ready(function($) {
      $('.ft-dev').each(function() {
            var editor = $(this).data('CodeMirrorInstance');
            if (editor) {
                editor.refresh(); 
            }
        });
    });
}
function ftSelectedRank() {
  var ftranksel = localStorage.getItem('ftranksel');
  if (ftranksel) {
    var sotab = document.querySelector('[onclick="fttab(event, \'' + ftranksel + '\')"]');
    if (sotab) {
      sotab.click();
    }
  }
}
document.addEventListener("DOMContentLoaded", function() {
  ftSelectedRank();
});
// display none
function getStyle(x, styleProp) {
    if (x.currentStyle) {
        var y = x.currentStyle[styleProp];
    }
    else if (window.getComputedStyle) {
        var y = document.defaultView.getComputedStyle(x, null).getPropertyValue(styleProp);
    }
    return y;
}
function ftnone(e, div_name) {
    var el = document.getElementById(div_name);
    var display = el.style.display || getStyle(el, 'display');
    el.style.display = (display == 'none') ? 'block' : 'none';
    ftnone.el = el;
    if (e.stopPropagation) e.stopPropagation();
    e.cancelBubble = true;
    return false;
}
// lay images tu media
jQuery(document).ready(function($) {
    $('.ft-selec').click(function(e) {
        e.preventDefault();
        var inputId = $(this).data('input-id');
        openMediaUploader(inputId);
    });

    function openMediaUploader(inputId) {
        var customUploader = wp.media({
            title: 'Media',
            button: {
                text: 'OK'
            },
            multiple: false
        });

        customUploader.on('select', function() {
            var attachment = customUploader.state().get('selection').first().toJSON();
            var imageUrl = attachment.url;
            $('#' + inputId).val(imageUrl);
        });

        customUploader.open();
    }
});

// kiem tra trang thai check
jQuery(document).ready(function($) {
    $('.toggle-checkbox').each(function() {
        var targetDiv = $('#' + $(this).data('target'));
        if ($(this).is(':checked')) {
            targetDiv.removeClass('noon');
        } else {
            targetDiv.addClass('noon');
        }
        $(this).change(function() {
            if ($(this).is(':checked')) {
                targetDiv.removeClass('noon');
            } else {
                targetDiv.addClass('noon');
            }
        });
    });
});
// thay ray keo qua lai
var sliders = document.querySelectorAll(".ftslide");
sliders.forEach(function (slider) {
  var output = document.getElementById("demo" + slider.dataset.index);
  output.innerHTML = slider.value;
  slider.oninput = function () {
    output.innerHTML = this.value;
  };
});
