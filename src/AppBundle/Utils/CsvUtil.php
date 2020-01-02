<?php

namespace AppBundle\Utils;

use Symfony\Component\HttpFoundation\Response;

class CsvUtil
{
    /**
     * Puts csv text into a response object with correct Content-Type header for csv
     *
     * @param string $csv_string the csv data as a string
     * @return Response a response object
     */
    public static function makeCsvResponse(string $csv_string):Response
    {
        $response = new Response($csv_string);
        $response->headers->set('Content-Type', 'text/csv');
        return $response;
    }

    /**
     * Makes a csv table with the columns specified in columnMap, and with data from
     * $row[$columnId] for $columnId=>$columnName in $columnMap for $row in $rows
     *
     * @param $columnMap - An map from question id to question text. The columns to be included in the csv output
     * @param $rows - The rows as an array of maps from columnName to value
     * @param $sep - The separator between items. Default: ';'
     * @return string - The finished csv string
     */
    public static function csvFromTable($columnMap, $rows, string $sep=';'):string
    {
        //Export the two-dimensional csv array to a csv string
        $content = "";
        foreach ($columnMap as $question) {
            $content .= CsvUtil::csvEscapeAndSeparate($question, $sep);
        }
        $content = CsvUtil::csvNewline($content);
        foreach ($rows as $csv_row) {
            foreach ($columnMap as $id => $qname) {
                if (isset($csv_row[$id])) {
                    $content .= CsvUtil::csvEscapeAndSeparate($csv_row[$id], $sep);
                } else {
                    $content .= CsvUtil::csvEscapeAndSeparate("", $sep);
                }
            }
            $content = CsvUtil::csvNewline($content);
        }

        return $content;
    }

    //Escapes the string, quotes it and adds a separator
    private static function csvEscapeAndSeparate(string $str, string $sep): string
    {
        $str = str_replace('"', '""', $str); //" is escaped with "" in csv
        return "\"$str\"$sep";
    }

    //Removes the last separator and replaces it with a csv newline, defined to be CRLF
    private static function csvNewline(string $csv): string
    {
        return substr($csv, 0, -1) . "\r\n";
    }
}
