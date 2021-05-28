<?php
$totalPage = ($countAll>0)?ceil($countAll / $limit): $currentPage;
$range = 5;
if($totalPage >1){
echo "<div class='d-flex justify-content-center'>";
    echo "<ul class='pagination'>";

    // button for previous page
    $previous=$currentPage-1;
    echo "<li class='page-item ";
    echo ($currentPage<=1)? ' disabled': '';
    echo "'><a href='".$page_url."$GETpagename=$previous' title='Previous' class='page-link'>";
        echo "Previous";
    echo "</a></li>";
    
    $pagelinks=ceil($currentPage/$range)*$range;
    for ($x=($pagelinks-$range)+1; $x<=$pagelinks; $x++) {
        if ($x <= $totalPage) {
            // current page
            if ($x == $currentPage) {
                echo "<li class='active page-item'><a class='page-link' href='#'>$x </a></li>";
            }
            // not current page
            else {
                echo "<li class='page-item'><a class='page-link' href='{$page_url}$GETpagename=$x'>$x</a></li>";
            }
        }
    }

    // button for next page
        $next=$currentPage+1;
        echo "<li class='page-item ";
        echo ($currentPage==$totalPage)? ' disabled' : '';
        echo "'><a class='page-link' href='" .$page_url . "$GETpagename=$next' title='Next'>";
            echo "Next";
        echo "</a></li>";


    echo "</ul></div>";
}
?>