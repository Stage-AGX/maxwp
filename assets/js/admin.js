document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('your-button-id').addEventListener('click', function(e) {
        e.preventDefault();

        var data = new FormData();
        data.append('action', 'monplugin_action');
        data.append('nonce', monplugin_ajax_object.nonce);
        data.append('additional_data', 'some_value');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', monplugin_ajax_object.ajax_url, true);

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                var response = JSON.parse(xhr.responseText);
                alert('Réponse du serveur : ' + response.message);
            } else {
                alert('Erreur serveur : ' + xhr.status);
            }
        };

        xhr.onerror = function() {
            alert('Erreur de connexion.');
        };

        xhr.send(data);
    });
    var ColorPickers = document.querySelectorAll('.color-picker');
    colorPickers.forEach(function(colorPicker) {
        new wpColorPicker(colorPicker);
    });
});