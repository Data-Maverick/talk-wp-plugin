<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Talk Test</title>
    </head>
    <body>
        <?php
        define( 'CORAL_PROJECT_TALK_DIR', dirname( __FILE__ ) );

        require(dirname(__FILE__) . "/vendor/autoload.php");

        use \Firebase\JWT\JWT;
        $key = "magikpassword";
        $talk_url = "http://localhost:3300";
        $token = array(
    		"jti" => uniqid(),
    		"exp" => time() + (7 * 24 * 60 * 60),
    		"iss" => $talk_url,
    		"aud" => "talk",
    		"sub" => "wordpress-12345",
            "name" => "Test User 12345",
            "email" => "test12345@blah.com",
            // "sub" => "9f11ec6c-6401-4f0a-9650-5a3beec02ecb"
    	);
    	$auth_token = JWT::encode($token, $key);
        ?>
        <h1>Comments</h1>
        <pre>
            <?php print_r($token); ?>
        </pre>
        <div class="" id="coral_talk"></div>
    	<script src="<?= $talk_url ?>/static/embed.js" async onload="
    		Coral.talkStream = Coral.Talk.render(document.getElementById('coral_talk'), {
    			talk: '<?= $talk_url ?>',
    			auth_token: '<?= $auth_token; ?>'
    		});
    	"></script>
    </body>
</html>
