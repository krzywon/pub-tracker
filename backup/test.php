<?php
for ( $i = 0; $i < 5; $i++ )
{
    for ( $j = 0; $j < 4; $j++ )
    {
        for ( $k = 0; $k < 3; $k++ )
        {
            for ( $m = 0; $m < 2; $m++ )
            {
                foo($i, $j, $k, $m);
            }
        }
    }
}

function foo($i, $j, $k, $m) {
	print "<p>(i,j,k,m) = ($i,$j,$k,$m)</p>\n";
}
?> 