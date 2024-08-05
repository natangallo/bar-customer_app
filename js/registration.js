document.getElementById('nameSearch').addEventListener('input', function() {
    var filter = this.value.toUpperCase();
    var options = document.getElementById('nameSelect').options;
    for (var i = 0; i < options.length; i++) {
        var txtValue = options[i].text;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            options[i].style.display = "";
        } else {
            options[i].style.display = "none";
        }
    }
});
