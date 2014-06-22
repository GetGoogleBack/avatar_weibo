<?php
session_start();
include_once 'config.php';
include_once 'sdk/api.php';
include_once 'getimg.php';
include_once 'water.php';

if (! (isset( $_SESSION ['token'] ) || (isset( $_POST ['username'] ) && isset( $_POST ['password'] )))) {
    ?>
<form method="post" action="index.php">
	<p>
		Username: <input type="text" name="username" />
	</p>
	<p>
		Password: <input type="password" name="password" />
	</p>
	<input type="submit" value="Submit" name="submit" />
</form>
<?php
} elseif (isset( $_SESSION ['token'] )) {
    if (isset( $_POST ['submit2'] )) {
        try {
            $w = new WeiboPHP( $_SESSION ['token'] );
            $uid = $w->HTTPGet( 'account/get_uid.json', array () )['uid'];
            $pic = $w->HTTPGet( 'users/show.json', array (
                    'uid' => $uid 
            ) )['avatar_large'];
            if (file_exists( $uid . '.jpg' )) {
                unlink( $uid . '.jpg' );
            }
            GrabImage( $pic, $uid . '.jpg' );
            Water( $uid );
            echo "<p>头像生成完毕，根据新浪微博要求，请自行下载头像并保存。<br/><img src=\"${uid}.jpg\"/></p>";
        } catch ( Exception $ex ) {
            echo $ex->getMessage();
        }
    } else {
        ?>
<form method="post" action="index.php">
	<input type="submit" value="Submit" name="submit2" />
</form>
<?php
    }
} elseif (isset( $_POST ['username'] ) && isset( $_POST ['password'] )) {
    $w = new WeiboPHP( $_POST ['username'], $_POST ['password'], AKEY, ASEC, RURL );
    $_SESSION ['token'] = $w->GetToken();
}
?>