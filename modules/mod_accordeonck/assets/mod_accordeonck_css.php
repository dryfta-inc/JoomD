<?php
header('content-type: text/css');
$id = 'ul#'.htmlspecialchars ( $_GET['cssid'] , ENT_QUOTES );
?>

<?php echo $id; ?> {
    padding:0px;
    margin: 0;
}

<?php echo $id; ?> li {
     margin: 0;
    text-align: left;
    list-style: none;
 }

<?php echo $id; ?> li ul li {
    list-style-type : square;
 }

<?php echo $id; ?> > li {
 }

<?php echo $id; ?> li a {
    margin: 0;
       text-decoration: none;
}

<?php echo $id; ?> li a:hover, <?php echo $id; ?> ul li a:focus {
 
}
