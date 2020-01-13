<?php
######PLEASE READ #######
# login.php script must be ran to initiate login and set the cookies in place so that this script may run correctly.
# from command line in current working directly type: php login.php after it appears that login is correctly done
#Session Cookie has been set you can run this file.

//page with the content I want to grab
$url="https://fossbytes.com/"; // Get the URL that shows the new leads that we need to process and put into our Sales system.

//Initiate the PHP Curl Function
$c = curl_init($url);

// Set the different Curl options and variables
curl_setopt($c, CURLOPT_COOKIEJAR, ‘cookiesale.txt’); //We set any cookies given to us
curl_setopt($c, CURLOPT_COOKIEFILE, ‘cookiesale.txt’);//We read the cookies that have been set.
//curl_setopt($c, CURLOPT_USERAGENT, ‘Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0’);
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($c, CURLOPT_HEADER, 1);
curl_setopt($c, CURLOPT_VERBOSE, true);

//Execute PHP Curl and the Options included Above
$page = curl_exec($c); //HTML Contents is stored in the $page variable, we will use this with SIMPLE HTML DOM to parse the HTML Elements.
#echo $page;
curl_close($c); // Close the Curl Session, so we can call new pages later
#### LOAD SIMPLE HTML DOM Class ############################
#Include the library of SIMPLE HTML DOM. HTML DOM Allows the ability to easily parse different HTML Elements, Classes Etc.
#We use it in this script to Find the Pagniation URL’s and to also parse out the table information that we want.

include(‘simple_html_dom.php’);
include(‘simple_html_dom_utility.php’);
$next = new simple_html_dom(); //$next variable is the object for the simple_html_dom class

// Get Page and Individual Leads

$next->load($page);//We get the current html source for the page from the $page variable captured by CURL.
foreach($next->find(‘a’) as $element){//Find all of the links in the HTML source
    if(is_numeric($element->innertext)){//Check to see if the Anchor Text of the link is strictly numeric, detecting if it has pagination e.g. 1 2 3 4 5

        $links[] = $element->href;

    }
}
#print_r($links); //Uncomment to see the output of links that it detected for Pagination.
### GRAB EACH NEW PAGE AND PROCESS THE LEADS ON EACH PAGE ##########
foreach($links as $link){//This will individually loop through each Pagination URL
    //   $url=’https://fossbytes.com/’.$link;// URL to initiate new CURL session to set cookies and get HTML to Parse.

    $c = curl_init($url);
    curl_setopt($c, CURLOPT_COOKIEJAR, ‘cookiesale.txt’);
    curl_setopt($c, CURLOPT_COOKIEFILE, ‘cookiesale.txt’);
//curl_setopt($c, CURLOPT_USERAGENT, ‘Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0’);//Setting the Broswer User Agent to look like Mozilla FireFox browser instead of it looking like CURL browser.
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_HEADER, 1);

//curl_setopt($ch, CURLOPT_VERBOSE, true);//Uncomment to set CURL output to VERBOSE.
    $page = curl_exec($c);//Execute Curl page and Code
    curl_close($c);//Close Curl Session

    define ("CUTTING_MARK", "href=\""); //escape da aspa "

    $html = new simple_html_dom();//Initate new simple_html_dom class to parse the $page html.
    $html->load($page);//simple_html_dom loads the html source returned by the CURL $page
    foreach($html->find(‘a’) as $element){//Foreach Item to iterate through all of the links that are pulled from $page source.
        if($element->innertext == ‘Lead’){//Only capture links that have Lead in the Anchor Text.
            $getlead = explode(CUTTING_MARK,$element->href);//Get the Lead ID after the = using explode

            if($getlead[1] < 249057){//Only Get leads with an ID higher than this number
                $leads[] = $element->href;
            }
        }
    }

#Get Table Details of Leads
    foreach($leads as $lead){

//page with the content I want to grab
        //  $url=’https://sales.examp.le/’.$lead;
#echo $url; // Print URL (uncomment to see the url)
        $c = curl_init($url);//Initiate new Curl session to the load the current lead page
        curl_setopt($c, CURLOPT_COOKIEJAR, ‘cookiesale.txt’);//Set the Cookie within Curl
        curl_setopt($c, CURLOPT_COOKIEFILE, ‘cookiesale.txt’);//Get the Cookie from Curl from Local file
//curl_setopt($c, CURLOPT_USERAGENT, ‘Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0’);//Set browser user agent to spoof a compatible FireFox Mozilla Browser.
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_HEADER, 1);
//curl_setopt($ch, CURLOPT_VERBOSE, true);

//End of Curl Options
        $page = curl_exec($c);//Execute curl and curl options
        curl_close($c);//Close the Curl Session

// Parse through the HTML table to find lead info. Lead info is located in the first HTML table.
        $html = new simple_html_dom();//Initate new simple_html_dom class
        $html->load($page);//Set the html_dom_class to $html object loading from the $page variable that has HTML source of the current $lead page.
        $table = $html->find(‘table’, 1);//Find the first table on the page.
        $line = null;
        foreach($table->find(‘tr’) as $row) {//Go through each <tr> to process the lead information
            $name = $row->find(‘td’,0)->innertext;//Grab the text located within the first <td>field e.g. <tr><td>Name
            $status = $row->find(‘td’,1)->innertext;//Grab the text located within the second <td>field e.g. <tr><td><td>Email
            $s = strip_tags($name);//Remove any uneccssary tags for $name
            $s .= strip_tags($status);//Remove any uneccssary tags for $status

            $line[] = $s;//Store table results of leads into $line array
        }

######### Store Lead Information into a CSV File ###############################
        $handle = fopen(“leads.csv”, “a”);//open leads.csv file and append to the end of the line
        fputcsv($handle, $line);//write to the file leads.csv
        fclose($handle);//Close the file session.
    }
}
?>