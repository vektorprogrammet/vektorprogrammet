/**
 * @param {string} tableId
 * @returns {string}
 */
function getCSVFromTable(tableId){
    let csv = '';
    let arr = [];
    let skipIndexes = [];

    $('#'+tableId+' th').each(function(index){
        if($.trim($(this).text()) === ''){
            // Skip columns with empty headers
            skipIndexes.push(index);
        }
        else arr.push($.trim($(this).text()));
    });
    csv += arr.join(',');

    $('#'+tableId+' tr').each(function(){
        if($(this).children('th').length !== 0) return true; // Skip th row
        csv += '\n';
        arr = [];
        $(this).children('td').each(function(index){
            if(skipIndexes.indexOf(index) === -1) arr.push('"'+$.trim(this.innerText)+'"');
        });
        csv += arr.join(',');
    });

    return csv;
}

/**
 * @param {string} tableId
 */
function downloadCSV(tableId){
    const csv = getCSVFromTable(tableId);
    const encodedUri = encodeURI(csv);
    const link = document.createElement("a");
    link.style.visibility = 'hidden';
    link.setAttribute("href", "data:text/csv;charset=utf-8,\uFEFF" + encodedUri);
    link.setAttribute("download", tableId + ".csv");
    document.body.appendChild(link);

    link.click();
}
