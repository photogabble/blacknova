<?php
require_once('../backends/simpletest/unit_tester.php');
require_once('../backends/simpletest/reporter.php');
$test = &new GroupTest('All tests for Blacknova Traders');

// In this section, we will automatically loop over all test files, and require 
// in the files from the includes directory.
$test_files = getDirFiles("./");
for ($i=0; $i<count($test_files); $i++)
{
    if ($test_files[$i] != 'testall.php' && $test_files[$i] != 'HOWTO')
    {
        require_once('../includes/'.$test_files[$i]);
        $test->AddTestFile($test_files[$i]);
    }
}

// Run the tests!
$test->run(new HtmlReporter('utf-8'));

// Iterate over the directory to get the list of files.
function getdirfiles($dirPath)
{
    if ($handle = opendir($dirPath))
    {
        while (false !== ($file = readdir($handle)))
        {
            if ($file != "." && $file != "..")
            {
                $filesArr[] = trim($file);
            }
        }
        closedir($handle);
    }
    sort($filesArr);
    return $filesArr;
}
?>
