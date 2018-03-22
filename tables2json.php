<?php

class Row
{
    public $data;
    function __construct($xpath, $node)
    {
        $data = Array();
        foreach(Array('th','td') as $query)
        {
            if (!$data)
                foreach ($xpath->query('.//'.$query, $node) as $cell)
                    $data[] = $cell->textContent;
        }
        ($data) && ($this->data = $data);
    }
}

class Table
{
    public $data;
    function __construct($xpath, $node)
    {
        $table = $xpath->query('.//tr', $node);
        $rows = Array();
        foreach ($table as $tr)
        {
            $row = new Row($xpath, $tr);
            if (isset($head))
            {
                if (isset($row->data))
                    for ($i = 0; $i < count($row->data); $i++)
                        $data[$head[$i]] = $row->data[$i];
                else
                    unset($data);

                ($data) && ($rows[] = $data);
            }
            else
                $head = $row->data;
        }
        ($rows) && ($this->data = $rows);
    }
}

function tables($html)
{
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
    $xpath = new DOMXpath($dom);
    $tables = Array();
    $counter = 0;
    foreach($xpath->query('//table') as $node)
    {
        $id = $node->getAttribute('id');
        $name = ($id) ? $id : 'table'.$counter;
        $table = new Table($xpath, $node);
        ($table) && ($tables[$name] = $table->data);
        $counter++;
    }
    return $tables;
}

$html = file_get_contents('tables.html');
$tables = tables($html);
echo json_encode($tables);