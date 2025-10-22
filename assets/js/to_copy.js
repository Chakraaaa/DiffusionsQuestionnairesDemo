var toCopy = document.getElementById('to-copy'),
        btnCopy = document.getElementById('copy');

btnCopy.addEventListener('click', function () {
    toCopy.select();
    document.execCommand('copy');
    toCopy.blur();
    return false;
});
