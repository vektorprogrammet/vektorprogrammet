function validateBankAccountNumber(element){
    var field = element.getElementsByTagName('input')[0];
    var valid = true;

    // Check for invalid formatting
    // Spaces and periods are valid, but optional separators
    if (field.value.match(/^\d{4}((\.\d{2}\.)|(\s\d{2}\s)|\d{2})\d{5}$/)) {}
    else valid = false;

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
    if ((11 - remainder) != numbers[numbers.length - 1]) valid = false;

    if(valid) field.setCustomValidity('');
    else field.setCustomValidity('Ugyldig bankkontonummer');
}
