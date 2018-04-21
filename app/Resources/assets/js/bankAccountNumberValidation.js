function validateBankAccountNumber(field){
    if (!field.getAttribute('required') && field.value === '') {
        return true;
    }
    // Check for invalid formatting
    // Spaces and periods are valid, but optional separators
    if (!field.value.match(/^\d{4}((\.\d{2}\.)|(\s\d{2}\s)|\d{2})\d{5}$/)) {
        field.setCustomValidity('Feil formatering. Eks: 12345678903,\n1234.56.78903,\n1234 56 78903');
        return false;
    }

    var cleanString = field.value.split('.').join('').split(' ').join('');

    var weightNumbers = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
    var numbers = cleanString.split('');

    // Take cross product of the numbers and weight numbers (except final control digit)
    var cross = 0;
    for (var i = 0; i < weightNumbers.length; ++i){
        cross += numbers[i] * weightNumbers[i];
    }

    // Invalid MOD11
    var remainder = cross % 11;
    if (((11 - remainder) % 11) !== numbers[numbers.length - 1]){
        field.setCustomValidity('Ugyldig bankkontonummer. Sjekk at alle sifrene er korrekte');
        return false;
    }

    field.setCustomValidity('');
    field.value = numbers.slice(0,4).join('') + '.' + numbers.slice(4,6).join('') + '.' + numbers.slice(6).join('');
    return true;
}
